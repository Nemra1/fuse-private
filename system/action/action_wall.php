<?php

require __DIR__ . "./../config_session.php";

if (!useWall()) {
    exit;
}
if (isset($_POST["offset"]) && isset($_POST["load_more"]) && isset($_POST["load_more_wall"])) {
    echo usermorewall();
    exit;
}
if (isset($_POST["post_to_wall"]) && isset($_POST["post_file"])) {
    echo userpostwall();
    exit;
}
if (isset($_POST["like"]) && isset($_POST["like_type"])) {
    echo userpostlike();
    exit;
}
if (isset($_POST["delete_reply"])) {
    echo deletereply();
    exit;
}
if (isset($_POST["content"]) && isset($_POST["reply_to_wall"])) {
    echo userwallreply();
    exit;
}
if (isset($_POST["id"]) && isset($_POST["load_comment"])) {
    echo userloadcomment();
    exit;
}
if (isset($_POST["current"]) && isset($_POST["id"]) && isset($_POST["load_reply"])) {
    echo userloadreply();
    exit;
}
if (isset($_POST["delete_wall_post"])) {
    echo userdeletewall();
    exit;
}
if (isset($_POST["view_likes"])) {
    echo viewWallLikes();
    exit;
}
function wallReplyCount($id){
    global $mysqli,$data,$lang;
    $get_count = $mysqli->query("SELECT count(reply_id) as total FROM boom_post_reply WHERE parent_id = '" . $id . "'");
    $t = $get_count->fetch_assoc();
    return $t["total"];
}

function userMoreWall(){
    global $mysqli,$data,$lang;
    $of = escape($_POST["offset"]);
    $wall_content = "";
    $find_friend = $mysqli->query("SELECT target FROM boom_friends WHERE hunter = '" . $data["user_id"] . "' AND fstatus = '3'");
    $friend_array = [$data["user_id"]];
    if (0 < $find_friend->num_rows) {
        while ($add_friend = $find_friend->fetch_assoc()) {
            array_push($friend_array, $add_friend["target"]);
        }
    }
    $newarray = implode(", ", $friend_array);
    $wall_post = $mysqli->query("\r\n\t\tSELECT boom_post.*, boom_users.*,\r\n\t\t(SELECT count( parent_id ) FROM boom_post_reply WHERE parent_id = boom_post.post_id ) as reply_count,\r\n\t\t(SELECT like_type FROM boom_post_like WHERE uid = '" . $data["user_id"] . "' AND like_post = boom_post.post_id) as liked\r\n\t\tFROM  boom_post, boom_users \r\n\t\tWHERE boom_post.post_user = boom_users.user_id AND boom_post.post_user IN (" . $newarray . ")\r\n\t\tORDER BY boom_post.post_actual DESC LIMIT 10 OFFSET " . $of . "\r\n\t");
    if (0 < $wall_post->num_rows) {
        while ($wall = $wall_post->fetch_assoc()) {
            $wall_content .= boomTemplate("element/wall_post", $wall);
        }
    } else {
        $wall_content .= 0;
    }
    return $wall_content;
}

function userPostWall(){
    global $mysqli,$data,$lang;
    if (!boomAllow(1)) {
        return "";
    }
    if (checkFlood()) {
        return "";
    }

    $content = clearBreak($_POST["post_to_wall"]);
    $content = escape($content);
    $post_file = escape($_POST["post_file"]);
    $file_content = "";
    $file_ok = 0;
    if (muted()) {
        return 0;
    }
    $content = wordFilter($content);
    if (empty($content) && empty($post_file)) {
        return 0;
    }
    if ($post_file != "") {
        $get_file = $mysqli->query("SELECT * FROM boom_upload WHERE file_key = '" . $post_file . "' AND file_user = '" . $data["user_id"] . "' AND file_complete = '0'");
        if (0 < $get_file->num_rows) {
            $file = $get_file->fetch_assoc();
            $file_content = "/upload/wall/" . $file["file_name"];
            $file_ok = 1;
        } else {
            if ($content == "") {
                return 0;
            }
        }
    }
    if (strlen($content) < 2000) {
        $mysqli->query("INSERT INTO boom_post (post_date, post_user, post_content, post_file, post_actual) VALUES ('" . time() . "', '" . $data["user_id"] . "', '" . $content . "', '" . $file_content . "', '" . time() . "' )");
        $postid = $mysqli->insert_id;
        if ($file_ok == 1) {
            $mysqli->query("UPDATE boom_upload SET file_complete = '1', relative_post = '" . $postid . "' WHERE file_key = '" . $post_file . "' AND file_user = '" . $data["user_id"] . "'");
        }
        $list = getFriendList($data["user_id"]);
        
        boomListNotify($list, "add_post", ["hunter" => $data["user_id"], "source" => "post", "sourceid" => $postid]);
	//runtime user exp
    	if(useLevel()){
    	    $mysqli->query("UPDATE boom_users SET user_exp = user_exp + 1 WHERE user_id = '{$data['user_id']}'");
    	    userExpLevel("exp_post");
    	}
        
        return showPost($postid);
    }
    return 2;
}

function userPostLike(){
    global $mysqli,$data,$lang;
    $id = escape($_POST["like"]);
    $type = escape($_POST["like_type"]);
    if (!boomAllow(1)) {
        return "";
    }

    if (!canPostAction($id)) {
        return boomCode(0);
    }
    $like_result = $mysqli->query("SELECT post_user, (SELECT like_type FROM boom_post_like WHERE like_post = '" . $id . "' AND uid = '" . $data["user_id"] . "') AS type FROM boom_post WHERE post_id = '" . $id . "'");
    if (0 < $like_result->num_rows) {
        $like = $like_result->fetch_assoc();
        $mysqli->query("DELETE FROM boom_post_like WHERE like_post = '" . $id . "' AND uid = '" . $data["user_id"] . "'");
        $mysqli->query("DELETE FROM boom_notification WHERE notifier = '" . $data["user_id"] . "' AND notify_id = '" . $id . "' AND notify_type = 'like'");
        if ($like["type"] == $type) {
            updateNotify($like["post_user"]);
            return boomCode(1, ["data" => getLikes($id, 0, "wall")]);
        }
        $mysqli->query("INSERT INTO boom_post_like ( uid, liked_uid, like_type, like_post, like_date) VALUE ('" . $data["user_id"] . "', '" . $like["post_user"] . "', " . $type . ", '" . $id . "', '" . time() . "')");
        if (!mySelf($like["post_user"])) {
            boomNotify("like", ["hunter" => $data["user_id"], "target" => $like["post_user"], "source" => "post", "sourceid" => $id, "custom" => $type]);
        }
        return boomCode(1, ["data" => getLikes($id, $type, "wall")]);
    }
    return boomCode(0);
}

function userWallReply(){
    global $mysqli,$data,$lang;
    $content = escape($_POST["content"]);
    $reply_to = escape($_POST["reply_to_wall"]);
    if (checkFlood()) {
        return "";
    }
    if (!boomAllow(1)) {
        return "";
    }
    if (muted()) {
        return boomCode(0);
    }
    $content = wordFilter($content);
    if (1001 <= strlen($content)) {
        return boomCode(0);
    }
    if (!canPostAction($reply_to)) {
        return boomCode(0);
    }
    $check_valid = $mysqli->query("SELECT * FROM boom_post WHERE post_id = '" . $reply_to . "'");
    if ($check_valid->num_rows < 1) {
        return boomCode(0);
    }
    $get_id = $check_valid->fetch_assoc();
    $id = $get_id["post_id"];
    $who = $get_id["post_user"];
    $mysqli->query("INSERT INTO boom_post_reply (parent_id, reply_uid, reply_date, reply_user, reply_content) VALUES ('" . $id . "', '" . $who . "', '" . time() . "', '" . $data["user_id"] . "', '" . $content . "')");
    $last_id = $mysqli->insert_id;
    $mysqli->query("UPDATE boom_post SET post_actual = '" . time() . "' WHERE post_id = '" . $id . "'");
    if (!mySelf($who)) {
        boomNotify("reply", ["hunter" => $data["user_id"], "target" => $who, "source" => "post", "sourceid" => $reply_to, "custom" => $last_id]);
    }
    $get_back = $mysqli->query("\r\n\t\tSELECT boom_post_reply.*, boom_users.* \r\n\t\tFROM boom_post_reply, boom_users\r\n\t\tWHERE boom_post_reply.parent_id = '" . $reply_to . "' AND boom_post_reply.reply_user = '" . $data["user_id"] . "' AND boom_users.user_id = '" . $data["user_id"] . "' \r\n\t\tORDER BY reply_id DESC LIMIT 1\r\n\t");
    if ($get_back->num_rows < 1) {
        return boomCode(0);
    }
    $reply = $get_back->fetch_assoc();
    $log = boomTemplate("element/reply", $reply);
    $total = wallreplycount($reply_to);
    return boomCode(1, ["data" => $log, "total" => $total]);
}

function userLoadComment(){
    global $mysqli,$data,$lang;

    $id = escape($_POST["id"]);
    if (!boomAllow(1)) {
        return "";
    }
    if (!canPostAction($id)) {
        return boomCode(0, ["reply" => 0, "more" => ""]);
    }
    $load_reply = "";
    $reply_count = 0;
    $find_reply = $mysqli->query("SELECT boom_post_reply.*, boom_users.*, (SELECT count(reply_id) FROM boom_post_reply WHERE parent_id = '" . $id . "' ) as reply_count FROM  boom_post_reply, boom_users  WHERE boom_post_reply.parent_id = '" . $id . "' AND boom_post_reply.reply_user = boom_users.user_id ORDER BY boom_post_reply.reply_id DESC LIMIT 10 ");
    if (0 < $find_reply->num_rows) {
        while ($reply = $find_reply->fetch_assoc()) {
            $load_reply .= boomTemplate("element/reply", $reply);
            $reply_count = $reply["reply_count"];
        }
    }
    if (10 < $reply_count) {
        $more = "<a onclick=\"moreComment(this," . $id . ")\" class=\"theme_color text_small more_comment\">" . $lang["view_more_comment"] . "</a>";
    } else {
        $more = 0;
    }
    return boomCode(1, ["reply" => $load_reply, "more" => $more]);
}

function userLoadReply(){
    global $mysqli,$data,$lang;
    $id = escape($_POST["id"]);
    $offset = escape($_POST["current"]);
    if (!boomAllow(1)) {
        return "";
    }
    if (!canPostAction($id)) {
        return 99;
    }
    $reply_comment = "";
    $find_reply = $mysqli->query("SELECT boom_post_reply.*, boom_users.* FROM  boom_post_reply, boom_users WHERE boom_post_reply.parent_id = '" . $id . "' AND boom_post_reply.reply_id < '" . $offset . "' AND boom_post_reply.reply_user = boom_users.user_id \r\n\tORDER BY boom_post_reply.reply_id DESC LIMIT 20");
    if (0 < $find_reply->num_rows) {
        while ($reply = $find_reply->fetch_assoc()) {
            $reply_comment .= boomTemplate("element/reply", $reply);
        }
    } else {
        $reply_comment = 0;
    }
    return $reply_comment;
}

function deleteReply(){
    global $mysqli,$data,$lang;
    $reply_id = escape($_POST["delete_reply"]);
    $reply_info = $mysqli->query("\r\n\tSELECT boom_post_reply.*, boom_users.* \r\n\tFROM boom_post_reply, boom_users \r\n\tWHERE boom_post_reply.reply_id = '" . $reply_id . "' AND boom_users.user_id = boom_post_reply.reply_user\r\n\t");
    if ($reply_info->num_rows == 1) {
        $reply = $reply_info->fetch_assoc();
        if (canDeleteWallReply($reply)) {
            $mysqli->query("DELETE FROM boom_post_reply WHERE reply_id = '" . $reply_id . "'");
            $mysqli->query("DELETE FROM boom_notification WHERE notifier = '" . $reply["reply_user"] . "' AND notify_id = '" . $reply["parent_id"] . "' AND notify_custom = '" . $reply_id . "'");
            updateNotify($reply["reply_uid"]);
            if (!mySelf($reply["user_id"])) {
                boomConsole("cwall_delete", ["hunter" => $data["user_id"], "target" => $reply["user_id"]]);
            }
            $total = wallreplycount($reply["parent_id"]);
            return boomCode(1, ["wall" => $reply["parent_id"], "reply" => $reply_id, "total" => $total]);
        }
        return boomCode(0);
    }
    return boomCode(0);
}

function userDeleteWall(){
    global $mysqli,$data,$lang;
    $post = escape($_POST["delete_wall_post"]);
    $valid = $mysqli->query("\r\n\tSELECT boom_post.*, boom_users.* \r\n\tFROM boom_post, boom_users \r\n\tWHERE boom_post.post_id = '" . $post . "' AND boom_users.user_id = boom_post.post_user\r\n\t");
    if (0 < $valid->num_rows) {
        $wall = $valid->fetch_assoc();
        if (!canDeleteWall($wall)) {
            return 1;
        }
        $mysqli->query("DELETE FROM boom_post WHERE post_id = '" . $post . "'");
        $mysqli->query("DELETE FROM boom_post_reply WHERE parent_id = '" . $post . "'");
        $mysqli->query("DELETE FROM boom_notification WHERE notify_id = '" . $post . "' AND notify_source = 'post'");
        $mysqli->query("DELETE FROM boom_post_like WHERE like_post = '" . $post . "'");
        $mysqli->query("DELETE FROM boom_report WHERE report_post = '" . $post . "' AND report_type = 2");
        if (0 < $mysqli->affected_rows) {
            updateStaffNotify();
        }
        removeRelatedFile($post, "wall");
        $list = getFriendList($wall["user_id"], 1);
        updateListNotify($list);
        if (!mySelf($wall["user_id"])) {
            boomConsole("wall_delete", ["hunter" => $data["user_id"], "target" => $wall["user_id"]]);
        }
        return "boom_post" . $post;
    }
    return 1;
}

?>