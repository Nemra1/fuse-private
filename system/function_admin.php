<?php
function createInfo($v){
	return '<i class="ri-apps-2-add-line theme_color infopop" data="' . $v . '"></i>';
}
function adminRoomList($mysqli, $lang){
    $list_rooms = '';
    // Use prepared statement to improve security
    $stmt = $mysqli->prepare("SELECT boom_rooms.* FROM boom_rooms ORDER BY pinned DESC, room_name ASC");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($room = $result->fetch_assoc()) {
                // Assuming boomTemplate is a function that safely generates HTML output
                $list_rooms .= boomTemplate('element/admin_room', $room);
            }
        } else {
            $list_rooms .= emptyZone($lang['empty']);
        }
        $stmt->close();
    } else {
        // Error handling for SQL query failure
        $list_rooms .= '<p>' . $lang['error'] . '</p>';
    }
    return $list_rooms;
}
/* bot system*/
function bot_adminRoomList() {
    global $db, $data ,$lang;
    $rooms = [];  // Initialize an empty array for rooms
    try {
        // Attempt to get rooms from the database
        $rooms = $db->get('rooms');  // Assuming this fetches all the rooms

        // Check if any rooms are found
        if ($db->count == 0) {
            // If no rooms are found, return an empty array
            return [];
        }
    } catch (Exception $e) {
        // In case of a database error, return an empty array
        return [];
    }

    return $rooms;
}

 
function getUpdateList($setting) {
    global $mysqli, $data, $lang;
    $update_list = '';
    $avail_update = 0;
    $dir = glob(BOOM_PATH . '/updates/*', GLOB_ONLYDIR);
    if ($dir === false) {
        // Handle the case where the glob function fails
        return '<div>' . $lang['error_fetching_updates'] . '</div>';
    }
    foreach ($dir as $dirnew) {
        // Get the update version from the directory name
        $update = str_replace(BOOM_PATH . '/updates/', '', $dirnew);
        // Validate that the update is a numeric version and greater than the current version
        if (is_numeric($update) && $update > $setting['version']) {
            $avail_update++;
            // Safely append the update to the list
            $update_list .= boomTemplate('element/update_element', ['update' => $update]);
        }
    }
    // Return the update list or a message if no updates are available
    if ($avail_update > 0) {
        return '<div>' . $update_list . '</div>';
    } else {
        return emptyZone($lang['no_update']);
    }
}

function adminAddonsList() {
	global $mysqli, $data, $lang;
    $addons_list = '';
    $avail_update = 0;
    // Get list of directories in the /addons folder
    $dir = glob(BOOM_PATH . '/addons/*', GLOB_ONLYDIR);
    if ($dir === false) {
        // Return an error message if the glob fails
        return emptyZone($lang['error_fetching_addons']);
    }
    foreach ($dir as $dirnew) {
        $install = 0;
        // Sanitize the addon name to avoid potential issues
        $addon = basename($dirnew); // Extracts the directory name safely
        // Check if the addon has an install script
        if (file_exists(BOOM_PATH . '/addons/' . $addon . '/system/install.php')) {
            $avail_update++;
            // Use prepared statement to check if the addon exists in the database
            $stmt = $mysqli->prepare("SELECT * FROM boom_addons WHERE addons = ?");
            $stmt->bind_param("s", $addon);
            $stmt->execute();
            $checkaddons = $stmt->get_result();
            if ($checkaddons->num_rows > 0) {
                // Add the addon to the uninstall list
                $addons = $checkaddons->fetch_assoc();
                $addons_list .= boomTemplate('element/addons_uninstall', $addons);
            } else {
                // Add the addon to the install list
                $addons_list .= boomTemplate('element/addons_install', ['addon' => $addon]);
            }
        }
    }
    // Return the appropriate response depending on whether any addons were found
    if ($avail_update > 0) {
        return $addons_list;
    } else {
        return emptyZone($lang['no_addons']);
    }
}

function getDashboard() {
    global $mysqli, $data, $lang;
    $delay = getDelay();
    $current_time = time(); // Get the current time to use in the query
    
    // Initialize the query parts
    $query_part_1 = "SELECT
                        (SELECT count(user_id) FROM boom_users) AS user_count,
                        (SELECT count(user_id) FROM boom_users WHERE last_action >= ?) AS online_count,
                        (SELECT count(user_id) FROM boom_users WHERE user_sex = 2) AS female_count,
                        (SELECT count(user_id) FROM boom_users WHERE user_sex = 1) AS male_count,
                        (SELECT count(id) FROM boom_private) AS private_count,
                        (SELECT count(post_id) FROM boom_chat) AS chat_count,
                        (SELECT count(post_id) FROM boom_post) AS post_count,
                        (SELECT count(reply_id) FROM boom_post_reply) AS reply_count,
                        (SELECT count(user_id) FROM boom_users WHERE user_ghost > ?) AS ghosted_users,
                        (SELECT count(user_id) FROM boom_users WHERE user_banned > 0) AS banned_users,
                        (SELECT count(user_id) FROM boom_users WHERE user_mute > ?) AS muted_users,
                        (SELECT count(user_id) FROM boom_users WHERE user_kick > ?) AS kicked_users";
    
    if (boomAllow(90)) {
        $query_part_2 = ", (SELECT count(user_id) FROM boom_users WHERE user_sex = 1) AS male_count";
        $stmt = $mysqli->prepare($query_part_1 . $query_part_2);
    } else {
        $stmt = $mysqli->prepare($query_part_1);
    }

    // Bind the parameters
    $stmt->bind_param("iiiii", $delay, $current_time, $current_time, $current_time, $current_time);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    $dashboard = $result->fetch_assoc();

    // Return the result
    return $dashboard;
}

function listLogin() {
    global $setting, $lang;
    $login_list = '';
    // Ensure the directory exists before proceeding
    $loginDir = BOOM_PATH . '/control/login/';
    if (!is_dir($loginDir)) {
        return ''; // Return an empty string if the directory doesn't exist
    }
    // Get all directories under the login directory
    $dir = glob($loginDir . '*' , GLOB_ONLYDIR);
    foreach ($dir as $dirnew) {
        // Extract the folder name and sanitize it
        $login = basename($dirnew); // Using basename ensures no path traversal
        // Check if the login.php file exists
        if (file_exists($dirnew . '/login.php')) {
            // Ensure $login is sanitized for safe HTML output
            $login = htmlspecialchars($login, ENT_QUOTES, 'UTF-8');            
            // Generate option HTML with current selection
            $login_list .= '<option ' . selCurrent($setting['login_page'], $login) . ' value="' . $login . '">' . $login . '</option>';
        }
    }
    return $login_list;
}

function listDj() {
    global $mysqli, $lang;
    $list_members = '';
    // Use a prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM boom_users WHERE user_dj = 1 ORDER BY user_onair DESC, user_name ASC");
    if ($stmt === false) {
        // Error handling if the query preparation fails
        die('Error preparing the SQL query: ' . $mysqli->error);
    }
    $stmt->execute();
    $getmembers = $stmt->get_result();
    if ($getmembers->num_rows > 0) {
        // Loop through the members and add them to the list
        while ($members = $getmembers->fetch_assoc()) {
            // Use the boomTemplate function to generate the HTML for each member
            $list_members .= boomTemplate('element/admin_dj', $members);
        }
    } else {
        // If no members are found, display an empty message
        $list_members .= emptyZone($lang['empty']);
    }
    return $list_members;
}

function listContact() {
    global $mysqli, $lang, $data;
    $contact_list = '';
    // Use a prepared statement to prevent potential issues and ensure flexibility in the future
    $stmt = $mysqli->prepare("SELECT * FROM boom_contact ORDER BY cdate ASC");
    if ($stmt === false) {
        // Error handling if the query preparation fails
        die('Error preparing the SQL query: ' . $mysqli->error);
    }
    $stmt->execute();
    $get_contact = $stmt->get_result();
    // Check if any contact entries exist in the database
    if ($get_contact->num_rows > 0) {
        // Loop through each contact entry and generate the list
        while ($contact = $get_contact->fetch_assoc()) {
            // Use the boomTemplate function to generate the HTML for each contact entry
            $contact_list .= boomTemplate('element/admin_contact', $contact);
        }
    } else {
        // If no contacts are found, display an empty message
        $contact_list .= emptyZone($lang['empty']);
    }
    return $contact_list;
}

function listFilter($type) {
    global $data, $mysqli, $lang;
    $list_word = '';
    // Prepare the SQL query to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM boom_filter WHERE word_type = ? ORDER BY word ASC");
    if ($stmt === false) {
        // Error handling if the query preparation fails
        die('Error preparing the SQL query: ' . $mysqli->error);
    }
    // Bind the parameter to the prepared statement
    $stmt->bind_param("s", $type); // "s" means the parameter is a string
    // Execute the prepared statement
    $stmt->execute();
    // Get the result of the query
    $getword = $stmt->get_result();
    // Check if any words were retrieved from the database
    if ($getword->num_rows > 0) {
        // Loop through each word and generate the list
        while ($word = $getword->fetch_assoc()) {
            $list_word .= boomTemplate('element/word', $word);
        }
    } else {
        // If no words are found, display an empty message
        $list_word .= emptyZone($lang['empty']);
    }

    return $list_word;
}

function listAdminIp() {
    global $mysqli, $lang;
    $list_ip = '';
    // Prepare the SQL query to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM boom_banned ORDER BY ip ASC");
    if ($stmt === false) {
        // Error handling if the query preparation fails
        die('Error preparing the SQL query: ' . $mysqli->error);
    }
    // Execute the prepared statement
    $stmt->execute();
    // Get the result of the query
    $getip = $stmt->get_result();
    // Check if any banned IPs were retrieved from the database
    if ($getip->num_rows > 0) {
        // Loop through each banned IP and generate the list
        while ($ip = $getip->fetch_assoc()) {
            $list_ip .= boomTemplate('element/admin_ip', $ip);
        }
    } else {
        // If no banned IPs are found, display an empty message
        $list_ip .= emptyZone($lang['empty']);
    }
    return $list_ip;
}

function listLastMembers() {
    global $mysqli, $lang;
    $list_members = '';
    // Prepare the SQL query to avoid SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM boom_users WHERE user_rank != 0 AND user_bot = 0 ORDER BY user_join DESC LIMIT 50");
    if ($stmt === false) {
        // Error handling if the query preparation fails
        die('Error preparing the SQL query: ' . $mysqli->error);
    }
    // Execute the prepared statement
    $stmt->execute();
    // Get the result of the query
    $getmembers = $stmt->get_result();
    // Check if any members were retrieved from the database
    if ($getmembers->num_rows > 0) {
        // Loop through each member and generate the list
        while ($members = $getmembers->fetch_assoc()) {
            $list_members .= boomTemplate('element/admin_user', $members);
        }
    } else {
        // If no members are found, display an empty message
        $list_members .= emptyZone($lang['empty']);
    }
    return $list_members;
}

function listStreamPlayer() {
    global $mysqli, $setting, $lang;
    $stream_list = '';
    // Prepare the SQL query to avoid SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM boom_radio_stream ORDER BY stream_alias ASC");
    if ($stmt === false) {
        // Error handling if the query preparation fails
        die('Error preparing the SQL query: ' . $mysqli->error);
    }
    // Execute the prepared statement
    $stmt->execute();
    // Get the result of the query
    $getstream = $stmt->get_result();
    // Check if any streams were retrieved from the database
    if ($getstream->num_rows > 0) {
        // Loop through each stream and generate the list
        while ($stream = $getstream->fetch_assoc()) {
            // Set the default status for the current stream
            $stream['default'] = '';
            if ($stream['id'] == $setting['player_id']) {
                // If the stream matches the player_id from settings, mark it as default
                $stream['default'] = '<div class="sub_list_selected"><i class="fa fa-circle success"></i></div>';
            }
            // Append the stream template to the list
            $stream_list .= boomTemplate('element/stream_player', $stream);
        }
    } else {
        // If no streams are found, display an empty zone message
        $stream_list .= emptyZone($lang['empty']);
    }
    return $stream_list;
}

function listAdminGift(){
    global $mysqli, $data, $lang;
    $list = '';
    // Prepare the SQL query to avoid SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM boom_gift WHERE id > 0 ORDER BY id DESC");
    if ($stmt === false) {
        // Error handling if the query preparation fails
        die('Error preparing the SQL query: ' . $mysqli->error);
    }
    // Execute the prepared statement
    $stmt->execute();
    // Get the result of the query
    $get_gift = $stmt->get_result();
    // Check if any gifts were retrieved from the database
    if ($get_gift->num_rows > 0) {
        // Loop through each gift and generate the list
        while ($gift = $get_gift->fetch_assoc()) {
            // Append the gift template to the list
            $list .= boomTemplate('element/admin_gift', $gift);
        }
    }
    return $list;
}

function onlineMap(){
    global $db,$data;
    // Define the active time threshold (e.g., 1 minute)
    $active_threshold = time() - 60; // 60 seconds for demonstration; adjust as needed
    // Define the array to hold user details
    $usersDetails = [];
    // Prepare the query to select user_name and user_ip of users who were active recently
    $db->where('last_active', $active_threshold, '>=');
    $users = $db->get('users', null, 'user_name, user_ip, user_tumb');
    
    if($db->count > 0){
        foreach ($users as $user){
            $usersDetails[] = [
                'user_name' => $user['user_name'],
                'user_ip' => $user['user_ip'],
                'user_tumb' => $data['domain'].'/avatar/'.$user['user_tumb'],
            ];
        }
    }
    
    return $usersDetails;
}
function listAdminCall(){
	global $mysqli, $data, $lang;
	$get_call = $mysqli->query("SELECT * FROM boom_call WHERE call_status > 0 AND call_active > 0 ORDER BY call_time DESC LIMIT 100");
	$list = '';
	if($get_call->num_rows > 0){
		while($call = $get_call->fetch_assoc()){
			$list .= boomTemplate('element/admin_call', $call);
		}
	}
	else {
		$list = emptyZone($lang['empty']);
	}
	return $list;
}
?>