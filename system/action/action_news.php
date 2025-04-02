<?php

require __DIR__ . "./../config_session.php";

if (isset($_POST["add_news"]) && isset($_POST["post_file"])) {
    echo postsystemnews();
    exit;
}
if (isset($_POST["like_news"]) && isset($_POST["like_type"])) {
    echo newslike();
    exit;
}
if (isset($_POST["more_news"])) {
    echo morenews();
    exit;
}
if (isset($_POST["id"]) && isset($_POST["load_news_comment"])) {
    echo loadnewscomment();
    exit;
}
if (isset($_POST["content"]) && isset($_POST["reply_news"])) {
    echo newsreply();
    exit;
}
if (isset($_POST["current"]) && isset($_POST["id"]) && isset($_POST["load_news_reply"])) {
    echo morenewscomment();
    exit;
}
if (isset($_POST["delete_news_reply"])) {
    echo deletenewsreply();
    exit;
}
if (isset($_POST["remove_news"])) {
    echo deletenews();
    exit;
}
function newsReplyCount($id) {
    global $mysqli, $data;
    // Sanitize the input to avoid SQL injection
    $id = escape($id); 
    // Prepare the SQL query with placeholders
    $query = "SELECT count(reply_id) as total FROM boom_news_reply WHERE parent_id = ?";
    // Prepare the statement
    if ($stmt = $mysqli->prepare($query)) {
        // Bind the parameter to the query
        $stmt->bind_param("i", $id);
        // Execute the query
        $stmt->execute();
        // Get the result
        $result = $stmt->get_result();
        // Fetch the result
        if ($row = $result->fetch_assoc()) {
            $stmt->close();  // Close the statement after fetching the result
            return $row["total"];
        } else {
            // Handle the case when no result is returned
            $stmt->close();
            return 0;  // Return 0 if no replies exist
        }
    } else {
        // Log an error if the statement preparation fails
        error_log("Error preparing query: " . $mysqli->error);
        return 0;
    }
}


function moreNews() {
    global $mysqli, $data;
    // Sanitize input to prevent SQL injection
    $news = escape($_POST["more_news"]);
    $news_content = "";
    // Prepare the query to fetch the news
    $query = "
        SELECT 
            boom_news.*, 
            boom_users.*, 
            (SELECT COUNT(id) FROM boom_news) AS news_count,
            (SELECT COUNT(parent_id) FROM boom_news_reply WHERE parent_id = boom_news.id) AS reply_count,
            (SELECT like_type FROM boom_news_like WHERE uid = ? AND like_post = boom_news.id) AS liked
        FROM 
            boom_news
        INNER JOIN 
            boom_users 
        ON 
            boom_news.news_poster = boom_users.user_id
        WHERE 
            boom_news.id < ?
        ORDER BY 
            boom_news.news_date DESC 
        LIMIT 10
    ";

    // Prepare the statement
    if ($stmt = $mysqli->prepare($query)) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("ii", $data["user_id"], $news);
        // Execute the query
        $stmt->execute();
        // Get the result
        $result = $stmt->get_result();
        // Fetch the results
        if ($result->num_rows > 0) {
            while ($news_item = $result->fetch_assoc()) {
                $news_content .= boomTemplate("element/news", $news_item);
            }
        } else {
            // No news found
            $news_content .= 0;
        }
        // Close the statement
        $stmt->close();
    } else {
        // Log an error if the statement preparation fails
        error_log("Error preparing query: " . $mysqli->error);
        return 0;
    }
    return $news_content;
}


function newsReply() {
    global $mysqli, $data, $cody;
    // Sanitize the content and reply_to inputs
    $content = escape($_POST["content"]);
    $reply_to = escape($_POST["reply_news"]);

    // Check if the user is allowed to reply to news
    if (!boomAllow($cody["can_reply_news"])) {
        return "";
    }
    // Sanitize target and fetch user details
    $target = escape($_POST['content'] && $_POST['reply_news']);
    $user = userDetails($target);
    if (empty($user)) {
        die();
    }
    // Prevent flooding
    if (checkFlood()) {
        echo 100;
        die();
    }
    // Check if the user is muted
    if (muted() || isRoomMuted($data)) {
        die();
    }
    // Apply word filter to the content
    $content = wordFilter($content);

    // Check if the content length exceeds the limit
    if (1001 <= strlen($content)) {
        return boomCode(0);
    }

    // Check if the news post exists
    $check_valid = $mysqli->prepare("SELECT * FROM boom_news WHERE id = ?");
    $check_valid->bind_param("i", $reply_to);
    $check_valid->execute();
    $result = $check_valid->get_result();

    if ($result->num_rows < 1) {
        return boomCode(0);
    }

    $news = $result->fetch_assoc();

    // Insert the reply into the database
    $insert_reply = $mysqli->prepare("
        INSERT INTO boom_news_reply (parent_id, reply_date, reply_user, reply_content, reply_uid) 
        VALUES (?, ?, ?, ?, ?)
    ");
	// Get the current time and news poster ID
	$current_time = time();
	$news_poster = $news["news_poster"];
	
	// Bind the parameters after assigning them to variables
	$insert_reply->bind_param("iiiss", $news["id"], $current_time, $data["user_id"], $content, $news_poster);
	$insert_reply->execute();
    $last_id = $mysqli->insert_id;

    // Fetch the inserted reply
    $get_back = $mysqli->prepare("
        SELECT boom_news_reply.*, boom_users.* 
        FROM boom_news_reply
        INNER JOIN boom_users ON boom_news_reply.reply_user = boom_users.user_id
        WHERE boom_news_reply.parent_id = ? AND boom_news_reply.reply_user = ? 
        ORDER BY boom_news_reply.reply_id DESC LIMIT 1
    ");
    $get_back->bind_param("ii", $reply_to, $user_id);  // Pass the variable by reference
    $get_back->execute();
    $result = $get_back->get_result();

    if ($result->num_rows < 1) {
        return boomCode(0);
    }

    $reply = $result->fetch_assoc();
    $log = boomTemplate("element/news_reply", $reply);

    // Get the total number of replies
    $total = newsReplyCount($reply_to);

    // Return success with the reply data and total
    return boomCode(1, ["data" => $log, "total" => $total]);
}


function loadNewsComment(){
    global $mysqli,$data,$lang;    
    // Escape input to prevent SQL injection
    $id = escape($_POST["id"]);
    $load_reply = "";
    $reply_count = 0;
    
    // Get the total reply count for this news post
    $get_reply_count = $mysqli->query("
        SELECT COUNT(reply_id) AS reply_count
        FROM boom_news_reply
        WHERE parent_id = '$id'
    ");
    if ($get_reply_count->num_rows > 0) {
        $reply_count_result = $get_reply_count->fetch_assoc();
        $reply_count = $reply_count_result["reply_count"];
    }

    // Fetch the latest 10 replies
    $find_reply = $mysqli->query("
        SELECT 
            boom_news_reply.*, 
            boom_users.* 
        FROM 
            boom_news_reply
        INNER JOIN 
            boom_users 
        ON 
            boom_news_reply.reply_user = boom_users.user_id
        WHERE 
            boom_news_reply.parent_id = '$id'
        ORDER BY 
            boom_news_reply.reply_id DESC 
        LIMIT 10
    ");

    if ($find_reply->num_rows > 0) {
        while ($reply = $find_reply->fetch_assoc()) {
            $load_reply .= boomTemplate("element/news_reply", $reply);
        }
    }

    // Check if there are more comments to load
    if ($reply_count > 10) {
        $more = "<a onclick=\"moreNewsComment(this, $id)\" class=\"theme_color text_small more_comment\">" . $lang["view_more_comment"] . "</a>";
    } else {
        $more = 0;
    }

    return boomCode(1, ["reply" => $load_reply, "more" => $more]);
}


function moreNewsComment(){
 global $mysqli,$data,$lang;        
    // Escape the input to prevent SQL injection
    $id = escape($_POST["id"]);
    $offset = escape($_POST["current"]);
    $reply_comment = "";

    // Use proper JOIN syntax and parameterized queries
    $query = "
        SELECT boom_news_reply.*, boom_users.* 
        FROM boom_news_reply
        INNER JOIN boom_users 
        ON boom_news_reply.reply_user = boom_users.user_id
        WHERE boom_news_reply.parent_id = ? 
        AND boom_news_reply.reply_id < ?
        ORDER BY boom_news_reply.reply_id DESC
        LIMIT 20
    ";

    // Prepare the query
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ii", $id, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if we have any replies and format them
    if ($result->num_rows > 0) {
        while ($reply = $result->fetch_assoc()) {
            $reply_comment .= boomTemplate("element/news_reply", $reply);
        }
    } else {
        $reply_comment = 0; // No more comments to load
    }

    return $reply_comment;
}


function deleteNewsReply(){
    global $mysqli, $data, $lang, $cody;
    // Escape the input to prevent SQL injection
    $reply_id = escape($_POST["delete_news_reply"]);
    // Prepare the query with INNER JOIN and parameters
    $query = "
        SELECT boom_news_reply.*, boom_users.* 
        FROM boom_news_reply
        INNER JOIN boom_users 
        ON boom_news_reply.reply_user = boom_users.user_id
        WHERE boom_news_reply.reply_id = ?
    ";
    // Prepare the statement
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $reply_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $reply = $result->fetch_assoc();
        // Check if the user has permission to delete the reply
        if (!canDeleteNewsReply($reply)) {
            return boomCode(0); // No permission
        }
        // Delete the reply
        $delete_query = "DELETE FROM boom_news_reply WHERE reply_id = ?";
        $delete_stmt = $mysqli->prepare($delete_query);
        $delete_stmt->bind_param("i", $reply_id);
        $delete_stmt->execute();
        // Get the total number of replies for the parent post
        $total = newsreplycount($reply["parent_id"]);
        // Return success with the updated reply count
        return boomCode(1, ["news" => $reply["parent_id"], "reply" => $reply_id, "total" => $total]);
    }
    // Return failure if the reply does not exist
    return boomCode(0);
}

function postSystemNews() {
    global $mysqli, $data, $lang, $cody;
    $news = clearBreak($_POST["add_news"]);
    $news = escape($news);
    $post_file = escape($_POST["post_file"]);
    $news_file = "";
    $file_ok = 0;
    // Check if user is muted
    if (muted()) {
        return 0;
    }
    // Check if user has permission to post news
    if (!canPostNews()) {
        return 0;
    }
    // Trim the content
    $news = trimContent($news);
    // Check if there is content to post (either news text or file)
    if (empty($news) && empty($post_file)) {
        return 0;
    }
    // Handle file attachment
    if ($post_file != "") {
        // Use prepared statement to prevent SQL injection
        $get_file_stmt = $mysqli->prepare("
            SELECT * FROM boom_upload 
            WHERE file_key = ? 
            AND file_user = ? 
            AND file_complete = '0'
        ");
        $get_file_stmt->bind_param("si", $post_file, $data["user_id"]);
        $get_file_stmt->execute();
        $get_file_result = $get_file_stmt->get_result();
        if ($get_file_result->num_rows > 0) {
            $file = $get_file_result->fetch_assoc();
            $news_file = "/upload/news/" . $file["file_name"];
            $file_ok = 1;
        } else {
            // If file doesn't exist, check if there's no news content
            if (empty($news)) {
                return 0;
            }
        }
    }
    // Update the user's last news post timestamp
    $update_user_stmt = $mysqli->prepare("UPDATE boom_users SET user_news = ? WHERE user_id = ?");
    $update_user_stmt->bind_param("ii", time(), $data["user_id"]);
    $update_user_stmt->execute();
    // Insert the news into the database
    $insert_news_stmt = $mysqli->prepare("
        INSERT INTO boom_news (news_poster, news_message, news_file, news_date) 
        VALUES (?, ?, ?, ?)
    ");
    $insert_news_stmt->bind_param("issi", $data["user_id"], $news, $news_file, time());
    $insert_news_stmt->execute();
    $news_id = $mysqli->insert_id;
    // Update the file completion status if a file was attached
    if ($file_ok == 1) {
        $update_file_stmt = $mysqli->prepare("
            UPDATE boom_upload 
            SET file_complete = '1', relative_post = ? 
            WHERE file_key = ? AND file_user = ?
        ");
        $update_file_stmt->bind_param("isi", $news_id, $post_file, $data["user_id"]);
        $update_file_stmt->execute();
    }
    // Notify all users
    updateAllNotify();
    // Return the news post display
    return showNews($news_id);
}


function deleteNews() {
    global $mysqli, $data, $lang, $cody;

    $news = escape($_POST["remove_news"]);
    // Use prepared statement for selecting the news to delete
    $valid_stmt = $mysqli->prepare("
        SELECT boom_news.*, boom_users.* 
        FROM boom_news 
        INNER JOIN boom_users 
        ON boom_news.news_poster = boom_users.user_id 
        WHERE boom_news.id = ?
    ");
    $valid_stmt->bind_param("i", $news);
    $valid_stmt->execute();
    $valid_result = $valid_stmt->get_result();
    if ($valid_result->num_rows > 0) {
        $tnews = $valid_result->fetch_assoc();
        // Check if the user has permission to delete the news
        if (!canDeleteNews($tnews)) {
            return 1;
        }
        // Start transaction to delete all associated records
        $mysqli->begin_transaction();
        try {
            // Delete related news
            $delete_news_stmt = $mysqli->prepare("DELETE FROM boom_news WHERE id = ?");
            $delete_news_stmt->bind_param("i", $news);
            $delete_news_stmt->execute();

            // Delete related replies
            $delete_replies_stmt = $mysqli->prepare("DELETE FROM boom_news_reply WHERE parent_id = ?");
            $delete_replies_stmt->bind_param("i", $news);
            $delete_replies_stmt->execute();

            // Delete related likes
            $delete_likes_stmt = $mysqli->prepare("DELETE FROM boom_news_like WHERE like_post = ?");
            $delete_likes_stmt->bind_param("i", $news);
            $delete_likes_stmt->execute();

            // Remove related files
            removeRelatedFile($news, "news");

            // Commit transaction
            $mysqli->commit();

            // Notify all users
            updateAllNotify();

            // Log the deletion if not deleting your own news
            if (!mySelf($tnews["user_id"])) {
                boomConsole("news_delete", ["hunter" => $data["user_id"], "target" => $tnews["user_id"]]);
            }

            // Return confirmation
            return "boom_news" . $news;

        } catch (Exception $e) {
            // Rollback transaction if any error occurs
            $mysqli->rollback();
            return 1;
        }
    }

    // Return 1 if news is not found or not deletable
    return 1;
}


function newsLike() {
    global $mysqli, $data, $lang, $cody;

    if (!boomAllow(1)) {
        return "";
    }

    // Escape input parameters
    $id = escape($_POST["like_news"]);
    $type = escape($_POST["like_type"]);

    // Use a prepared statement to prevent SQL injection
    $like_stmt = $mysqli->prepare("
        SELECT news_poster, 
               (SELECT like_type 
                FROM boom_news_like 
                WHERE like_post = ? AND uid = ?) AS type 
        FROM boom_news 
        WHERE id = ?
    ");
    $like_stmt->bind_param("iii", $id, $data["user_id"], $id);
    $like_stmt->execute();
    $like_result = $like_stmt->get_result();

    if ($like_result->num_rows > 0) {
        $like = $like_result->fetch_assoc();

        // Delete the existing like (if any)
        $delete_like_stmt = $mysqli->prepare("DELETE FROM boom_news_like WHERE like_post = ? AND uid = ?");
        $delete_like_stmt->bind_param("ii", $id, $data["user_id"]);
        $delete_like_stmt->execute();

        // If the user is trying to like with the same type, do nothing
        if ($like["type"] == $type) {
            return boomCode(1, ["data" => getLikes($id, 0, "news")]);
        }

        // Insert the new like
        $insert_like_stmt = $mysqli->prepare("
            INSERT INTO boom_news_like (uid, liked_uid, like_type, like_post, like_date) 
            VALUES (?, ?, ?, ?, ?)
        ");
		// Store the result of time() in a variable
		$current_time = time();
        $insert_like_stmt->bind_param("iiisi", $data["user_id"], $like["news_poster"], $type, $id, $current_time);
        $insert_like_stmt->execute();

        return boomCode(1, ["data" => getLikes($id, $type, "news")]);
    }

    return boomCode(0);
}

?>