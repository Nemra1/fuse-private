<?php
/**
* Codychat
*
* @package Codychat
* @author www.boomcoding.com
* @copyright 2020
* @terms any use of this script without a legal license is prohibited
* all the content of Codychat is the propriety of BoomCoding and Cannot be 
* used for another project.
*/

function fu_json_results($input) {
    header("Content-type: application/json");
    echo json_encode($input);
    exit();
}

function cl_update_user_data($user_id = null, $res = array()) {
    global $db, $data;
    // Check if user_id is a valid number and $res is a non-empty array
    if (!is_numeric($user_id) || empty($res) || !is_array($res)) {
        return false;
    }
    // Set the condition for the update query
    $db->where('user_id', $user_id);
    // Execute the update query
    $update = $db->update("users", $res);
    // Return true if the update was successful, false otherwise
    return ($update) ? true : false;
}

function getIp(){
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $cloud =   @$_SERVER["HTTP_CF_CONNECTING_IP"];
    $remote  = $_SERVER['REMOTE_ADDR'];
    if(filter_var($cloud, FILTER_VALIDATE_IP)) {
        $ip = $cloud;
    }
    else if(filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP)){
        $ip = $forward;
    }
    else{
        $ip = $remote;
    }
    return escape($ip);
}
function boomTemplate($getpage, $boom = '') {
	global $data, $lang, $mysqli, $cody;
    $page = BOOM_PATH . '/system/' . $getpage . '.php';
    $structure = '';
    ob_start();
    require($page);
    $structure = ob_get_contents();
    ob_end_clean();
    return $structure;
}
function addons_boomTemplate($getpage, $boom = '') {
	global $data, $lang, $mysqli, $cody;
    $page = BOOM_PATH . '/addons/' . $getpage . '.php';
    $structure = '';
    ob_start();
    require($page);
    $structure = ob_get_contents();
    ob_end_clean();
    return $structure;
}
function not_num(&$var) {
    return (empty($var) || is_numeric($var) != true || $var < 1) ? true : false;
}
function calHour($h){
	return time() - ($h * 3600);
}
function calWeek($w){
	return time() - ( 3600 * 24 * 7 * $w);
}
function calmonth($m){
	return time() - ( 3600 * 24 * 30 * $m);
}
function calDay($d){
	return time() - ($d * 86400);
}
function calSecond($sec){
	return time() - $sec;
}
function calMinutes($min){
	return time() - ($min * 60);
}
function calHourUp($h){
	return time() + ($h * 3600);
}
function calWeekUp($w){
	return time() + ( 3600 * 24 * 7 * $w);
}
function calmonthUp($m){
	return time() + ( 3600 * 24 * 30 * $m);
}
function calDayUp($d){
	return time() + ($d * 86400);
}
function calMinutesUp($min){
	return time() + ($min * 60);
}
function calSecondUp($sec){
	return time() + $sec;
}
function boomActive($feature){
	if($feature <= 100){
		return true;
	}
}

function canGift(){
	global $data;
	if(boomAllow($data['can_gift'])){
		return true;
	}
}
function boomFormat($txt){
	$count = substr_count($txt, "\n" );
	if($count > 20){
		return $txt;
	}
	else {
		return nl2br($txt);
	}
}
function boomFileVersion(){
	global $data;
	if($data['bbfv'] > 1.0){
		return '?v=' . $data['bbfv'];
	}
	return '';
}
function boomNull($val){
	if(is_null($val)){
		return 0;
	}
	else {
		return $val;
	}
}
function boomCacheUpdate(){
	global $mysqli;
	$mysqli->query("UPDATE boom_setting SET bbfv = bbfv + 0.01 WHERE id > 0");
}
function embedMode(){
	global $data;
	if(isset($_GET['embed'])){
		return true;
	}
}
function embedCode(){
	global $data;
	if(isset($_GET['embed'])){
		return 1;
	}
	else {
		return 0;
	}
}
function myColor($u){
	return $u['user_color'];
}
function myColorFont($u){
	return $u['user_color'] . ' ' . $u['user_font'];
}
function myTextColor($u){
	return $u['bccolor'] . ' ' . $u['bcbold'] . ' ' . $u['bcfont'];
}
function myAvatar($a){
	global $data;
	$path =  '/avatar/';
	if(defaultAvatar($a)){
		$path =  '/default_images/avatar/';
	}
	return $data['domain'] . $path . $a;
}
function imgLoader(){
	return 'default_images/misc/holder.png';
}
function defaultAvatar($a) {
    if (!is_string($a) || trim($a) === '') {
        return false; // Return false if $a is null, empty, or not a string
    }
    return stripos($a, 'default') !== false;
}

function myCover($a){
	global $data;
	return $data['domain'] . '/cover/' . $a;
}
function getCover($user){
	global $data;
	if(userHaveCover($user)){
		return 'style="background-image: url(' . myCover($user['user_cover']) . ');"';
	}
}
function coverClass($user){
	global $data;
	if(userHaveCover($user)){
		return 'cover_size';
	}
}
function userHaveCover($user){
	global $data;
	if($user['user_cover'] != ''){
		return true;
	}
}
function getIcon($icon, $c){
	global $data, $lang;
	return '<img class="' . $c . '" src="' . $data['domain'] . '/default_images/icons/' . $icon . boomFileVersion() . '"/>';
}
function boomCode($code, $custom = array()){
	$def = array('code'=> $code);
	$res = array_merge($def, $custom);
	return json_encode( $res, JSON_UNESCAPED_UNICODE);
}
function profileAvatar($a){
	global $data;
	$path =  '/avatar/';
	if(defaultAvatar($a)){
		$path =  '/default_images/avatar/';
	}
	return 'href="' . $data['domain'] . $path  . $a . '" src="' . $data['domain'] . $path  . $a . '"';
}
function boomUserTheme($user){
	global $data;
	if($user['user_theme'] == 'system'){
		return $data['default_theme'];
	}
	else {
		return $user['user_theme'];
	}
}
function linkAvatar($a){
	if(preg_match('@^https?://@i', $a)){
		return true;
	}
}
// Function to sanitize output for XSS protection
function sanitizeOutput($output) {
	return htmlspecialchars($output ?? '', ENT_QUOTES, 'UTF-8');
    //return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}
function cleanString($string) {
    return $string = preg_replace("/&#?[a-z0-9]+;/i", "", $string);
}
function cl_rn_strip($text = ""){
    // Trim leading and trailing whitespace
    $text = trim($text);
    // Replace line breaks with a single space
    $text = str_ireplace(["\r\n", "\n\r", "\r", "\n"], " ", $text);
    // Replace encoded ampersands with actual ampersands
    $text = str_replace('&amp;#', '&#', $text);
    // Remove parentheses
    $text = str_replace(['(', ')'], '', $text);
    return $text;
}
function escape($t){
	global $mysqli;
	//$t= sanitizeChatInput($t);
	return $mysqli->real_escape_string(trim(htmlspecialchars($t, ENT_QUOTES)));
}
function sanitizeChatInput($input) {
    // Strip all HTML/JavaScript tags
    $clean = strip_tags($input);
    // Convert special characters to HTML entities
    $clean = htmlspecialchars($clean, ENT_QUOTES, 'UTF-8');
    // Remove unwanted characters
    $clean = preg_replace('/[^\w\s,.!?@#\-]/', '', $clean);
    // Limit message length
    return substr($clean, 0, 500);
}
// For any user-generated content display:
function safeDisplay($content) {
    echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
}

function boomSanitize($t){
	global $mysqli;
	$t = str_replace(array('\\', '/', '.', '<', '>', '%', '#'), '', $t);
	return $mysqli->real_escape_string(trim(htmlspecialchars($t, ENT_QUOTES)));
}
function softEscape($t){
	global $mysqli;
	$atags = '<a><p><h1><h2><h3><h4><img><b><strong><br><ul><li><div><i><span><u><th><td><tr><table><strike><small><ol><hr><font><center><blink><marquee>';
	$t = strip_tags($t, $atags);
	return $mysqli->real_escape_string(trim($t));
}
function systemReplace($text){
	global $lang;
	$text = str_replace('%bcquit%', $lang['leave_message'], $text);
	$text = str_replace('%bcjoin%', $lang['join_message'], $text);
	$text = str_replace('%bcclear%', $lang['clear_message'], $text);
	$text = str_replace('%spam%', $lang['spam_content'], $text);
	$text = str_replace('%bcname%', $lang['name_message'], $text);
	$text = str_replace('%bckick%', $lang['kick_message'], $text);
	$text = str_replace('%bcban%', $lang['ban_message'], $text);
	$text = str_replace('%bcmute%', $lang['mute_message'], $text);
	$text = str_replace('%bcblock%', $lang['block_message'], $text);
	return $text;
}
function textReplace($text){
	global $data, $lang;
	$text = str_replace('%user%', $data['user_name'], $text);
	return $text;
}
function systemSpecial($content, $type, $custom = array()){
	global $data, $lang;
	$def = array(
		'content'=> $content,
		'type'=> $type,
		'delete'=> 1,
		'title'=> $lang['default_title'],
		'icon'=> 'default.svg',
	);
	$template = array_merge($def, $custom);
	return boomTemplate('element/system_log', $template);
}
function specialLogIcon($icon){
	global $data;
	return $data['domain'] . '/default_images/special/' . $icon . boomFileVersion();
}
function userDetails($user_id) {
    global $db;
    // Check if user_id is a valid number
    if (!is_numeric($user_id)) {
        return false;
    }
    // Add where clause to filter by user_id
    $db->where('user_id', $user_id);
    // Get user data
    $user_data = $db->getOne('users');
    // Check if user data was returned
    if (empty($user_data)) {
        return false;
    }
    return $user_data;    
}
function userNameDetails($name){
    global $mysqli;
    $user = [];
    // Use prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT user_id, user_name, user_email, user_avatar FROM boom_users WHERE user_name = ?");
    $stmt->bind_param('s', $name); // Bind the parameter to avoid SQL injection
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
    }
    $stmt->close(); // Always close the prepared statement
    return $user;
}

function ownAvatar($i){
	global $data;
	if($i == $data['user_id']){
		return 'glob_av';
	}
	return '';
}
function getUserAge($age){
	global $lang;
	return $age . ' ' . $lang['years_old'];
}
function delExpired($d){
	if($d < calSecond(12)){
		return true;
	}
}
function chatAction($room){
    global $mysqli, $data;
    // Validate room to ensure it is a valid numeric value
    if(!is_numeric($room) || $room <= 0){
        return false; // Invalid room ID
    }
    // Prepare the SQL query to prevent SQL injection
    $stmt = $mysqli->prepare("UPDATE boom_rooms SET rcaction = rcaction + 1, room_action = ? WHERE room_id = ?");
    if ($stmt === false) {
        return false; // Error in preparing statement
    }
    $time = time(); // Get the current timestamp
    // Bind parameters and execute the statement
    $stmt->bind_param('si', $time, $room);
    $stmt->execute();
    // Check for errors in execution
    if ($stmt->affected_rows > 0) {
        $stmt->close(); // Close the statement
        return true; // Successful update
    } else {
        $stmt->close(); // Close the statement
        return false; // No rows affected or error occurred
    }
}

function chatLevel($v){
	global $data;
}
function userPostChat($content, $custom = array()){
    global $mysqli, $data;
    $ghosted = 0;
    // Default values
    $def = array(
        'hunter'=> $data['user_id'],
        'room'=> $data['user_roomid'],
        'color'=> escape(myTextColor($data)),
        'type'=> 'public__message',
        'rank'=> 999,
        'snum'=> '',
    );
    // Merge custom data with defaults
    $c = array_merge($def, $data, $custom);
    // Check if the user is ghosted
    if(isGhosted($data)){
        $ghosted = 1;
    }
    // Handle runtime user experience points
    if(useLevel()){
        $mysqli->query("UPDATE boom_users SET user_exp = user_exp + 1 WHERE user_id = '{$data['user_id']}'");
        userExpLevel("exp_chat");
        getMyGift($content);
    }
    // Prepare the SQL query using prepared statements
    $stmt = $mysqli->prepare("INSERT INTO `boom_chat` (post_date, user_id, post_message, post_roomid, type, log_rank, snum, tcolor, pghost) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        return false; // Error preparing the query
    }
    $post_date = time();
    $stmt->bind_param('iisissssi', $post_date, $c['hunter'], $content, $c['room'], $c['type'], $c['rank'], $c['snum'], $c['color'], $ghosted);
    // Execute the query
    if (!$stmt->execute()) {
        $stmt->close();
        return false; // Error executing the query
    }
    // Get the last inserted ID
    $last_id = $mysqli->insert_id;
    // Update room action (chatAction)
    chatAction($data['user_roomid']);
    // Check if snum is not empty and create a log
    if (!empty($c['snum'])){
        $user_post = array(
            'post_id'=> $last_id,
            'type'=> $c['type'],
            'post_date'=> $post_date,
            'tcolor'=> $c['color'],
            'log_rank'=> $c['rank'],
            'post_message'=> $content,
        );
        // Merge custom data with user_post data
        $post = array_merge($c, $user_post);        
        // Create log if post data exists
        if(!empty($post)){
            return createLog($data, $post);
        }
    }
    $stmt->close();
    return true; // Successful execution
}

function userPostChatFile($content, $file_name, $type, $custom = array()){
    global $mysqli, $data;
    
    // Default custom values
    $def = array(
        'type' => 'public__message',
        'file2' => '', // Optional additional file
    );
    $c = array_merge($def, $custom);

    // Sanitize input variables
    $content = $content; 
    $file_name = escape($file_name);
    $type = escape($type);

    // Prepare chat message insert
    $stmt = $mysqli->prepare("INSERT INTO `boom_chat` (post_date, user_id, post_message, post_roomid, type, file) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        return false; // Error preparing the query
    }

    $time_now = time();
    $file_placeholder = 1; // Assuming file exists, this can be set dynamically

    // Assign values to variables first
    $user_id = $data['user_id'];
    $user_roomid = $data['user_roomid'];
    $message_type = $c['type'];

    $stmt->bind_param("iisssi", $time_now, $user_id, $content, $user_roomid, $message_type, $file_placeholder);
    
    if (!$stmt->execute()) {
        $stmt->close();
        return false; // Error executing the query
    }

    $rel = $stmt->insert_id;
    $stmt->close();

    // Perform chat action (notify about new message)
    chatAction($user_roomid);

    // Handle file upload insert
    $stmt_upload = $mysqli->prepare("INSERT INTO `boom_upload` (file_name, date_sent, file_user, file_zone, file_type, relative_post) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt_upload === false) {
        return false; // Error preparing the file insert query
    }

    // Assign values to variables before binding
    $file_zone = 'chat';
    $file2 = $c['file2'];

    if (!empty($file2)) {
        $stmt_upload->bind_param("siissi", $file_name, $time_now, $user_id, $file_zone, $type, $rel);
    } else {
        $stmt_upload->bind_param("siissi", $file_name, $time_now, $user_id, $file_zone, $type, $rel);
    }

    if (!$stmt_upload->execute()) {
        $stmt_upload->close();
        return false; // Error executing the query
    }

    $stmt_upload->close();
    return true; // Success
}

function systemPostChat($room, $content, $custom = array()){
    global $mysqli, $data;
    $def = array(
        'type' => 'system',
        'color' => 'chat_system',
        'rank' => 999,
    );
    $post = array_merge($def, $custom);
    // Ensure values are sanitized and safe
    $room = (int) $room;
    $content = trim($content);
    $type = htmlspecialchars($post['type'], ENT_QUOTES, 'UTF-8');
    $color = htmlspecialchars($post['color'], ENT_QUOTES, 'UTF-8');
    $rank = (int) $post['rank'];
    $system_id = (int) $data['system_id'];
    $post_date = time();
    // Use a prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("INSERT INTO boom_chat (post_date, user_id, post_message, post_roomid, type, log_rank, tcolor) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssis", $post_date, $system_id, $content, $room, $type, $rank, $color);
    $stmt->execute();
    $stmt->close();
    chatAction($room);
    return true;
}

function botPostChat($id, $room, $content, $custom = array()){
    global $mysqli, $data;
	// Ensure proper MySQL connection character set for UTF-8
	mysqli_set_charset($mysqli, 'utf8mb4');
    $def = array(
        'type' => 'public__message',
        'color' => '',
        'rank' => 999,
    );
    $post = array_merge($def, $custom);
    // Ensure values are sanitized and safe
    $id = (int) $id;
    $room = (int) $room;
    $content = trim($content);
    $type = htmlspecialchars($post['type'], ENT_QUOTES, 'UTF-8');
    $color = htmlspecialchars($post['color'], ENT_QUOTES, 'UTF-8');
    $rank = (int) $post['rank'];
    $post_date = time();
    // Use a prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("INSERT INTO boom_chat (post_date, user_id, post_message, post_roomid, type, log_rank, tcolor) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssis", $post_date, $id, $content, $room, $type, $rank, $color);
    $stmt->execute();
    $stmt->close();
    chatAction($room);
    return true;
}

function updateLastActive($user_id){
    global $mysqli;
    // Get the current timestamp
    $current_time = time();
    // Prepare the update query
    $stmt = $mysqli->prepare("UPDATE boom_users SET last_active = ? WHERE user_id = ?");
    if ($stmt === false) {
        // Error preparing the statement
        return false;
    }
    // Bind parameters and execute the query
    $stmt->bind_param("ii", $current_time, $user_id);
    if (!$stmt->execute()) {
        // Error executing the query
        $stmt->close();
        return false;
    }
    // Close the statement after execution
    $stmt->close();
    return true;  // Success
}

function postPrivate($from, $to, $content, $snum = ''){
    global $mysqli, $data;
    // Ensure $from and $to are integers
    $from = intval($from);
    $to = intval($to);
    // Sanitize content to prevent XSS
    $content = trim($content); 
    //$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); // Prevent XSS
    // Validate inputs
    if ($from <= 0 || $to <= 0 || empty($content)) {
        return false; // Invalid input
    }
    $time = time();
    // Use prepared statement to insert private message securely
    $stmt = $mysqli->prepare("INSERT INTO boom_private (time, target, hunter, message) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        return false; // Prepare failed
    }
    $stmt->bind_param("iiis", $time, $to, $from, $content);
    if (!$stmt->execute()) {
        $stmt->close();
        return false; // Execute failed
    }
    $last_id = $stmt->insert_id;
    $stmt->close();
    // Update recipient's message count securely
    if ($to !== $from) {
        $stmt = $mysqli->prepare("UPDATE boom_users SET pcount = pcount + 1 WHERE user_id = ?");
        if ($stmt === false) {
            return false; // Prepare failed
        }
        $stmt->bind_param("i", $to);
        if (!$stmt->execute()) {
            $stmt->close();
            return false; // Execute failed
        }
        $stmt->close();
        // Handle OneSignal notification if enabled
        if ($data['allow_onesignal'] == '1') {
            $rec_data = userDetails($to);
            $from_data = userDetails($from);
            $last_active = intval($rec_data['last_active']);
            $current_time = time();
            $inactive_time = 60; // 1 minute threshold
            if (($current_time - $last_active) > $inactive_time) {
				if($data['allow_onesignal']==1){
					$notification_msg = 'You have a message from 🧐 ' . htmlspecialchars($from_data['user_name'], ENT_QUOTES, 'UTF-8');
					sendNotification($rec_data['push_id'], $notification_msg);
				}
            }
        }
    }
    // Handle private log if a serial number is provided
    if (!empty($snum)) {
        $user_post = array(
            'id' => $last_id,
            'time' => $time,
            'message' => $content,
            'hunter' => $from,
        );
        $post = array_merge($data, $user_post);
        if (!empty($post)) {
            return privateLog($post, $post['user_id']);
        }
    }

    return true;
}
function sendNotification($userId, $message) {
    global $data, $cody;
    $content = array(
        "en" => $message
    );
    $fields = array(
        'app_id' => $data['onesignal_web_push_id'], // Replace with your OneSignal App ID
        'include_player_ids' => array($userId), // OneSignal user ID
        'isChrome' => false,
        'android_led_color' => 'FF0000FF',
        'priority' => 10,
        'contents' => $content
    );
    $headers = array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '. $data['onesignal_web_reset_key'] // Replace with your OneSignal REST API Key
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Enable SSL certificate verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    // Optionally specify the CA certificates file path if needed:
    // curl_setopt($ch, CURLOPT_CAINFO, '/path/to/cacert.pem');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function sendNotificationToAll($message) {
    global $data,$cody;
    $appId = $data['onesignal_web_push_id']; // OneSignal App ID
    $restApiKey = $data['onesignal_web_reset_key']; // OneSignal REST API Key
    $content = array(
        "en" => $message
    );
    $fields = array(
        'app_id' => $appId,
        'contents' => $content,
        'included_segments' => array('All') // Target all subscribers
    );
    $headers = array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $restApiKey
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // For production, set to true and remove verification disable.
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    // Error handling for cURL request
    if (curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch)); // Log cURL error
        $result = false; // Set result as false on failure
    }
    curl_close($ch);
    // Log or handle the result if needed
    if ($result === false) {
        error_log('Notification sending failed.');
    }
    return $result;
}

function getAllSubscribers($appId, $restApiKey) {
    global $data, $cody;
    $url = "https://onesignal.com/api/v1/players";
    $limit = 300; // Number of subscribers per request
    $offset = 0; // Pagination offset
    $allSubscribers = [];
    $hasMore = true;
    while ($hasMore) {
        $headers = [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $restApiKey
        ];
        $params = [
            'app_id' => $appId,
            'limit' => $limit,
            'offset' => $offset
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Enable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);  // Verify SSL certificate
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    // Verify host against certificate
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === false) {
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }
        $response = json_decode($result, true);
        if (isset($response['players'])) {
            $allSubscribers = array_merge($allSubscribers, $response['players']);
        }
        // Check if there are more subscribers to fetch
        $hasMore = isset($response['next_page']) && $response['next_page'];
        $offset += $limit;
    }
    return $allSubscribers;
}


function postPrivateContent($from, $to, $content) {
    global $mysqli, $data;
    // Ensure user IDs are integers
    $from = intval($from);
    $to = intval($to);
    // Sanitize content to prevent XSS
    $content = htmlspecialchars(trim($content), ENT_QUOTES, 'UTF-8');
    // Get current timestamp
    $time = time();
    // Use prepared statement for inserting message
    $stmt = $mysqli->prepare("INSERT INTO boom_private (time, target, hunter, message, file) VALUES (?, ?, ?, ?, ?)");
    $file_flag = 1; // Assuming 1 represents a file presence, modify as needed
    $stmt->bind_param("iiisi", $time, $to, $from, $content, $file_flag);
    $stmt->execute();
    $rel = $stmt->insert_id;
    $stmt->close();
    // Update message count for both users using a prepared statement
    $stmt = $mysqli->prepare("UPDATE boom_users SET pcount = pcount + 1 WHERE user_id IN (?, ?)");
    $stmt->bind_param("ii", $from, $to);
    $stmt->execute();
    $stmt->close();  
    return true;
}

function userPostPrivateFile($content, $target, $file_name, $type) {
    global $mysqli, $data;
    // Ensure target user ID is an integer
    $target = intval($target);
    $user_id = intval($data['user_id']);
    // Sanitize input to prevent XSS
    //$content = htmlspecialchars(trim($content), ENT_QUOTES, 'UTF-8');
    $content = $content;
    $file_name = htmlspecialchars(trim($file_name), ENT_QUOTES, 'UTF-8');
    $type = htmlspecialchars(trim($type), ENT_QUOTES, 'UTF-8');
    // Get current timestamp
    $time = time();
    $file_flag = 1; // Assuming 1 represents a file presence, modify as needed
    // Insert private message using prepared statement
    $stmt = $mysqli->prepare("INSERT INTO boom_private (time, target, hunter, message, file) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisi", $time, $target, $user_id, $content, $file_flag);
    $stmt->execute();
    $rel = $stmt->insert_id;
    $stmt->close();
    // Update pcount for both users
    $stmt = $mysqli->prepare("UPDATE boom_users SET pcount = pcount + 1 WHERE user_id IN (?, ?)");
    $stmt->bind_param("ii", $user_id, $target);
    $stmt->execute();
    $stmt->close();
    // Insert file upload record using prepared statement
    $stmt = $mysqli->prepare("INSERT INTO boom_upload (file_name, date_sent, file_user, file_zone, file_type, relative_post) VALUES (?, ?, ?, 'private', ?, ?)");
    $stmt->bind_param("siisi", $file_name, $time, $user_id, $type, $rel);
    $stmt->execute();
    $stmt->close();
    return true;
}

function getFriendList($id, $type = 0) {
    global $mysqli;
    $id = intval($id); // Ensure ID is an integer
    $friend_list = [];
    // Use prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT target FROM boom_friends WHERE hunter = ? AND fstatus = 3");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($find = $result->fetch_assoc()) {
        $friend_list[] = $find['target'];
    }
    $stmt->close();
    if ($type == 1) {
        $friend_list[] = $id;
    }
    return $friend_list;
}

function getRankList($rank) {
    global $mysqli;
    $rank = intval($rank); // Ensure rank is an integer
    $list = [];
    // Use prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT user_id FROM boom_users WHERE user_rank = ?");
    $stmt->bind_param("i", $rank);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($find = $result->fetch_assoc()) {
        $list[] = $find['user_id'];
    }
    $stmt->close();
    return $list;
}

function getStaffList() {
    global $mysqli;
    $list = [];
    // Use prepared statement for better security and performance
    $stmt = $mysqli->prepare("SELECT user_id FROM boom_users WHERE user_rank >= ?");
    $staff_rank = 70;
    $stmt->bind_param("i", $staff_rank);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($find = $result->fetch_assoc()) {
        $list[] = $find['user_id'];
    }
    $stmt->close();
    return $list;
}
function boomListNotify($list, $type, $custom = array()) {
    global $mysqli, $data;
    if (empty($list)) {
        return false;
    }
    $stmt = $mysqli->prepare("
        INSERT INTO boom_notification 
        (notifier, notified, notify_type, notify_date, notify_source, notify_id, notify_rank, notify_delay, notify_reason, notify_custom, notify_custom2) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $current_time = time();
    foreach ($list as $user) {
        $def = [
            'hunter'   => $data['system_id'],
            'room'     => $data['user_roomid'],
            'rank'     => 0,
            'delay'    => 0,
            'reason'   => '',
            'source'   => 'system',
            'sourceid' => 0,
            'custom'   => '',
            'custom2'  => '',
        ];
        $c = array_merge($def, $custom);
        // Bind parameters
        $stmt->bind_param(
            "iisisiissss", 
            $c['hunter'], $user, $type, $current_time, 
            $c['source'], $c['sourceid'], $c['rank'], $c['delay'], 
            $c['reason'], $c['custom'], $c['custom2']
        );
        $stmt->execute();
    }
    $stmt->close();
    updateListNotify($list);
    return true;
}
function boomNotify($type, $custom = array()){
	global $mysqli, $data;
	$def = array(
		'hunter'=> $data['system_id'],
		'target'=> 0,
		'room'=> $data['user_roomid'],
		'rank'=> 0,
		'delay'=> 0,
		'reason'=> '',
		'source'=> 'system',
		'sourceid'=> 0,
		'custom' => '',
		'custom2' => '',
		'icon' => '',
	);
	$c = array_merge($def, $custom);
	if($c['target'] == 0){
		return false;
	}
	$mysqli->query("INSERT INTO boom_notification ( notifier, notified, notify_type, notify_date, notify_source, notify_id, notify_rank, notify_delay, notify_reason, notify_custom, notify_custom2, notify_icon) 
	VALUE ('{$c['hunter']}', '{$c['target']}', '$type', '" . time() . "', '{$c['source']}', '{$c['sourceid']}', '{$c['rank']}', '{$c['delay']}', '{$c['reason']}', '{$c['custom']}', '{$c['custom2']}' , '{$c['icon']}')");
	updateNotify($c['target']); 
}
function updateNotify($id){
	global $mysqli;
	$stmt = $mysqli->prepare("UPDATE boom_users SET naction = naction + 1 WHERE user_id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->close();
}

function updateListNotify($list){
    global $mysqli;
    if(empty($list)){
        return false;
    }
    // Sanitize the list: ensure all items are integers.
    $sanitized_list = array_map('intval', $list);
    // Prepare the query
    $placeholders = implode(',', array_fill(0, count($sanitized_list), '?'));
    $stmt = $mysqli->prepare("UPDATE boom_users SET naction = naction + 1 WHERE user_id IN ($placeholders)");
    // Dynamically bind parameters to the query.
    $stmt->bind_param(str_repeat('i', count($sanitized_list)), ...$sanitized_list);
    // Execute the query.
    $stmt->execute();
    $stmt->close();
}

function updateStaffNotify(){
    global $mysqli;
    // Using prepared statement for safety, even though there are no dynamic inputs
    $stmt = $mysqli->prepare("UPDATE boom_users SET naction = naction + 1 WHERE user_rank > ?");
    $rank_threshold = 7; // User rank threshold for staff
    // Bind the threshold as a parameter and execute the statement
    $stmt->bind_param('i', $rank_threshold);
    $stmt->execute();
    $stmt->close();
}
function updateAllNotify(){
    global $mysqli;
    // Calculate the delay (number of minutes ago) securely
    $delay = calMinutes(2);
    // Use prepared statements for better security
    $stmt = $mysqli->prepare("UPDATE boom_users SET naction = naction + 1 WHERE last_action > ?");
    // Bind the parameter and execute the query
    $stmt->bind_param('i', $delay); // Assuming 'i' for integer (Unix timestamp or equivalent)
    $stmt->execute();
    $stmt->close();
}

function loadIgnore($id){
    global $mysqli, $data;
    $list = [];
    // Check if the list is cached in Redis (uncomment if Redis caching is enabled)
    // if(is_array($cache = redisGetObject('ignore:' . $id))){
    //     return $cache;
    // }
    // Use a prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT ignored FROM boom_ignore WHERE ignorer = ?");
    $stmt->bind_param("i", $data['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    // Fetch the ignored user IDs
    while ($ignore = $result->fetch_assoc()) {
        $list[] = (int) $ignore['ignored'];
    }
    $stmt->close();
    // Cache the result in Redis (uncomment if Redis caching is enabled)
    // redisSetObject('ignore:' . $id, $list);
    return $list;
}

function createIgnore(){
    global $mysqli, $data;
    // Initialize an array to hold the ignored user IDs
    $ignore_list = [];
    // Use a prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT ignored FROM boom_ignore WHERE ignorer = ?");
    $stmt->bind_param("i", $data['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    // Fetch ignored user IDs and store them in an array
    while($ignore = $result->fetch_assoc()){
        $ignore_list[] = $ignore['ignored'];
    }
    // Close the prepared statement
    $stmt->close();
    // Store the ignore list in the session variable, using '|' as separator
    $_SESSION[BOOM_PREFIX . 'ignore'] = '|' . implode('|', $ignore_list) . '|';
}

function isIgnored($ignore, $id){
	global $cody;
	if(strpos($ignore, '|' . $id . '|') !== false){
		return true;
	}
}
function getIgnore(){
	global $cody;
	return $_SESSION[BOOM_PREFIX . 'ignore'];
}
function processChatMsg($post) {
	global $data;
	if($post['user_id'] != $data['user_id'] && !preg_match('/http/',$post['post_message'])){
		$post['post_message'] = str_ireplace($data['user_name'], '<span class="my_notice">' . $data['user_name'] . '</span>', $post['post_message']);
	}
	return mb_convert_encoding(systemReplace($post['post_message']), 'UTF-8', 'auto');
}
function processPrivateMsg($post) {
	global $data;
	return mb_convert_encoding(systemReplace($post['message']), 'UTF-8', 'auto');
}
function mainRoom(){
	global $data;
	if($data['user_roomid'] == 1){
		return true;
	}
}
function renderInfo($user){
}
function chatRank($user){
	global $data;
	if(isBot($user)){
		return '';
	}
	$rank = systemRank($user['user_rank'], 'chat_rank');
	if($rank != ''){
		return $rank;
	}
}
function isQuotable($post){
	if(!isSystem($post['user_id'])){
		return true;
	}
}
/*=====================store=================*/
function fu_borderLevel($blev) {
    if ($blev < 1) {
        return 'border-1';  // Ensure that levels below 1 default to border-1
    }
    elseif ($blev >= 1 && $blev <= 100) {
        return 'border-' . $blev;  // Directly return the exact border level
    }
    else {
        return 'border-100';  // Levels above 100 default to border-100
    }
}

function fu_levelColors($lev) {
    // Hide rank 0
    if ($lev === 0) {
        return 'hidden'; // Return a class name or simply an empty string
    }

    $colors = [
        'bcback1', // 0-9
        'bcback2', // 10-19
        'bcback3', // 20-29
        'bcback4', // 30-39
        'bcback5', // 40-49
        'bcback6', // 50-59
        'bcback7', // 60-69
        'bcback8', // 70-79
        'bcback9', // 80-89
        'bcback10' // 90-99
    ];

    // Make sure levels above 0 are handled correctly
    if ($lev > 0 && $lev < 100) {
        return $colors[floor($lev / 10)];
    } elseif ($lev == 100) {
        return 'glow reach-100'; // Special case for level 100
    }

    return ''; // Return empty string for invalid levels
}
// Levels system
function userExpLevel($type) {
    global $mysqli, $data;
	$res = array();
    // Escape and determine the type of XP gain
    $exp_type = escape($type);
    $exp_amount = 0;

    // Determine the amount of XP based on the type
    switch($exp_type) {
        case "exp_chat":
            $exp_amount = $data['exp_chat']; // XP gain from chat
            break;
        case "exp_priv":
            $exp_amount = $data['exp_priv']; // XP gain from private messages
            break;
        case "exp_post":
            $exp_amount = $data['exp_post']; // XP gain from posts
            break;
        case "exp_gift":
            $exp_amount = $data['exp_gift']; // XP gain from gifts
            break;
        default:
            $exp_amount = 0; // Default to 0 if type is not recognized or no value provided
            break;
    }

    // Set default XP gain to 0 if no specific XP value is found
    $xpGain = $exp_amount > 0 ? $exp_amount : 0;

    $maxlevel = 100; // Maximum level a user can reach
    $currentLevel = $data['user_level'];
    $currentExp = $data['user_exp'];
    
    // Calculate experience needed for the next level
    $expNeeded = $currentLevel == 0 ? 5 : $currentLevel * 50;

    // Calculate remaining experience needed to level up
    $xpToNextLevel = $expNeeded - $currentExp;

    // Output remaining XP needed to reach the next level
    $res['xpToNextLevel'] = "You need $xpToNextLevel XP to reach the next level.";

    // User can gain experience but hasn't leveled up yet
    if ($currentExp + $xpGain < $expNeeded && $currentLevel < $maxlevel) {
        $mysqli->query("UPDATE boom_users SET user_exp = user_exp + $xpGain WHERE user_id = '{$data['user_id']}'");
    }
    // User reaches or exceeds the required experience for the next level
    elseif ($currentExp + $xpGain >= $expNeeded && $currentLevel < $maxlevel) {
        $newLevel = $currentLevel + 1;
        // Calculate remaining XP after leveling up
        $remainingXP = ($currentExp + $xpGain) - $expNeeded;

        // Level up the user, reset experience and add remaining XP (if any)
        $mysqli->query("UPDATE boom_users SET user_exp = $remainingXP, user_level = user_level + 1 WHERE user_id = '{$data['user_id']}'");
        // Create a message to announce the level-up
        $msg = str_replace(
            array('@user@', '@level@'),
            array($data['user_name'], $newLevel),
            '<a class="status_icon" style="display: inline-block; width: 15px;"> <img src="default_images/status/online.svg" alt="Status Icon" style="width: 12px; height: 12px;"> </a> <span>Member <strong style="color: #b70606;">[ @user@ ]</strong> has reached Level <strong style="color: #b70606;">[ @level@ ]</strong>! Congratulations! Keep interacting to level up even more.</span>'
        );

        // Send the level-up message to the chat
        systemPostChat($data['user_roomid'], $msg);

        // Display the level-up template
        echo boomTemplate('store/exp_level_up', $msg);
    }
    // User has reached the maximum level
    elseif ($currentLevel == $maxlevel) {
        return false;
    }
}
function getProfileLevel($user){
	global $data;
	echo boomTemplate('element/pro_level', $user);
}


function useLevel(){
	global $data;
	if($data['use_level'] > 0){
		return true;
	}
}
function canLevel($user){
	if(!isGuest($user)){
		return true;
	}
}
function getMyGift($content) {
    global $mysqli, $data;

    // Escape and sanitize input
    $gift_text = $data['coins_gift_text']; // The current gift text
    $coins_gift_code = $data['coins_gift_code']; // The amount of coins associated with the gift code
    $user_gift_gold = $data['user_gift_gold']; // The gift the user has already claimed

    // Check if the content matches the gift and the user hasn't claimed it yet
    if ($content == $gift_text && $user_gift_gold != $gift_text) {
        // Update user gold and mark the gift as claimed
        $stmt = $mysqli->prepare("UPDATE boom_users SET user_gold = user_gold + ?, user_gift_gold = ? WHERE user_id = ?");
        $stmt->bind_param('isi', $coins_gift_code, $gift_text, $data['user_id']);
        $stmt->execute();

        // Prepare and send the success message
        $success_msg = str_replace('@coins@', $coins_gift_code, 'I got <b>@coins@</b> Gold from the code!');
        echo systemSpecial($success_msg, 'seen', array('icon' => 'default.svg', 'title' => 'Gift Bot'));
    } 
    // If the user has already claimed the gift or the content doesn't match
    elseif ($content == $gift_text && $user_gift_gold == $gift_text) {
        // Prepare and send the failure message
        $error_msg = 'You have used this code before or it has been changed.';
        echo systemSpecial($error_msg, 'seen', array('icon' => 'default.svg', 'title' => 'Gift Bot'));
    }
}

function useWings(){
	global $data;
	if($data['use_wings'] > 0){
		return true;
	}
}
function useFrame(){
	global $data;
	if($data['use_frame'] > 0){
		return true;
	}
}
/* function exChooseStoreItemTime($id){
	global $mysqli;
	$loadd = '';
	$get_store = $mysqli->query("SELECT * FROM boom_store WHERE store_id = '$id'");
	if($get_store->num_rows > 0){
		while($store = $get_store->fetch_assoc()){
			$loadd = $store['rank_end'];
		}
	}
	else {
    	return false;
	}
	return $loadd;
} */
/*
function Fu_storeItemTime($id){
	global $mysqli, $data, $lang;
	$loadd = '';
	$get_store = $mysqli->query("SELECT * FROM boom_store WHERE store_id = '$id'");
	if($get_store->num_rows > 0){
		while($store = $get_store->fetch_assoc()){
		    if($store['rank_end'] == 1){
		        $loadd = '7 days';
		    }
		    if($store['rank_end'] == 2){
		        $loadd = '15 days';
		    }
		    if($store['rank_end'] == 3){
		        $loadd = '30 days';
			}
		}
	}
	else {
    	return false;
	}
	return $loadd;
}

function Fu_chooseStoreItemTime($id){
	global $mysqli, $data, $lang;
	$loadd = '';
	$get_store = $mysqli->query("SELECT * FROM boom_store WHERE id = '$id'");
	if($get_store->num_rows > 0){
		while($store = $get_store->fetch_assoc()){
			if($store['rank_end'] == 0){
		        $loadd = '<p class="label" style="color:red;">There is no term</p>';
			}
		    if($store['rank_end'] == 1){
		        $loadd = '<p class="label" style="color:red;">membership period is 7 days</p>';
		    }
		    if($store['rank_end'] == 2){
		        $loadd = '<p class="label" style="color:red;">membership period is 15 days</p>';
		    }
		    if($store['rank_end'] == 3){
		        $loadd = '<p class="label" style="color:red;">membership period is 30 days</p>';
			}
		}
	}
	else {
    	return false;
	}
	return $loadd;
}*/
// premium time
function Fu_premiumNewTime($plan, $user) {
    $valid_plans = [7, 15, 30, 180, 365];
    
    if (in_array($plan, $valid_plans)) {
        return strtotime("+$plan days");
    }
    
    return isset($user['vip_end']) ? $user['vip_end'] : time(); // Default to current time if vip_end is not set
}

function Fu_premiumDate($date){
	return date("Y-m-d", $date);
}
function Fu_premiumEndingDate($val){
	global $lang;
	if($val == 0){
		return 'Life time';
	}
	else {
		return '<i class="ri-error-warning-fill error"></i> ' .  Fu_premiumDate($val);
	}
}
function exProfileBg($user){
	global $data;
	$bg = '';
	if(!empty($user['pro_background'])){
		$bg = 'style="background: url(upload/premium/premium_background/'. $user['pro_background'] .') center bottom / cover;"';
	}
	return $bg;
}
function isPremium($user){
	if($user['user_prim'] > 0){
		return true;
	}
}
/*=====================end===================*/
function createLog($data, $post, $ignore = ''){
	$log_options = '';
	$report = 0;
	$delete = 0;
	$frame = $level = $border = $wing1 = $wing2 = $av_style ='';

	$m = 0;
	if(isIgnored($ignore, $post['user_id'])){
		return false;
	}
	if(boomAllow($post['log_rank'])){
		return false;
	}
	if(canDeleteLog() || canDeleteRoomLog() || canDeleteSelfLog($post)){
		$delete = 1;
		$m++;
	}
	else if(canReport() && !isSystem($post['user_id'])){
		$report = 1;
		$m++;
	}
	if(useFrame()) {
		if (!empty($post['photo_frame'])) {
			// Sanitize and validate the photo_frame input
			$safe_frame = htmlspecialchars($post['photo_frame'], ENT_QUOTES, 'UTF-8');
			$allowed_ext = [ 'gif', 'jpg', 'jpeg', 'png', 'bmp', 'webp', 'svg', ];
			$frame_ext = strtolower(pathinfo($safe_frame, PATHINFO_EXTENSION));
			// Validate the image format
			if (in_array($frame_ext, $allowed_ext)) {
				$frame = '<img class="frame_class" src="system/store/frames/' . $safe_frame . '"/>';
			}
			$av_style = 'chat_avatar_frame';
			$level = ($post['user_level'] > 0 && $data['use_level'] == 1) ? '<span title="level" class="ex_levels ' . fu_levelColors($post['user_level']) . '">' . $post['user_level'] . '</span>' : '';
			$border = fu_borderLevel($post['user_level']);
		}
	}
	if(useWings()) {
		$wing1= (useWings() && !empty($post['name_wing1'])) ? '<img class="wing_icon" src="system/store/wing/'. $post['name_wing1'] .'" />' : '';
		$wing2= (useWings() && !empty($post['name_wing2'])) ? '<img class="wing_icon" src="system/store/wing/'. $post['name_wing2'] .'" />' : '';
	}
	if($m > 0){
		$log_options = '<div class="cclear" onclick="logMenu(this, ' . $post['post_id'] . ',' . $delete . ',' . $report . ');"><i class="ri-more-2-line i_btm"></i></div>';
	}

	return  '<li id="log' . $post['post_id'] . '" data="' . $post['post_id'] . '" class="ch_logs ' . $post['type'] . '">
				<div class="'.$av_style.'  avtrig chat_avatar" onclick="avMenu(this,'.$post['user_id'].',\''.$post['user_name'].'\','.$post['user_rank'].','.$post['user_bot'].',\''.$post['country'].'\',\''.$post['user_cover'].'\',\''.$post['user_age'].'\',\''.userGender($post['user_sex']).'\');">
					<img class="'. $border .' cavatar avav ' . avGender($post['user_sex']) . ' ' . ownAvatar($post['user_id']) . '" src="' . myAvatar($post['user_tumb']) . '" />
					'. $frame .$level.'
				</div>
				<div class="my_text">
					<div class="btable">
							<div class="cname">' . chatRank($post) . '<span class="username ' . myColorFont($post) . '">'.$wing1.' ' . $post['user_name'] . ''.$wing2.'</span></div>
							<div class="cdate">' . chatDate($post['post_date']) . '</div>
							' . $log_options . '
					</div>
					<div class="chat_message ' . $post['tcolor'] . '">' . processChatMsg($post) . '</div>
				</div>
			</li>';
}
function privateLog($post, $hunter){
	if($hunter == $post['hunter']){
		return '<li id="priv' . $post['id'] . '">
					<div class="private_logs">
						<div class="private_avatar">
							<img data="' . $post['user_id'] . '" class="get_info avatar_private" src="' . myAvatar($post['user_tumb']) . '"/>
						</div>
						<div class="private_content">
							<div class="hunter_private">' . processPrivateMsg($post) . '</div>
							<p class="pdate">' . displayDate($post['time']) . '</p>
						</div>
					</div>
				</li>';
	}
	else {
		return '<li id="priv' . $post['id'] . '">
					<div class="private_logs">
						<div class="private_content">
							<div class="target_private">' . processPrivateMsg($post) . '</div>
							<p class="ptdate">' . displayDate($post['time']) . '</p>
						</div>
						<div class="private_avatar">
							<img data="' . $post['user_id'] . '" class="get_info avatar_private" src="' . myAvatar($post['user_tumb']) . '"/>
						</div>
					</div>
				</li>';
	}
} 
function avGender_icon($s){
	global $data;
	if($data['gender_ico'] > 0){
		switch($s){
			case 1:
				return '<i class="ri-men-line boy"></i>';
			case 2:
				return '<i class="ri-women-line girl"></i>';
			case 3:
				return '<i class="ri-genderless-line"></i>';
			default:
				return '';
		}
	}
	else {
		return '';
	}
}

function DJonAir_icon($list){
	global $data;
	$icon ='';
	$onair = '';
	if($list['user_dj'] ==1 ){
        $icon = '<i class="ri-customer-service-fill warn"></i>';
        
	}
	if($list['user_onair'] ==1 ){
        $icon = '<i title="broadcaster" class="ri-customer-service-fill success"></i>';
        
	}
    return $icon;
}
function getliveIcon($list){
	$icon ='';
	if($list['is_live'] ==1 ){
        $icon = '<button type="button" onclick="fuseVideo_watch_broadcaster('.$list['user_id'].');" class="broadcast_watcher"><i class="ri-video-on-line" style=" font-size: 23px; color: #25e325; "></i></button>';
	}
    return $icon;	
}
function createUserlist($list) {
    global $data, $lang;
	$frame = $frame_style = $level = $border = $av_style = '';
    if (!isVisible($list)) {
        return false;
    }

    $icons = [
        'rank' => getRankIcon($list, 'list_rank'),
        'mute' => getMutedIcon($list, 'list_mute'),
        'mobile' => getMobileIcon($list, 'list_mob'),
        'dj' => DJonAir_icon($list, 'list_dj'),
        'is_live' => getliveIcon($list),
		// Handle wings
		'wing1' => (useWings() && !empty($list['name_wing1'])) ? '<img class="wing_icon" src="system/store/wing/'. $list['name_wing1'] .'" />' : '',
		'wing2' => (useWings() && !empty($list['name_wing2'])) ? '<img class="wing_icon" src="system/store/wing/'. $list['name_wing2'] .'" />' : '',
    ];

    $verifyIcon = $list['user_verify'] == 1 ? '<div class="user_item_icon icrank"><i class="ri-verified-badge-fill"></i></div>' : '';
    $flag = useFlag($list['country']) ? '<div class="user_item_flag"><img src="' . countryFlag($list['country']) . '"/></div>' : '';
    $sexIcon = !empty(userGender($list['user_sex'])) ? '<div class="user_item_sex">' . avGender_icon($list['user_sex']) . '</div>' : '';
    $mood = !empty($list['user_mood']) ? '<p class="text_xsmall bustate bellips">' . $list['user_mood'] . '</p>' : '';
	// Handle frame and avatar styling
	if(useFrame()) {
		
		$safe_frame = htmlspecialchars($list['photo_frame'] ?? '', ENT_QUOTES, 'UTF-8');
		$allowed_ext = [ 'gif', 'jpg', 'jpeg', 'png', 'bmp', 'webp', 'svg', ];
		$frame_ext = strtolower(pathinfo($safe_frame, PATHINFO_EXTENSION));
		// Validate the image format
		if (in_array($frame_ext, $allowed_ext)) {
			$frame = '<img class="frame_class" src="system/store/frames/' . $safe_frame . '"/>';
		}		
		$frame_style = !empty($list['photo_frame']) ? 'user_item_avatar_frame' : '';
		$av_style = !empty($list['photo_frame']) ? 'style="width:40px;height: 40px;"' : '';
		$level = ($list['user_level'] > 0 && $data['use_level'] == 1) ? '<span title="level" class="ex_levels ' . fu_levelColors($list['user_level']) . '">' . $list['user_level'] . '</span>' : '';
		$border = fu_borderLevel($list['user_level']);
	}
	
    $status = '';
    $offline = 'offline';
    if ($list['last_action'] > getDelay() || isBot($list)) {
        $offline = '';
        $status = getStatus($list['user_status'], 'list_status');
    }

    $onClick = sprintf(
        "dropUser(this, %d, '%s', %d, %d, '%s', '%s', '%s', '%s', '%s');",
        $list['user_id'],
        $list['user_name'],
        $list['user_rank'],
        $list['user_bot'],
        $list['country'],
        $list['user_cover'],
        $list['user_age'],
        userGender($list['user_sex']),
        $list['user_level']
    );
    return '<div onclick="' . $onClick . '" class="avtrig user_item ' . $offline . '">
                ' . renderIcon($icons['rank']).'
                <div class="user_item_avatar ' . $frame_style.'">
				
                    <img class="'.$border.'  avav acav ' . avGender($list['user_sex']) . ' ' . ownAvatar($list['user_id']) . '" src="' . myAvatar($list['user_tumb']) . '"/> 
					' . $frame . $status . '' .$level. '
                </div>
				 
                <div class="user_item_data">
                    <p class="username ' . myColorFont($list) . '">'.$icons['wing1'].'' . $list["user_name"] . ''.$icons['wing2'].'</p>
                    ' . $mood . '
                </div>
				
                 ' .renderIcon($icons['is_live']) .renderIcon($icons['dj']) . renderIcon($icons['mobile']). renderIcon($icons['mute']) . $verifyIcon . $sexIcon . $flag .'
            </div>';
}


function renderIcon($icon) {
    return !empty($icon) ? '<div class="user_item_icon icrank">' . $icon . '</div>' : '';
}

function useFlag($country){
	global $data;
	if($data['flag_ico'] > 0 && $country != 'ZZ' && $country != ''){
		return true;
	}
}
function listCountry($c){
	global $lang;
	require BOOM_PATH . '/system/location/country_list.php';
	$list_country = '';
	$list_country .= '<option value="ZZ" ' . selCurrent($c, 'ZZ') . '>' . $lang['not_shared'] . '</option>';
	foreach ($country_list as $country => $key) {
		$list_country .= '<option ' . selCurrent($c, $country) . ' value="' . $country . '">' . $key . '</option>';
	}	
	return $list_country;
}
function userCountry($country){
	global $data;
	if($country != 'ZZ' && $country != ''){
		return true;
	}
}
function countryFlag($country){
	global $data;
	return 'system/location/flag/' . $country . '.png';
}
function countryName($country){
	global $lang;
	require BOOM_PATH . '/system/location/country_list.php';
	if(array_key_exists($country, $country_list)){
		return $country_list[$country];
	}
	else {
		return $lang['not_available'];
	}
}
function chatDate($date){
	return date("j/m G:i", $date);
}
function displayDate($date){
	return date("j/m G:i", $date);
}
function longDate($date){
	return date("Y-m-d ", $date);
}
function longDateTime($date){
	return date("Y-m-d G:i ", $date);
}
function userTime($user){          
	$d = new DateTime(date("d F Y H:i:s",time()));
	$d->setTimezone(new DateTimeZone($user['user_timezone']));
	$r =$d->format('G:i');
	return $r;
}
function boomRenderMinutes($val){
	global $lang;
	$day = '';
	$hour = '';
	$minute = '';
	$d = floor ($val / 1440);
	$h = floor (($val - $d * 1440) / 60);
	$m = $val - ($d * 1440) - ($h * 60);
	if($d > 0){
		if($d > 1){ $day = $d . ' ' . $lang['days']; } else{ $day = $d . ' ' . $lang['day']; }
	}
	if($h > 0){
		if($h > 1){ $hour = $h . ' ' . $lang['hours']; } else{ $hour = $h . ' ' . $lang['hour']; }
	}
	if($m > 0){
		if($m > 1){ $minute = $m . ' ' . $lang['minutes']; } else{ $minute = $m . ' ' . $lang['minute']; }
	}
	return trim($day . ' ' . $hour  . ' ' . $minute);
}
function boomRenderSeconds($val){
	global $lang;
	$day = '';
	$hour = '';
	$minute = '';
	$second = '';
	$d = floor ($val / 86400);
	$h = floor (($val - $d * 86400) / 3600);
	$m = floor (($val - ($d * 86400) - ($h * 3600)) / 60);
	$s = $val - ($d * 86400) - ($h * 3600) - ($m * 60);
	if($d > 0){
		if($d > 1){ $day = $d . ' ' . $lang['days']; } else{ $day = $d . ' ' . $lang['day']; } }
	if($h > 0){
		if($h > 1){ $hour = $h . ' ' . $lang['hours']; } else{ $hour = $h . ' ' . $lang['hour']; }
	}
	if($m > 0){
		if($m > 1){ $minute = $m . ' ' . $lang['minutes']; } else{ $minute = $m . ' ' . $lang['minute']; }
	}
	if($s > 0){
		if($s > 1){ $second = $s . ' ' . $lang['seconds']; } else{ $second = $s . ' ' . $lang['second']; }
	}
	return trim($day . ' ' . $hour  . ' ' . $minute . ' ' . $second);
}
function boomTimeLeft($t){
	return boomRenderMinutes(floor(($t - time()) / 60));
}
function boomAllow($rank){
	global $data;
	if($data['user_rank'] >= $rank){
		return true;
	}
}
function userBoomAllow($user, $val){
	if($user['user_rank'] >= $val){
		return true;
	}
}
function boomRole($role){
	global $data;
	if($data['user_role'] >= $role){
		return true;
	}
}
function haveRole($role){
	if($role > 0){
		return true;
	}
}
function isGreater($rank){
	global $data;
	if($data['user_rank'] > $rank){
		return true;
	}
}
function mySelf($id){
	global $data;
	if($id == $data['user_id']){
		return true;
	}
}
function isBot($user){
	if($user['user_bot'] > 0){
		return true;
	}
}
function systemBot($user){
	if($user == 9){
		return true;
	}
}
function isSystem($id){
	global $data;
	if($id == $data['system_id']){
		return true;
	}
}

function getTopic($t){
	global $lang;
	$topic = processUserData($t);
	if(!empty($topic)){
		return systemSpecial($topic, 'topic_log', array('icon'=> 'topic.svg', 'title'=> $lang['topic_title']));
	}
}
function boomRoomData($r){
    global $mysqli;
    $room = null; // Default return value when no room is found
    // Use a prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM boom_rooms WHERE room_id = ?");
    $stmt->bind_param("i", $r);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $room = $result->fetch_assoc(); // Fetch room data
    }
    // Close the prepared statement
    $stmt->close();
    return $room; // Return the room data (or null if not found)
}


function boomConsole($type, $custom = array()){
    global $mysqli, $data;
    // Default values for the parameters
    $def = array(
        'hunter' => $data['user_id'],
        'target' => $data['user_id'],
        'room' => $data['user_roomid'],
        'rank' => 0,
        'delay' => 0,
        'reason' => '',
        'custom' => '',
        'custom2' => '',
    );
    // Merge default values with the custom ones
    $c = array_merge($def, $custom);
    // Prepare the query with placeholders to prevent SQL injection
	$current_time = time();
    $query = "
        INSERT INTO boom_console (hunter, target, room, ctype, crank, delay, reason, custom, custom2, cdate)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    // Prepare the statement
    if ($stmt = $mysqli->prepare($query)) {
        // Bind the parameters
        $stmt->bind_param(
            'iiisisisss', // Define types for each value (integer, integer, string, etc.)
            $c['hunter'], 
            $c['target'], 
            $c['room'], 
            $type, 
            $c['rank'], 
            $c['delay'], 
            $c['reason'], 
            $c['custom'], 
            $c['custom2'], 
            $current_time
        );
        // Execute the statement and check for errors
        if (!$stmt->execute()) {
            // Log the error if the query fails
            error_log("Error executing query: " . $stmt->error);
        }
        // Close the statement
        $stmt->close();
    } else {
        // Log the error if the statement preparation fails
        error_log("Error preparing query: " . $mysqli->error);
    }
}

function boomHistory($type, $custom = array()) {
    global $mysqli, $data;
    // Default values for the history data
    $def = array(
        'hunter' => $data['user_id'],
        'target' => 0,
        'rank'   => 0,
        'delay'  => 0,
        'reason' => '',
        'content' => '',
    );
    // Merge custom values with default ones
    $c = array_merge($def, $custom);
    // Ensure that a valid target is provided
    if ($c['target'] == 0) {
        return false;
    }
    // Prepare the SQL query
    $stmt = $mysqli->prepare(
        "INSERT INTO boom_history 
        (hunter, target, htype, delay, reason, history_date) 
        VALUES (?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        return false;
    }
    $history_date = time();    
    // Modified bind_param - changed htype to 's' (string)
    $bound = $stmt->bind_param(
        "iisisi", // Changed third parameter to 's' for string
        $c['hunter'],
        $c['target'],
        $type,       // Now treated as string
        $c['delay'],
        $c['reason'],
        $history_date
    );
    if (!$bound) {
        return false;
    }
    
    $executed = $stmt->execute();
    if (!$executed) {
        $stmt->close();
        return false;
    }
    $stmt->close();
    return true;
}
function renderReason($t){
	global $lang;
	switch($t){
		case '':
			return $lang['no_reason'];
		case 'badword':
			return $lang['badword'];
		case 'spam':
			return $lang['spam'];
		case 'flood':
			return $lang['flood'];
		case 'vpn':
			return $lang['vpn'];
		default:
			return systemReplace($t);
	}
}
function userUnmute($user){
    global $mysqli;
    // Check if the user is not a guest and is muted
    if(!guestMuted()){
        // Clear any mute-related notifications
        clearNotifyAction($user['user_id'], 'mute');
        // Use cl_update_user_data to update user mute status
        $updateData = array(
            'user_mute' => 0,
            'mute_msg' => '',
            'user_regmute' => 0
        );
        $unmuteSuccess = cl_update_user_data($user['user_id'], $updateData);
        // Check if the update was successful
        if ($unmuteSuccess) {
            // Send unmute notification
            boomNotify('unmute', array('target'=> $user['user_id'], 'source'=> 'mute'));
        } else {
            // Log error if unmute update fails
            error_log("Failed to unmute user with ID: {$user['user_id']}");
        }
    }
}

function userUnkick($user){
    global $mysqli;
    // Update user data using the cl_update_user_data function
    $unkick = cl_update_user_data($user['user_id'], array('user_kick' => 0));
    // Check if the update was successful
    if($unkick){
        return true; // Successfully un-kicked the user
    } else {
        // Log an error if the un-kick failed
        error_log("Failed to unkick user with ID: {$user['user_id']}");
        return false; // Return false if the operation failed
    }
}

	/*
	if(roomMuted()){
		$d['rm'] = 1;
	}
	if(guestMuted()){
		$d['rm'] = 2;
	}
	if(mutedData($data)){
		if(isMuted($data) || isRegmute($data)){
			$d['rm'] = 2;
		}
		else {
			userUnmute($data);
		}
	}*/
function checkMute($data) {
    // Initialize the mute flags as an associative array
    $muteFlags = [
        'isMuted' => false,        // Whether the user is muted
        'canPrivate' => true,      // Whether the user can send private messages
        'isMainMuted' => false,    // Whether the user is muted in the main room
        'isRoomMuted' => false     // Whether the user is muted in the room
    ];
    // Check if the user is globally muted
    if (isMuted($data)) {
        $muteFlags['isMuted'] = true;
        $muteFlags['isRoomMuted'] = true; // Assuming room mute is also part of global mute
    }
    if (!canPrivate()) {
        $muteFlags['canPrivate'] = false; // User cannot send private messages
    }
	
    // Check if the user is muted in the main room
    if (isMainMuted($data) || isRoomMuted($data) || !canMain()) {
        $muteFlags['isMainMuted'] = true;
    }
    return $muteFlags;
}


function muted(){
	global $data;
	if(isMuted($data) || isBanned($data) || isKicked($data) || outChat($data) || isRegmute($data) || guestMuted()){
		return true;
	}
}


function isRoomMuted($user){
	if($user['room_mute'] > time()){
		return true;
	}
}
function mainMuted(){
	global $data;
	if(isMuted($data) || isMainMuted($data) || !inChat($data) || isRoomMuted($data) || !canMain()){
		return true;
	}
}
function isMuted($user){
	if($user['user_mute'] > time()){
		return true;
	}
}
//main muted
function isMainMuted($user){
	if($user['user_mmute'] > time()){
		return true;
	}
}
//private muted
function isPrivateMuted($user){
	if($user['user_pmute'] > time()){
		return true;
	}
}
function isGuestMuted($user){
	global $data;
	if($user['user_rank'] == 0 && $data['guest_talk'] == 0){
		return true;
	}
}
function guestMuted(){
	global $data;
	if($data['user_rank'] == 0 && $data['guest_talk'] == 0){
		return true;
	}
}
function isRegmute($user){
	if($user['user_regmute'] > time()){
		return true;
	}
}
function isOnAir($user){
	if($user['user_onair'] > 0){
		return true;
	}
}
function mutedData($user){
	if($user['user_mute'] > 0 || $user['user_regmute'] > 0){
		return true;
	}
}
function kickedData($user){
	if($user['user_kick'] > 0){
		return true;
	}
}
function isBanned($user){
	if($user['user_banned'] > 0){
		return true;
	}
}
function isKicked($user){
	if($user['user_kick'] > time()){
		return true;
	}
}
function systemNameFilter($user){
	return '<span onclick="getProfile(' . $user['user_id'] . ')"; class="sysname bclick">' . $user['user_name'] . '</span>';
}
function joinRoom(){
	global $lang, $data, $cody;
	if(allowLogs() && isVisible($data) && $cody['join_room'] == 1){
		$content = str_replace('%user%', systemNameFilter($data), $lang['system_join_room']);
		systemPostChat($data['user_roomid'], $content, array('type'=> 'system__join'));
	}
}
function leaveRoom(){
	global $data, $lang, $cody;
	if(allowLogs() && $cody['leave_room'] == 1){
		if(isVisible($data) && $data['user_roomid'] != 0 && $data['last_action'] > time() - 30 ){
			$content = str_replace('%user%', systemNameFilter($data), $lang['quit_room']);
			systemPostChat($data['user_roomid'], $content, array('type'=> 'system__leave'));
		}
	}
}
function changeNameLog($user, $n){
	global $lang, $data, $cody;
	if(allowLogs() && isVisible($user) && $cody['name_change'] == 1){
		$content = str_replace('%user%', $user['user_name'], $lang['system_name_change']);
		$user['user_name'] = $n;
		$content = str_replace('%nname%', systemNameFilter($user), $content);
		systemPostChat($user['user_roomid'], $content, array('type'=> 'system__action'));
	}
}


function processUserData($t){
	global $data;
	return str_replace(array('%user%'), array($data['user_name']), $t);
}
function roomStaff(){
	if(boomRole(4)){
		return true;
	}
}
function userRoomStaff($rank){
	if($rank >= 4){
		return true;
	}
}
function allowLogs(){
	global $data;
	if($data['allow_logs'] == 1){
		return true;
	}
}
function isVisible($user){
	if($user['user_status'] != 99){
		return true;
	}
}
function isSecure($user){
	if(isEmail($user['user_email'])){
		return true;
	}
}
function isMember($user){
	if(!isGuest($user) && !isBot($user)){
		return true;
	}
}
function isGuest($user){
	if($user['user_rank'] == 0){
		return true;
	}
}
function guestForm(){
	global $data;
	if($data['guest_form'] == 1){
		return true;
	}
}
function strictGuest(){
	global $cody;
	if($cody['strict_guest'] == 1){
		return true;
	}
}
function userDj($user){
	if($user['user_dj'] == 1){
		return true;
	}
}
function boomRecaptcha(){
	global $data;
	if($data['use_recapt'] > 0){
		return true;
	}
}

function encrypt($d){
	return sha1(str_rot13($d . BOOM_CRYPT));
}
function boomEncrypt($d, $encr){
	return sha1(str_rot13($d . $encr));
}
function getDelay(){
    global $data;
	if($data['online_forever'] ==1){
	//display online users for long time
		return time() - 40000000;
	}else{
		return time() - 35;
	}
    
}
function getMinutes($t){
	return $t / 60;
}
function userActive($user, $c){
	global $data, $cody;
	if(!isVisible($user) && !boomAllow($cody['can_inv_view'])){
		return '<img class="' . $c . '" src="' . $data['domain'] . '/default_images/icons/innactive.svg"/>';
	}
	else if($user['last_action'] >= getDelay() || isBot($user)){
		return '<img class="' . $c . '" src="' . $data['domain'] . '/default_images/icons/active.svg"/>';
	}
	else {
		return '<img class="' . $c . '" src="' . $data['domain'] . '/default_images/icons/innactive.svg"/>';
	}
}
function isOwner($user){
	if($user['user_rank'] == 100){
		return true;
	}
}
function isStaff($rank){
	if($rank >= 8){
		return true;
	}
}
function genKey(){
	return md5(rand(10000,99999) . rand(10000,99999));
}
function genCode(){
	return rand(111111,999999);
}
function genSnum(){
	global $data;
	return $data['user_id'] . rand(1111111, 9999999);
}
function boomUnderClear($t){
	return str_replace('_', ' ', $t);
}
function allowGuest(){
	global $data;
	if($data['allow_guest'] == 1){
		return true;
	}
}
function boomMerge($a, $b){
	$c = $a . '_' . $b;
	return trim($c);
}
function clearNotifyAction($id, $type){
    global $mysqli;
    // Prepare the SQL query to prevent SQL injection
    $stmt = $mysqli->prepare("DELETE FROM boom_notification WHERE notified = ? AND notify_source = ?");
    // Bind the parameters to the prepared statement
    $stmt->bind_param("is", $id, $type); // 'i' for integer, 's' for string
    // Execute the query and check for success
    if ($stmt->execute()) {
        $stmt->close(); // Close the prepared statement
        return true;    // Return true if the query executed successfully
    } else {
        $stmt->close(); // Close the prepared statement in case of failure
        return false;   // Return false if the query failed
    }
}

function setToken(){
	global $data, $cody;
	if(!empty($_SESSION[BOOM_PREFIX . 'token'])){
		$_SESSION[BOOM_PREFIX . 'token'] = $_SESSION[BOOM_PREFIX . 'token'];
		return $_SESSION[BOOM_PREFIX . 'token'];
	}
	else {
		$session = md5(rand(000000,999999));
		$_SESSION[BOOM_PREFIX . 'token'] = $session;
		return $session;
	}
}
function logPending($c){
	return array('log', $c);
}
function modalPending($c, $t, $s = 400){
	return array('modal', $c,$t,$s);
}
function pendingPush($s, $d){
	if(is_array($d)){
		array_push($s, $d);
	}
	return $s;
}
function boomDuplicateIp($val){
    global $mysqli, $data, $cody;
    // Sanitize the IP input
    $val = escape($val);
    // Prepare the query to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM `boom_banned` WHERE `ip` = ?");
    $stmt->bind_param("s", $val);
    $stmt->execute();
    $result = $stmt->get_result();
    // Check if the IP is found in the banned list
    if($result->num_rows > 0){
        $stmt->close();
        return true;
    }
    $stmt->close();
    return false;
}

function checkToken() {
	global $cody;
    if (!isset($_POST['token']) || !isset($_SESSION[BOOM_PREFIX . 'token']) || empty($_SESSION[BOOM_PREFIX . 'token'])) {
        return false;
    }
	if($_POST['token'] == $_SESSION[BOOM_PREFIX . 'token']){
		return true;
	}
    return false;
}

// ranking functions

function getMutedIcon($user, $c){
	global $lang;
	if(isGuestMuted($user)){
		return '<img title="' . $lang['view_only'] . '" class="' . $c . '" src="default_images/actions/guestmuted.svg"/>';
	}
	if(isGhosted($user) && canGhost()){
		return  '<div class="user_item_icon icghost"><img class="' . $c . '" src="default_images/actions/ghost.svg"/></div>';
	}	
	
	if(isRegmute($user)){
		return '<img title="' . $lang['muted'] . '" class="' . $c . '" src="default_images/actions/regmuted.svg"/>';
	}
	else if(isMuted($user) || isMainMuted($user)){
		return '<img title="' . $lang['muted'] . '" class="' . $c . '" src="default_images/actions/muted.svg"/>';
	}
	else if(isRoomMuted($user)){
		return '<img title="' . $lang['muted'] . '" class="' . $c . '" src="default_images/actions/room_muted.svg"/>';
	}
	else if(isPrivateMuted($user)){
		return '<img title="' . $lang['private_muted'] . '" class="' . $c . '" src="default_images/actions/private_mute.png"/>';
	}	
	else {
		return '';
	}
}

// sex and gender and status functions
function listGender($sex){
	global $lang;
	$list = '';
	$list .= '<option ' . selCurrent($sex, 1) . ' value="1">' . $lang['male'] . '</option>';
	$list .= '<option ' . selCurrent($sex, 2) . ' value="2">' . $lang['female'] . '</option>';
	$list .= '<option ' . selCurrent($sex, 3) . ' value="3">' . $lang['other'] . '</option>';
	return $list;
}
function validGender($sex){
	$gender = array(1,2,3);
	if(in_array($sex, $gender)){
		return true;
	}
}
function getGender($s){
	global $lang;
	switch($s){
		case 1:
			return $lang['male'];
		case 2:
			return $lang['female'];
		case 3:
			return $lang['other'];
		default:
			return $lang['other'];
	}
}
function userGender($g){
	global $lang;
	switch($g){
		case 1:
			return $lang['male'];
		case 2:
			return $lang['female'];
		default:
			return '';
	}
}
function avGender($s){
	global $data;
	if($data['gender_ico'] > 0){
		switch($s){
			case 1:
				return 'avsex boy';
			case 2:
				return 'avsex girl';
			case 3:
				return 'avsex nosex';
			default:
				return 'avsex nosex';
		}
	}
	else {
		return 'avsex nosex';
	}
}

// mobile function

function getMobile() {
    // List of mobile device keywords
    $mobileKeywords = [
        'mobile', 'phone', 'iphone', 'ipad', 'ipod', 'android', 
        'silk', 'kindle', 'blackberry', 'opera mini', 'opera mobi', 
        'symb', 'windows phone', 'tablet'
    ];
    // Get the user agent string
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    // Check if the device is mobile
    $isMobile = 0; // Default to desktop
    foreach ($mobileKeywords as $keyword) {
        if (stripos($userAgent, $keyword) !== false) {
            $isMobile = 1; // Mobile device detected
            break;
        }
    }
    // Determine the device type
    $deviceType = $isMobile ? 'mobile' : 'desktop';
    // Return the device type and is_mobile flag
    return [
        'device_type' => $deviceType,
        'is_mobile' => $isMobile
    ];
}
function getMobileIcon($user, $c){
	global $lang;
	if($user['user_mobile'] > 0){
		return '<img title="' . $lang['mobile'] . '" class="' . $c . '" src="default_images/icons/mobile.svg"/>';
	}
}

// status functions

function validStatus($val){
	$valid = array(1,2,3,99);
	if($val == 99 && !canInvisible()){
		return false;
	}
	if(in_array($val, $valid)){
		return true;
	}
}
function statusTitle($status){
	global $lang;
	switch($status){
		case 1:  
			return $lang['online'];
		case 2:  
			return $lang['away'];
		case 3:  
			return $lang['busy'];
		case 99:  
			return $lang['invisible'];
		default: 
			return $lang['online'];
	}
}
function statusIcon($status){
	switch($status){
		case 1:
			return 'online.svg';
		case 2:
			return 'away.svg';
		case 3:
			return 'busy.svg';
		case 99:
			return 'invisible.svg';
		default:
			return 'online.svg';
	}	
}
function getStatus($status, $c){
	switch($status){
		case 2:
			return curStatus(statusTitle(2), statusIcon(2), $c);
		case 3:
			return curStatus(statusTitle(3), statusIcon(3), $c);
		default:
			return '';
	}
}
function listStatus($status){
	switch($status){
		case 1:
			return statusMenu(statusTitle(1), statusIcon(1));
		case 2:
			return statusMenu(statusTitle(2), statusIcon(2));
		case 3:
			return statusMenu(statusTitle(3), statusIcon(3));
		case 99:
			return statusMenu(statusTitle(99), statusIcon(99));
		default:
			return statusMenu(statusTitle(1), statusIcon(1));
	}
}
function listAllStatus(){
	$list = '';
	$list .= statusElement(1, statusTitle(1), statusIcon(1));
	$list .= statusElement(2, statusTitle(2), statusIcon(2));
	$list .= statusElement(3, statusTitle(3), statusIcon(3));
	if(canInvisible()){
		$list .= statusElement(99, statusTitle(99), statusIcon(99));
	}
	return $list;
}
function newStatusIcon($status){
	return 'default_images/status/' . statusIcon($status);
}
function curStatus($txt, $icon, $c){
	return '<img title="' . $txt . '" class="' . $c . '" src="default_images/status/' . $icon . '"/>';	
}
function statusMenu($txt, $icon){
	return '<div class="status_zone"><img class="status_icon" src="default_images/status/' . $icon . '"/></div><div class="status_text">' . $txt . '</div>';
}
function statusElement($val, $txt, $icon){
	return '<div class="status_option sub_item" onclick="updateStatus(' . $val . ');" data="' . $val . '">
				<div class="zone_status"><img class="icon_status" src="default_images/status/' . $icon . '"/></div>
				<div class="icon_text">' . $txt . '</div>
			</div>';
}

// system ranking define name and functions

function botRankTitle(){
	global $lang;
	return $lang['user_bot'];
}
function botRankIcon(){
	global $lang;
	return 'bot.svg';
}

function systemRank($rank, $type){
	switch($rank){
		case 50://vip Elite
		case 51://vip >vip_prime
		case 52://vip >vip_supreme
		case 60://premium Elite
		case 61://premium  prime
		case 62://premium  supreme
		case 69://bot    
		case 70://moderator
		case 80://admin
		case 90://superadmin
		case 100://owner
			return curRanking($type, rankTitle($rank), rankIcon($rank));
		default:
			return '';
	}
}
function rankIcon($rank){
	switch($rank){
		case 0:
			return 'guest.svg';
		case 1:
			return 'user.svg';
		case 50:
			return 'vip_elite.gif';
		case 51:
			return 'vip_prime.gif';
		case 52:
			return 'vip_supreme.gif';
		case 60:
			return 'premium_elite.gif';
		case 61:
			return 'premium_prime.gif';
		case 62:
			return 'premium_supreme.gif';
		case 69:
			return 'bot.svg';
		case 70:
			return 'mod.gif';
		case 80:
			return 'admin.gif';
		case 90:
			return 'super.gif';
		case 100:
			return 'owner.gif';
		default:
			return 'user.svg';
	}
}
function rankTitle($rank){
	global $lang;
	switch($rank){
		case 0:
			return $lang['guest'];
		case 1:
			return $lang['user'];
		case 50:
			return 'VIP Elite';
		case 51:
			return 'VIP Prime';
		case 52:
			return 'VIP Supreme';
		case 60:
			return 'Premium Elite';
		case 61:
			return 'Premium Prime';
		case 62:
			return 'Premium Supreme';
		case 69:
			return $lang['user_bot'];
		case 70:
			return $lang['mod'];
		case 80:
			return $lang['admin'];
		case 90:
			return $lang['super_admin'];
		case 100:
			return $lang['owner'];
		case 999:
			return $lang['nobody'];
		default:
			return $lang['user'];
	}
}


function roomRankTitle($rank){
	global $lang;
	switch($rank){
		case 6:
			return $lang['r_owner'];
		case 5:
			return $lang['r_admin'];
		case 4:
			return $lang['r_mod'];
		default:
			return $lang['user'];
	}
}
function roomRankIcon($rank){
	switch($rank){
		case 6:
			return 'room_owner.svg';
		case 5:
			return 'room_admin.svg';
		case 4:
			return 'room_mod.svg';
		default:
			return 'user.svg';
	}
}

function botRank($type){
	return curRanking($type, botRankTitle(), botRankIcon());
}

function proRanking($user, $type){
	if(isBot($user)){
		return proRank($type, botRankTitle(), botRankIcon());
	}
	else {
		switch($user['user_rank']){
			case 0://guest
			case 1://member
			case 50://vip Elite
			case 51://vip >vip_prime
			case 52://vip >vip_supreme
			case 60://premium Elite
			case 61://premium  prime
			case 62://premium  supreme
			case 69://bot 	    
			case 70://moderator
			case 80://admin
			case 90://superadmin
			case 100://owner
				return proRank($type, rankTitle($user['user_rank']), rankIcon($user['user_rank']));
			default:
				return '';
		}
	}
}
function roomRank($rank, $type){
	switch($rank){
		case 6:
		case 5:
		case 4:
			return curRanking($type, roomRankTitle($rank), roomRankIcon($rank));
		default:
			return '';
	}
}
function listRank($current, $req = 0){
	global $data;
	$rank = '';
	if($req == 1){
		$rank .= '<option value="0" ' . selCurrent($current, 0) . '>' . rankTitle(0) . '</option>';
	}
	$rank .= '<option value="1" ' . selCurrent($current, 1) . '>' . rankTitle(1) . '</option>';
	$rank .= '<option value="50" ' . selCurrent($current, 50) . '>' . rankTitle(50) . '</option>';
	$rank .= '<option value="51" ' . selCurrent($current, 51) . '>' . rankTitle(51) . '</option>';
	$rank .= '<option value="52" ' . selCurrent($current, 52) . '>' . rankTitle(52) . '</option>';
	$rank .= '<option value="60" ' . selCurrent($current, 60) . '>' . rankTitle(60) . '</option>';
	$rank .= '<option value="61" ' . selCurrent($current, 61) . '>' . rankTitle(61) . '</option>';
	$rank .= '<option value="62" ' . selCurrent($current, 62) . '>' . rankTitle(62) . '</option>';
	$rank .= '<option value="69" ' . selCurrent($current, 69) . '>' . rankTitle(69) . '</option>';
	$rank .= '<option value="70" ' . selCurrent($current, 70) . '>' . rankTitle(70) . '</option>';
	$rank .= '<option value="80" ' . selCurrent($current, 80) . '>' . rankTitle(80) . '</option>';
	$rank .= '<option value="90" ' . selCurrent($current, 90) . '>' . rankTitle(90) . '</option>';
	$rank .= '<option value="100" ' . selCurrent($current, 100) . '>' . rankTitle(100) . '</option>';
	$rank .= '<option value="999" ' . selCurrent($current, 999) . '>' . rankTitle(999) . '</option>';
	return $rank;
}
function listRankStaff($current){
	global $data;
	$rank = '';
	$rank .= '<option value="70" ' . selCurrent($current, 70) . '>' . rankTitle(70) . '</option>';
	$rank .= '<option value="80" ' . selCurrent($current, 80) . '>' . rankTitle(80) . '</option>';
	$rank .= '<option value="90" ' . selCurrent($current, 90) . '>' . rankTitle(90) . '</option>';
	$rank .= '<option value="100" ' . selCurrent($current, 100) . '>' . rankTitle(100) . '</option>';
	return $rank;
}

function changeRank($current){
	global $data, $cody;
	$rank = '';
	if(boomAllow($data['can_rank'])){
		$rank .= '<option value="1" ' . selCurrent($current, 1) . '>' . rankTitle(1) . '</option>';
		$rank .= '<option value="50" ' . selCurrent($current, 50) . '>' . rankTitle(50) . '</option>';
		$rank .= '<option value="51" ' . selCurrent($current, 51) . '>' . rankTitle(51) . '</option>';
		$rank .= '<option value="52" ' . selCurrent($current, 52) . '>' . rankTitle(52) . '</option>';
		$rank .= '<option value="60" ' . selCurrent($current, 60) . '>' . rankTitle(60) . '</option>';
		$rank .= '<option value="61" ' . selCurrent($current, 61) . '>' . rankTitle(61) . '</option>';
		$rank .= '<option value="62" ' . selCurrent($current, 62) . '>' . rankTitle(62) . '</option>';
		$rank .= '<option value="69" ' . selCurrent($current, 69) . '>' . rankTitle(69) . '</option>';
		$rank .= '<option value="70" ' . selCurrent($current, 70) . '>' . rankTitle(70) . '</option>';
	}
	if(boomAllow(100)){
		$rank .= '<option value="80" ' . selCurrent($current, 80) . '>' . rankTitle(80) . '</option>';
		$rank .= '<option value="90" ' . selCurrent($current, 90) . '>' . rankTitle(90) . '</option>';
	}
	return $rank;
}
function listRoomRank($current = 0){
	global $lang, $data;
	$rank = '';
	$rank .= '<option value="0" ' . selCurrent($current, 0) . '>' . $lang['none'] . '</option>';
	$rank .= '<option value="4" ' . selCurrent($current, 4) . '>' . roomRankTitle(4) . '</option>';
	$rank .= '<option value="5" ' . selCurrent($current, 5) . '>' . roomRankTitle(5) . '</option>';
	if(boomAllow(90)){
		$rank .= '<option value="6" ' . selCurrent($current, 6) . '>' . roomRankTitle(6) . '</option>';
	}
	return $rank;
}
function curRanking($type, $txt, $icon){
	return '<img src="default_images/rank/' . $icon . '" class="' . $type . '" title="' . $txt . '"/>';
}
function proRank($type, $txt, $icon){
	return '<img src="default_images/rank/' . $icon . '" class="' . $type . '"/> ' . $txt;
}
function getRankIcon($list, $type){
	if(isBot($list)){
		return botRank($type);
	}
	else if(haveRole($list['user_role']) && !isStaff($list['user_rank'])){
		return roomRank($list['user_role'], $type);
	}
	else {
		return systemRank($list['user_rank'], $type);
	}
}

// room access ranking functions

function roomAccessTitle($room){
	global $lang;
	switch($room){
		case 0:
			return $lang['public'];
		case 1:
			return $lang['members'];
		case 50:
			return $lang['vip'];
		case 60:
			return $lang['premium'];
		case 70:
			return $lang['mod'];
		case 80:
			return $lang['admin'];
		case 90:
			return $lang['super_admin'];
		case 100:
			return $lang['owner'];
		default:
			return $lang['public'];
	}
}
function roomAccessIcon($room){
	global $lang;
	switch($room){
		case 0:
			return 'public_room.svg';
		case 1:
			return 'member_room.svg';
		case 50:
			return 'vip_room.svg';
		case 60:
			return 'premium.svg';
		case 70:
			return 'mod_room.gif';
		case 80:
			return 'admin_room.svg';
		case 90:
			return 'superadmin_room.png';
		case 100:
			return 'owner_room.gif';
		default:
			return 'public_room.svg';
	}
}
function roomRanking($rank = 0){
	global $lang;
	$room_menu = '<option value="0" ' . selCurrent($rank, 0) . '>' . roomAccessTitle(0) . '</option>';
	if(boomAllow(1)){
		$room_menu .= '<option value="1" ' . selCurrent($rank, 1) . '>' . roomAccessTitle(1) . '</option>';
	}
	if(boomAllow(50)){ 
		$room_menu .= '<option value="50" ' . selCurrent($rank, 50) . '>' . roomAccessTitle(50) . '</option>';
	}
	if(boomAllow(60)){ 
		$room_menu .= '<option value="60" ' . selCurrent($rank, 60) . '>' . roomAccessTitle(60) . '</option>';
	}
	if(boomAllow(70)){ 
		$room_menu .= '<option value="70" ' . selCurrent($rank, 70) . '>' . roomAccessTitle(70) . '</option>';
	}	
	if(boomAllow(80)){ 
		$room_menu .= '<option value="80" ' . selCurrent($rank, 80) . '>' . roomAccessTitle(80) . '</option>';
	}
	if(boomAllow(90)){ 
		$room_menu .= '<option value="90" ' . selCurrent($rank, 90) . '>' . roomAccessTitle(90) . '</option>';
	}
	if(boomAllow(100)){ 
		$room_menu .= '<option value="100" ' . selCurrent($rank, 100) . '>' . roomAccessTitle(100) . '</option>';
	}
	return $room_menu;
}
function roomIcon($room, $type){
	global $lang;
	switch($room['access']){
		case 0:
		case 1:
		case 50:
		case 60:
		case 70:
		case 80:
		case 90:
		case 100:
			return roomIconTemplate($type, roomAccessTitle($room['access']), roomAccessIcon($room['access']));
		default:
			return roomIconTemplate($type, roomAccessTitle(0), roomAccessIcon(0));
	}
}
function roomIconTemplate($type, $txt, $icon){
	global $data;
	return '<img title="' . $txt . '" class="' . $type .  '" src="' . $data['domain'] . '/default_images/rooms/' . $icon . '">';	
}
function roomLock($room, $type){
	global $data, $lang;
	if($room['password'] != ''){
		return '<img title="' . $lang['password'] . '" class="' . $type .  '" src="' . $data['domain'] . '/default_images/rooms/locked_room.svg">';
	}
}

// can permission functions

function canEditRoom(){
    global $cody, $data;
	//if(boomRole(6) || boomAllow(70)){
	if(boomRole(6) || boomAllow($data['can_raction'])){
		return true;
	}
}

function canPrivate(){
	global $cody, $data;
	if(boomAllow($data['allow_private']) && !isPrivateMuted($data)){
		return true;
	}
}
function userCanPrivate($user){
	global $cody, $data;
	if(userBoomAllow($user, $data['allow_private']) && !isPrivateMuted($user)){
		return true;
	}
}

function mainBlocked(){
	if(mainMuted() || checkFlood()){
		return true;
	}
}
function canMain(){
	global $data;
	if(boomAllow($data['allow_main'])){
		return true;
	}
}
function canManageRoom(){
	if(boomRole(4) || boomAllow(70)){
		return true;
	}
}
function canPhotoFrame(){
	global $data;
	if(boomAllow($data['can_frame'])){
		return true;
	}
}
function canUploadChat(){
	global $data;
	if(boomAllow($data['allow_cupload'])){
		return true;
	}
}
function canUploadPrivate(){
	global $data;
	if(boomAllow($data['allow_pupload'])){
		return true;
	}
}
function canUploadWall(){
	global $data;
	if(boomAllow($data['allow_wupload'])){
		return true;
	}
}
function canCover(){
	global $data;
	if(boomAllow($data['allow_cover'])){
		return true;
	}
}
function canGifCover(){
	global $data;
	if(boomAllow($data['allow_gcover'])){
		return true;
	}
}
function canRoom(){
	global $data;
	if(boomAllow($data['allow_room'])){
		return true;
	}
}
function canEmo(){
	global $data;
	if(boomAllow($data['emo_plus'])){
		return true;
	}
}
function canName(){
	global $data;
	if(boomAllow($data['allow_name'])){
		return true;
	}
}
function canDirect(){
	global $data;
	if(boomAllow($data['allow_direct'])){
		return true;
	}
}
function userCanDirect($user){
	global $data;
	if(userBoomAllow($user, $data['allow_direct'])){
		return true;
	}
}
function canColor(){
	global $data;
	if(boomAllow($data['allow_colors'])){
		return true;
	}
}
function canGrad(){
	global $data;
	if(boomAllow($data['allow_grad'])){
		return true;
	}
}
function canNeon(){
	global $data;
	if(boomAllow($data['allow_neon'])){
		return true;
	}
}
function canFont(){
	global $data;
	if(useFont() && boomAllow($data['allow_font'])){
		return true;
	}
}
function canMood(){
	global $data;
	if(boomAllow($data['allow_mood'])){
		return true;
	}
}
function canVerify(){
	global $data;
	if(boomAllow($data['allow_verify'])){
		return true;
	}
}
function canHistory(){
	global $data;
	if(boomAllow($data['allow_history'])){
		return true;
	}
}
function canAvatar(){
	global $data;
	if(boomAllow($data['allow_avatar'])){
		return true;
	}
}
function canTheme(){
	global $data;
	if(boomAllow($data['allow_theme'])){
		return true;
	}
}
function canInfo(){
	global $cody;
	if(boomAllow($cody['can_edit_info'])){
		return true;
	}
}
function canAbout(){
	global $cody;
	if(boomAllow($cody['can_edit_about'])){
		return true;
	}
}
function canNameColor(){
	global $data;
	if(boomAllow($data['allow_name_color'])){
		return true;
	}
}
function canNameGrad(){
	global $data;
	if(boomAllow($data['allow_name_grad'])){
		return true;
	}
}
function canNameNeon(){
	global $data;
	if(boomAllow($data['allow_name_neon'])){
		return true;
	}
}
function canNameFont(){
	global $data;
	if(useFont() && boomAllow($data['allow_name_font'])){
		return true;
	}
}
function canInvisible(){
	global $data, $cody;
	if(boomAllow($cody['can_invisible'])){
		return true;
	}
}
function canPostNews(){
	global $data, $cody;
	if(boomAllow($cody['can_post_news'])){
		return true;
	}
}
function canModifyAvatar($user){
	global $data, $cody;
	if(!empty($user) && canAvatar() && canEditUser($user, $data['can_modavat'])){
		return true;
	}
}
function canModifyCover($user){
	global $data, $cody;
	if(!empty($user) && canCover() && canEditUser($user, $data['can_modcover'])){
		return true;
	}
}
function canModifyName($user){
	global $data, $cody;
	if(!empty($user) && canName() && canEditUser($user, $data['can_modname'])){
		return true;
	}
}
function canModifyMood($user){
	global $data, $cody;
	if(!empty($user) && canMood() && canEditUser($user, $data['can_modmood'])){
		return true;
	}
}
function canModifyAbout($user){
	global $data, $cody;
	if(!empty($user) && canEditUser($user, $data['can_modabout'])){
		return true;
	}
}
function canModifyEmail($user){
	global $data, $cody;
	if(!empty($user) && isMember($user) && isSecure($user) && canEditUser($user, $data['can_modemail'], 1)){
		return true;
	}
}
function canModifyColor($user){
	global $data, $cody;
	if(!empty($user) && canNameColor() && canEditUser($user, $data['can_modcolor'])){
		return true;
	}
}
function canModifyPassword($user){
	global $data, $cody;
	if(!empty($user) && isMember($user) && isSecure($user) && canEditUser($user, $data['can_modpass'], 1)){
		return true;
	}
}

function canUserHistory($user){
	global $data, $cody;
	if(!empty($user) && canEditUser($user, $cody['can_view_history'], 1)){
		return true;
	}
}
function canViewInvisible(){
	global $cody;
	if(boomAllow($cody['can_inv_view'])){
		return true;
	}
}
function canViewTimezone($user){
	global $data, $cody;
	if(canEditUser($user, $cody['can_view_timezone'], 1)){
		return true;
	}
}
function canViewEmail($user){
	global $data, $cody;
	if(userHaveEmail($user) && canEditUser($user, $cody['can_view_email'], 1)){
		return true;
	}
}
function canViewId($user){
	global $data, $cody;
	if(canEditUser($user, $cody['can_view_id'], 1)){
		return true;
	}
}
function canCritera($t){
	if(boomAllow($t)){
		return true;
	}
}
function canViewIp($user){
	global $data, $cody;
	if(canEditUser($user, $cody['can_view_ip'], 1)){
		return true;
	}
}
function canRoomPassword(){
	global $data, $cody;
	if(boomAllow($cody['can_room_pass']) || boomRole(6)){
		return true;
	}
}
function canBan(){
	global $data, $cody;
	if(boomAllow($data['can_ban'])){
		return true;
	}
}
function canBanUser($user){
	global $data, $cody;
	if(!empty($user) && canEditUser($user, $data['can_ban'], 1)){ 
		return true;
	}
}
function canRankUser($user){
	global $data, $cody;
	if(isOwner($user) || isGuest($user)){
		return false;
	}
	if(!empty($user) && canEditUser($user, $data['can_rank'], 0)){ 
		return true;
	}
}
function canDeleteUser($user){
	global $data, $cody;
	if(isOwner($user)){
		return false;
	}
	if(!empty($user) && canEditUser($user, $data['can_delete'], 1)){ 
		return true;
	}
}
function canWarn(){
	global $data;
	if(boomAllow($data['can_warn'])){
		return true;
	}
}
function canWarnUser($user){
	global $data;
	if(!empty($user) && canEditUser($user, $data['can_warn'], 1)){ 
		return true;
	}
}
function canManageDj(){
	global $data;
	if(boomAllow($data['can_dj'])){
		return true;
	}
}
function canKick(){
	global $data, $cody;
	if(boomAllow($data['can_kick'])){
		return true;
	}
}
function canKickUser($user){
	global $data, $cody;
	if(!empty($user) && canEditUser($user, $data['can_kick'], 1)){ 
		return true;
	}
}
function canDeleteNews($news){
	global $data, $cody;
	if(mySelf($news['news_poster'])){
		return true;
	}
	if(boomAllow($cody['can_delete_news']) && isGreater($news['user_rank'])){
		return true;
	}
}
function canDeleteNewsReply($reply){
	global $data, $cody;
	if(mySelf($reply['reply_uid'])){
		return true;
	}
	if(boomAllow($cody['can_delete_news']) && isGreater($reply['user_rank'])){
		return true;
	}
}
function canDeleteWall($wall){
	global $data, $cody;
	if(mySelf($wall['post_user'])){ 
		return true;
	}
	if(boomAllow($cody['can_delete_wall']) && isGreater($wall['user_rank'])){
		return true;
	}
}
function canDeleteWallReply($wall){
	global $data, $cody;
	if(mySelf($wall['reply_user'])){
		return true;
	}
	if(mySelf($wall['reply_uid'])){ 
		return true;
	}
	if(boomAllow($cody['can_delete_wall']) && isGreater($wall['user_rank'])){
		return true;
	}
}
function canDeleteLog(){
	global $cody;
	if(boomAllow(1) && boomAllow($cody['can_delete_logs'])){
		return true;
	}
}
function canDeleteSelfLog($p){
	global $data, $cody;
	if($p['user_id'] == $data['user_id'] && boomAllow($cody['can_delete_slogs'])){
		return true;
	}
}
function canReport(){
	global $cody;
	if(boomAllow($cody['can_report'])){
		return true;
	}
}
function canManageReport(){
	global $cody;
	if(boomAllow($cody['can_manage_report'])){
		return true;
	}
}
function selfManageReport($id){
	global $cody;
	if(!mySelf($id)){
		return true;
	}
	if(mySelf($id) && boomAllow($cody['can_self_report'])){
		return true;
	}
}
function canDeletePrivate(){
	global $cody;
	if(boomAllow($cody['can_delete_private'])){
		return true;
	}
}
function canDeleteRoomLog(){
	if(boomAllow(1) && boomRole(4)){
		return true;
	}
}
function canClearRoom(){
	global $cody;
	if(boomAllow($cody['can_clear_room'])){
		return true;
	}
}
function canViewGold(){
	global $data;
	if(useGold() && boomAllow($data['can_vgold'])){
		return true;
	}
}
// DO NOT MODIFY THE MUTE PERMISSION THIS WILL MAKE CONFLICT IN THE SYSTEM.
// permission functions
function canMute(){
	global $data;
	if(boomAllow($data['can_mute'])){
		return true;
	}
}
function canMuteUser($user){
	global $data, $cody;
    if(!empty($user) && canEditUser($user, $data['can_mute'], 1)){ 
		return true;
	}
}

function fileFlood(){
	global $cody;
	$f = basename($_SERVER['PHP_SELF']);
	$t1 = round(microtime(true)*1000);
	$t2 = round(microtime(true)*1000) - 500;
	
	if(isset($_SESSION[BOOM_PREFIX . 'ufile'], $_SESSION[BOOM_PREFIX . 'ufiletime'])){
		if($_SESSION[BOOM_PREFIX . 'ufile'] == $f && $_SESSION[BOOM_PREFIX . 'ufiletime'] >= $t2){
			return true;
		}
		else {
			$_SESSION[BOOM_PREFIX . 'ufiletime'] = $t1;
			$_SESSION[BOOM_PREFIX . 'ufile'] = $f;
			return false;
		}
	}
	else {
		$_SESSION[BOOM_PREFIX . 'ufiletime'] = $t1;
		$_SESSION[BOOM_PREFIX . 'ufile'] = $f;
		return false;
	}
}
function blackNotify(){
	global $mysqli, $data, $user,$db;
	$id = escape($_POST['get_profile']);
	$inline_css = 'color: #fff;background: green;padding: 2px 8px;border-radius: 2px;font-size: 12px;';
	$template = '<span onclick="getProfile('. $data['user_id'] .');" style="'. $inline_css .'"><i class="ri-eye-line"></i> Open profile</span>';
	if(isset($id) && !mySelf($user['user_id']) && isOwner($user)){
	    $db->where('notifier', $data['user_id']);
	    $db->where('notified', $user["user_id"]);
	    $db->where('notify_type', 'profile_visit');
        $stats = $db->getOne("notification");
	     if($stats) {
    	    $db->where('notifier', $data['user_id']);
    	    $db->where('notified', $user["user_id"]);
    	    $db->where('notify_type', 'profile_visit');
            $updateResult = $db->update('notification', array(
                'notify_date' => time(),
                'notify_view' => 0,
            ));
            updateNotify($id);
	    } else {
	         boomNotify("profile_visit", array("hunter" => $data['user_id'], "target" => $user["user_id"], "custom" => $template));   
	    }
//boomNotify("profile_visit", array("hunter" => $data['user_id'], "target" => $user["user_id"], "custom" => $template));   	
		
	}
}




/*fake users*/
function get_fake_users(){
    global $db, $data, $lang;
	$res = array();
	$db->where('user_bot', "1");
	$bots = $db->get('users');
	if($db->count > 0){
		 foreach ($bots as $bot){
			 $userdata = userDetails($bot['user_id']);
			 $res[] = $userdata;
		}	   
	}
	 return $res;
}
function bot_list_by_room($group_id){
    global $db,$data;
	$res = array();
	$list = '';
	$db->where('group_id', $group_id);
    $bots = $db->get('bot_data');
    if ($db->count > 0){
		foreach ($bots as $bot){
			$userdata =  userDetails($bot['user_id']);
			 $v['id'] =  cleanString($bot['id']);
			 $v['bot_name'] =  $userdata['user_name'];
			 $v['user_tumb'] =  $userdata['user_tumb'];
			 $v['reply'] =  cleanString($bot['reply']);
			 $v['view'] =  cleanString($bot['view']);
			 $v['fuse_bot_status'] =  cleanString($bot['fuse_bot_status']);
			 $v['fuse_bot_time'] =  cleanString($bot['fuse_bot_time']);
			 $v['fuse_bot_type'] =  cleanString($bot['fuse_bot_type']);
			 $v['user_id'] =  cleanString($bot['user_id']);
			 $v['group_id'] =  cleanString($bot['group_id']);
            $v['user_font'] 		= $userdata['user_font'];
            $v['user_color'] 		= $userdata['user_color'];
            $v['bccolor'] =  	    $userdata['bccolor'];
            $v['bcbold'] =  		$userdata['bcbold'];
            $v['bcfont'] =  		$userdata['bcfont'];
			 
			 $list .= boomTemplate('element/bots/bot_item', $v);
		}
		return $list;
    }
}
function bot_informatin($bot_id,$group_id){
global $db,$data;
	$list = '';
	$db->where('id', $bot_id);
	$db->where('group_id', $group_id);
    $bot_data = $db->getOne('bot_data');
    if (empty($bot_data)) {
        return false;
    }
	$user_data 					= userDetails($bot_data['user_id']);
	$user_info['bot_id']            = $bot_data['id'];
	$user_info['bot_name']          = $user_data['user_name'];
	$user_info['bot_room']          = $bot_data['group_id'];
	$user_info['bot_time'] 			= $bot_data['fuse_bot_time'];
	$user_info['bot_status'] 		= $bot_data['fuse_bot_status'];
	$user_info['bot_reply'] 		= $bot_data['reply'];
	$user_info['bot_type'] 			= $bot_data['fuse_bot_type'];
	$user_info['user_id'] 			= $user_data['user_id'];
	$user_info['user_font'] 		= $user_data['user_font'];
	$user_info['user_color'] 		= $user_data['user_color'];
	$user_info['bccolor'] =  	    $user_data['bccolor'];
	$user_info['bcbold'] =  		$user_data['bcbold'];
	$user_info['bcfont'] =  		$user_data['bcfont'];
    $list .= boomTemplate('element/bots/edit_bot', $user_info);
	 return $list;
}
function bot_data($bot_id,$group_id){
    global $db,$data;
	$db->where('id', $bot_id);
	$db->where('group_id', $group_id);
    $bot_data = $db->getOne('bot_data');
    if (empty($bot_data)) {
        return false;
    }    
	$user_data 					= fuse_user_data($bot_data['user_id']);
	$user_info['bot_id']            = $bot_data['id'];
	$user_info['bot_name']          = $user_data['user_name'];
	$user_info['group_id']          = $bot_data['group_id'];
	$user_info['bot_delay'] 		= $bot_data['fuse_bot_delay'];
	$user_info['bot_time'] 			= $bot_data['fuse_bot_time'];
	$user_info['bot_status'] 		= $bot_data['fuse_bot_status'];
	$user_info['bot_reply'] 		= $bot_data['reply'];
	$user_info['bot_type'] 			= $bot_data['fuse_bot_type'];
	$user_info['user_id'] 			= $user_data['user_id'];
	 return $user_info;
}
//update single bot data

function cl_update_bot_data($bot_id = null,$res = array()) {
    global $db,$data;
    if ((not_num($bot_id)) || (empty($res) || is_array($res) != true)) {
        return false;
    } 
    $query     = $db->where('id', $bot_id);
    $update = $db->update("bot_data",$res);
    return ($update == true) ? true : false;
}

function fuse_user_data($user_id = 0) {
    global $db, $data, $mysqli;

    // Check if $user_id is a valid number
    if (!is_numeric($user_id) || $user_id <= 0) {
        return false; // Invalid user ID
    }
    // Get the user data where user_id matches
    $db->where('user_id', $user_id);
    $user_data = $db->getOne('users');

    // Return false if no user data found
    if (empty($user_data)) {
        return false;
    }

    // Return the retrieved user data
    return $user_data;
}

function ignoring($user){
	if($user['ignoring'] > 0){
		return true;
	}
}

function userFullDetails($id, $room = '') {
    global $mysqli, $data;
    // Set the default room value if not provided
    if ($room == '') {
        $room = $data['user_roomid'];
    }
    // Fetch the basic user details (assuming this function is defined elsewhere)
    $user = userDetails($id);
    if (!empty($user)) {
        // Define the queries and their bind parameters
        $queries = [
            [
                'query' => "SELECT IFNULL(fstatus, 0) FROM boom_friends WHERE hunter = ? AND target = ?",
                'params' => [$data['user_id'], $id],
                'resultKey' => 'friendship'
            ],
            [
                'query' => "SELECT COUNT(ignore_id) FROM boom_ignore WHERE ignorer = ? AND ignored = ?",
                'params' => [$id, $data['user_id']],
                'resultKey' => 'ignored'
            ],
            [
                'query' => "SELECT COUNT(ignore_id) FROM boom_ignore WHERE ignorer = ? AND ignored = ?",
                'params' => [$data['user_id'], $id],
                'resultKey' => 'ignoring'
            ],
            [
                'query' => "SELECT IFNULL(action_muted, 0) FROM boom_room_action WHERE action_user = ? AND action_room = ?",
                'params' => [$id, $room],
                'resultKey' => 'room_muted'
            ],
            [
                'query' => "SELECT IFNULL(action_blocked, 0) FROM boom_room_action WHERE action_user = ? AND action_room = ?",
                'params' => [$id, $room],
                'resultKey' => 'room_blocked'
            ],
            [
                'query' => "SELECT IFNULL(room_rank, 0) FROM boom_room_staff WHERE room_staff = ? AND room_id = ?",
                'params' => [$id, $room],
                'resultKey' => 'room_ranking'
            ]
        ];
        $userData = [];
        // Iterate over each query, bind parameters, and fetch the result
        foreach ($queries as $q) {
            $stmt = $mysqli->prepare($q['query']);
            $stmt->bind_param(str_repeat('i', count($q['params'])), ...$q['params']);
            $stmt->execute();
            $stmt->bind_result($result);
            $stmt->fetch();
            $userData[$q['resultKey']] = $result;
            $stmt->close();
        }
        // Merge the fetched data with the user data
        return array_merge($user, $userData);
    }
    return [];  // Return an empty array if the user is not found or no data
}

function userRelationDetails($id){
    global $mysqli, $data;
    // Fetch user details
    $user = userDetails($id);
    if(!empty($user)){
        // Define the queries in an array
        $queries = [
            [
                'query' => "SELECT IFNULL(fstatus, 0) FROM boom_friends WHERE hunter = ? AND target = ?",
                'params' => [$data['user_id'], $id],
                'resultKey' => 'friendship'
            ],
            [
                'query' => "SELECT count(ignore_id) FROM boom_ignore WHERE ignorer = ? AND ignored = ?",
                'params' => [$id, $data['user_id']],
                'resultKey' => 'ignored'
            ],
            [
                'query' => "SELECT count(ignore_id) FROM boom_ignore WHERE ignorer = ? AND ignored = ?",
                'params' => [$data['user_id'], $id],
                'resultKey' => 'ignoring'
            ]
        ];
        // Prepare and execute each query
        $userData = [];
        foreach ($queries as $query) {
            $stmt = $mysqli->prepare($query['query']);
            // Bind the parameters
            $stmt->bind_param(str_repeat('i', count($query['params'])), ...$query['params']);
            // Execute the query
            $stmt->execute();
            // Get the result and store it in the result key
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $userData[$query['resultKey']] = $result->fetch_row()[0];
            } else {
                $userData[$query['resultKey']] = 0;
            }
            $stmt->close();
        }
        // Merge the user details and the additional data
        return array_merge($user, $userData);
    }
    return [];  // Return an empty array if no user found or no data
}

function setUserRoom(){
 	global $data, $mysqli;
	$room = myRoomDetails($data['user_roomid']);
    $mysqli->query("UPDATE boom_users SET user_roomid = '{$data['user_roomid']}', last_action = '" . time() . "', room_mute = '{$room['room_muted']}', user_role = '{$room['room_ranking']}' WHERE user_id = '{$data['user_id']}'"); 
     //redisInitUser($data);
}
function myRoomDetails($r){
    global $mysqli, $data;
    // Fetch room details
    $room = roomDetails($r);
    if(!empty($room)){
        // Define the queries in an array
        $queries = [
            [
                'query' => "SELECT IFNULL(action_muted, 0) FROM boom_room_action WHERE action_user = ? AND action_room = ?",
                'params' => [$data['user_id'], $r],
                'resultKey' => 'room_muted'
            ],
            [
                'query' => "SELECT IFNULL(action_blocked, 0) FROM boom_room_action WHERE action_user = ? AND action_room = ?",
                'params' => [$data['user_id'], $r],
                'resultKey' => 'room_blocked'
            ],
            [
                'query' => "SELECT IFNULL(room_rank, 0) FROM boom_room_staff WHERE room_staff = ? AND room_id = ?",
                'params' => [$data['user_id'], $r],
                'resultKey' => 'room_ranking'
            ]
        ];
        // Prepare and execute each query
        $roomData = [];
        foreach ($queries as $query) {
            $stmt = $mysqli->prepare($query['query']);
            // Bind the parameters
            $stmt->bind_param(str_repeat('i', count($query['params'])), ...$query['params']);
            // Execute the query
            $stmt->execute();
            // Get the result and store it in the result key
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $roomData[$query['resultKey']] = $result->fetch_row()[0];
            } else {
                $roomData[$query['resultKey']] = 0;
            }
            $stmt->close();
        }
        // Merge the room details and the additional data
        return array_merge($room, $roomData);
    }
    return [];  // Return an empty array if no room found or no data
}

function roomDetails($id){
    global $mysqli;
    $room = [];
    // Prepare the SQL query
    $stmt = $mysqli->prepare("SELECT * FROM boom_rooms WHERE room_id = ?");
    // Bind the parameter
    $stmt->bind_param('i', $id);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result = $stmt->get_result();
    // Check if any rows were returned
    if($result->num_rows > 0){
        // Fetch the result as an associative array
        $room = $result->fetch_assoc();
    }
    // Close the prepared statement
    $stmt->close();
    
    return $room;
}

/* wallet and gold functions */
function useWallet(){
	global $data;
	if($data['use_wallet'] > 0){
		return true;
	}
}

function useGold(){
	global $data;
	if($data['use_gold'] > 0){
		return true;
	}
}
function canGold(){
	global $data;
	if(boomAllow($data['can_gold'])){
		return true;
	}
}
function canReceiveGold($user){
	global $data;
	if(!isBot($user) && userBoomAllow($user, $data['can_rgold'])){
		return true;
	}
}
function canShareGold($user){
	global $data;
	if(!useGold() || isBot($user) || ignored($user) || ignoring($user) || !userBoomAllow($user, $data['can_rgold'])){
		return false;
	}
	if(boomAllow($data['can_sgold'])){
		return true;
	}
}
function goldBalance($gold){
	global $data;
	if($data['user_gold'] >= $gold){
		return true;
	}
}
function addGold($user, $gold){
    global $mysqli;
    // Prepare the SQL query
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_gold = user_gold + ? WHERE user_id = ?");
    // Bind the parameters
    $stmt->bind_param('ii', $gold, $user['user_id']);
    // Execute the query
    $stmt->execute();
    // Close the prepared statement
    $stmt->close();
	//redisUpdateUser($user['user_id']);
}
function removeGold($user, $gold){
    global $mysqli;
    // Prepare the SQL query
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_gold = user_gold - ? WHERE user_id = ?");
    // Bind the parameters
    $stmt->bind_param('ii', $gold, $user['user_id']);
    // Execute the query
    $stmt->execute();
    // Close the prepared statement
    $stmt->close();
	//redisUpdateUser($user['user_id']);
}
function maxGoldShare(){
	return 50000;
}
function validGold($n){
	if($n > 0 && $n <= maxGoldShare() && $n % 100 == 0){
		return true;
	}
}

/* gift functions */
function useStore(){
	global $data;
	if($data['use_store'] > 0){
		return true;
	}
}
function useGift(){
	global $data;
	if($data['use_gift'] > 0){
		return true;
	}
}
function canSendGift($user){
	if(!useGift()){
		return false;
	}
	if(isBot($user)){
		return false;
	}
	if(ignored($user) || ignoring($user)){
		return false;
	}
	return true;
}
function giftDetails($id){
    global $mysqli;
    $gift = [];
    // Prepare the SQL query
    $stmt = $mysqli->prepare("SELECT * FROM boom_gift WHERE id = ?");
    // Bind the parameter
    $stmt->bind_param('i', $id);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result = $stmt->get_result();
    // Fetch the data if available
    if($result->num_rows > 0){
        $gift = $result->fetch_assoc();
    }
    // Close the prepared statement
    $stmt->close();
    return $gift;
}

function giftRecord($user, $gift){
    global $mysqli;
    // Get the current timestamp
    $currentTime = time();	
    // Check if the user has already received this gift
    $stmt = $mysqli->prepare("SELECT id FROM boom_users_gift WHERE target = ? AND gift = ?");
    $stmt->bind_param('ii', $user['user_id'], $gift['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        // Update the record if the gift already exists
        $stmt_update = $mysqli->prepare("UPDATE boom_users_gift SET gift_count = gift_count + 1, gift_date = ? WHERE target = ? AND gift = ?");
        $stmt_update->bind_param('iii', $currentTime, $user['user_id'], $gift['id']);
        $stmt_update->execute();
    } else {
        // Insert a new record if the gift doesn't exist
        $stmt_insert = $mysqli->prepare("INSERT INTO boom_users_gift (target, gift, gift_date) VALUES (?, ?, ?)");
        $stmt_insert->bind_param('iii', $user['user_id'], $gift['id'], $currentTime);
        $stmt_insert->execute();
    }
    // Close the prepared statements
    $stmt->close();
    if (isset($stmt_update)) $stmt_update->close();
    if (isset($stmt_insert)) $stmt_insert->close();
}
function gift_list_byId($gift_id){
    global $mysqli, $data, $lang;
    // Escape and clean the gift_id
    $gift_id = escape($gift_id);
    $gift_id = cleanString($gift_id);
    // Prepare the query
    $stmt = $mysqli->prepare("SELECT * FROM `boom_gift` WHERE id = ? ORDER BY `time` DESC LIMIT 1");
    // Bind parameters and execute the query
    $stmt->bind_param('i', $gift_id);
    $stmt->execute();
    // Get the result
    $result = $stmt->get_result();
    // Check if the result contains any rows
    if ($result->num_rows > 0) {
        // Fetch the row and prepare the data
        $row = $result->fetch_assoc();
        $fu_gifts = [
            'gift_id'    => $row['id'],
            'gift_title' => htmlspecialchars_decode(stripcslashes(cl_rn_strip($row['gift_title'])), ENT_QUOTES),
            'gift_cost'  => $row['gift_cost'],
            'gift_thumb' => $row['gift_image'],
            'gift_url'   => cleanString($data['domain'].'/system/gifts/files/media/'.$row['gift_image']),
            'gif_file'   => cleanString($data['domain'].'/system/gifts/files/media/'.$row['gif_file']),
            'gift_rank'  => $row['gift_rank']
        ];
        // Return the gift data
        return $fu_gifts;
    }
    // If no result is found, return an empty array
    return [];
}

function gift_notification(){
    global $db,$data,$mysqli;
    $fu_gifts = array();
    //SELECT new gift for target user and make it visible for 10 seconde
    $get_gift = $mysqli->query("SELECT * FROM `boom_users_gift` WHERE `gift_date` >= UNIX_TIMESTAMP(NOW()) - 10 AND target  = '{$data['user_id']}' ORDER BY `gift_date` DESC LIMIT 1;");
	if($get_gift->num_rows > 0){
		$gift = $get_gift->fetch_assoc();
		$fu_gifts['gift_data'] = gift_list_byId($gift['gift']);
		
	}
	return $fu_gifts;
}
function giftContentSendedOk($gift_data, $from, $to) {
    $gift_array = $gift_data;
    $gift_source = '<img src="'.$gift_array['gift_url'].'" class="gifts_chat"/>';
	$content = '<div onclick="play_gift(this)"
    	data-src="'.$gift_array['gif_file'].'"
    	data-to="'.$to.'"
    	data-from="'.$from.'"
    	data-price="'.$gift_array['gift_cost'].'"
    	data-gname="'.$gift_array['gift_title'].'"
    	data-icon="'.$gift_array['gift_url'].'"
    	class="gift_contianer">
    	<font color="red"> (Click to Play)</font>
    	<div class="gift_block">'. $gift_source.'</div>
    	<font color="red">'.$from.'</font>
    	<font color="orange"> Send '.$gift_array['gift_title'].'</font>
    	<font color="green">To '.$to.'</font>
	</div>';
	return $content;
}

function extra_users_list($list,$list_type =0){
  	global $data, $lang;
	//if(!isVisible($list)){
	//	return false;
	//}
	$icon = '';
	$muted = '';
	$status = '';
	$mood = '';
	$flag = '';
	$sex_icon = '';
	$offline = 'offline';
  
    if ($list['last_action'] > getDelay() || isBot($list)) {
        $offline = '';
        $status = getStatus($list['user_status'], 'list_status');
    }

 	if(!empty( userGender($list['user_sex']))){
		$sex_icon = '<div class="user_item_sex">'.avGender_icon($list['user_sex']).'</div>';
	}
	if(!empty($list['user_mood'])){
		$mood = '<p class="text_xsmall bustate bellips">' . $list['user_mood'] . '</p>';
	}
     return '<label for="'.$list['user_name'].'" class="avtrig user_item confirm_uid '.$offline.' user_data_'.$list['user_id'].'">
                <input id="'.$list['user_name'].'" type="radio" name="user_selection" value="'.$list['user_id'].'" data-uname="'.$list['user_name'].'" style="display: none;">
                <div class="user_item_avatar">
                    <img class="avav acav '.avGender($list['user_sex']).' '.ownAvatar($list['user_id']).'" src="'.myAvatar($list['user_tumb']).'" alt="User Avatar"/> 
                    '.$status.'
                </div>
                <div class="user_item_data">
                    <p class="username '.myColorFont($list).'">'.$list["user_name"].'</p>
                    '.$mood.'
                </div>
               '. $muted . $icon . $sex_icon . '
            </label>';	

 }
//will deleted
function runGiftSearch($q){
    global $mysqli, $data, $lang;
    // Escape the search string
    $search_string = escape($q);
    // Prepare the query with a wildcard search for the user_name
    $stmt = $mysqli->prepare("SELECT * FROM boom_users WHERE user_name LIKE ? LIMIT 5");
    // Bind the parameter for the search string
    $search_pattern = "%$search_string%";
    $stmt->bind_param('s', $search_pattern);
    // Execute the query
    $stmt->execute();
    // Get the result
    $result_array = $stmt->get_result();
    // Check if there are any results
    if ($result_array->num_rows > 0) {
        $list = "";
        // Loop through the results and build the list
        foreach ($result_array as $result) {
            $list .= extra_users_list($result);
        }
        return $list;
    } else {
        // Return a message if nothing is found
        return emptyZone($lang["nothing_found"]);
    }
}

//will deleted

function gif_list(){
    global $mysqli,$db,$data, $lang;
    $gift_arr = array();
	$gifts = $db->get('gift');
	if($db->count > 0){
		 foreach ($gifts as $gift){
			$fu_gifts['gift_id'] =          $gift['id'];
			$fu_gifts['gift_name']        	= cl_rn_strip($gift['gift_title']);  
			$fu_gifts['gift_name']        	= stripcslashes($gift['gift_title']);  
			$fu_gifts['gift_name']        	= htmlspecialchars_decode($gift['gift_title'], ENT_QUOTES);   
			$fu_gifts['price']				= $gift['gift_cost'];
			$fu_gifts['gift_thumb']			= $gift['gift_image'];
			$fu_gifts['gift_url'] = 		cleanString($data['domain'].'/system/gifts/files/media/'.$gift['gift_image']);
			$fu_gifts['video_file'] = 		cleanString($data['domain'].'/system/gifts/files/media/'.$gift['video_file']);
			$fu_gifts['gif_file'] = 		cleanString($data['domain'].'/system/gifts/files/media/'.$gift['gif_file']);
            $gift_arr[] = $fu_gifts;
		     
		}
		return $gift_arr;
	} 
	 
}
//will deleted
function record_gift($gdata){
    global $mysqli;
    // Extract the necessary data from the input array
    $target_id = $gdata['target_id'];
    $gift_id = $gdata['gift_id'];
    $room_id = $gdata['room_id'];
    $hunter_id = $gdata['hunter_id'];
    // Prepare the query to check if the gift has already been recorded
    $stmt = $mysqli->prepare("SELECT id FROM boom_users_gift WHERE target = ? AND gift = ?");
    $stmt->bind_param('ii', $target_id, $gift_id);  // 'ii' because both target and gift are integers
    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    // Check if the gift already exists in the database
    if ($result->num_rows > 0) {
        // If it exists, update the gift count and gift date
        $update_stmt = $mysqli->prepare("UPDATE boom_users_gift SET gift_count = gift_count + 1, gift_date = ? WHERE target = ? AND gift = ?");
        $current_time = time();
        $update_stmt->bind_param('iii', $current_time, $target_id, $gift_id);
        $update_stmt->execute();
    } else {
        // If it doesn't exist, insert a new record
        $insert_stmt = $mysqli->prepare("INSERT INTO boom_users_gift (target, hunter, room_id, gift, gift_date) VALUES (?, ?, ?, ?, ?)");
        $current_time = time();
        $insert_stmt->bind_param('iiiii', $target_id, $hunter_id, $room_id, $gift_id, $current_time);
        $insert_stmt->execute();
    }
}
/******************* start ghost function********************/
// ghost function
function systemSpamGhost($user, $custom = ''){
	global $data,$cody;
	if(isGhosted($user)){
		return false;
	}
	if(!isStaff($user['user_rank']) && !isBot($user)){
		systemGhost($user, $data['spam_delay']);
		boomHistory('spam_ghost', array('hunter'=> $cody['system_id'], 'target'=> $user['user_id'], 'delay'=> $data['spam_delay'], 'reason'=> $custom));
		boomConsole('spam_ghost', array('hunter'=>$cody['system_id'], 'target'=> $user['user_id'], 'reason'=> $custom, 'delay'=> $data['spam_delay']));
	}
}
function userIsGhosted($id){
    global $mysqli;
    // Prepare the query to retrieve the user ghost status
    $stmt = $mysqli->prepare("SELECT user_ghost FROM boom_users WHERE user_id = ?");
    $stmt->bind_param('i', $id);  // 'i' because user_id is an integer
    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();
    // Check if the user exists and is ghosted
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (isGhosted($user)) {
            return true;
        }
    }
    return false;
}
function systemGhost($user, $delay) {
    global $mysqli;
    $ghost_end = max($user['user_ghost'], time() + ($delay * 60));
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_ghost = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $ghost_end, $user['user_id']);
    return $stmt->execute() && $stmt->affected_rows > 0;
}
function systemUnghost($user){
    global $mysqli;
	$ghost_value = 0;
    // Prepare the query to set user_ghost to 0
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_ghost = ? WHERE user_id = ?");
    // Bind parameters (0 for un-ghosting, user_id as integer)
    $stmt->bind_param('ii', $ghost_value, $user['user_id']);
    // Execute the query
    $stmt->execute();
    // Optionally update the Redis cache
    // redisUpdateUser($user['user_id']);
}
function canGhost(){
	global $data;
	if(boomAllow($data['can_ghost'])){
		return true;
	}
}
function validGhost($val){
	if(in_array($val, ghostValues())){
		return true;
	}
}
function canViewGhost(){
	global $data;
	if(boomAllow($data['can_vghost'])){
		return true;
	}
}
function isGhosted($user){
	if($user['user_ghost'] > time()){
		return true;
	}
}
function isWarned($user){
	if(!empty($user['warn_msg'])){
		return true;
	}
}
function canGhostUser($user){
	global $data;
	if(!empty($user) && canEditUser($user, $data['can_ghost'], 1) && !isStaff($user['user_rank'])){ 
		return true;
	}
}
function ghostAccount($id, $delay, $reason = '') {
    global $mysqli, $data;
    if (!validGhost($delay)) {
        return ['status' => 0, 'error' => 'Invalid ghost duration'];
    }
    $user = userDetails($id);
    if (empty($user)) {
        return ['status' => 0, 'error' => 'User not found'];
    }
    if (!canGhostUser($user)) {
        return ['status' => 0, 'error' => 'No permission'];
    }
    if (isGhosted($user)) {
        return ['status' => 0, 'error' => 'Already ghosted'];
    }
    if (!systemGhost($user, $delay)) {
        return ['status' => 0, 'error' => 'Ghost failed'];
    }
    boomHistory('ghost', [
        'target' => $user['user_id'],
        'delay' => $delay,
        'reason' => $reason,
        'hunter' => $data['user_id']
    ]);
    return ['status' => 1, 'message' => 'Ghost applied'];
}
function unghostAccount($id){
	global $mysqli;
	$user = userDetails($id);
	if(empty($user)){
		return 3;
	}
	if(!canGhostUser($user)){
		return 0;
	}
	if(!isGhosted($user)){
		return 2;
	}
	systemUnghost($user);
	boomConsole('unghost', array('target'=> $user['user_id']));
	return 1;
}
/******************* End ghost function********************/
function muteAccountMain($id, $delay, $reason = '') {
    global $mysqli, $data;
    // 1. Verify user exists
    $user = userDetails($id);
    if (!$user) {
        return ['status' => 0, 'code' => 3, 'error' => 'User not found'];
    }
    // 2. Check permissions
    if (!canMuteUser($user)) {
        return ['status' => 0, 'code' => 0, 'error' => 'Mute not allowed'];
    }
    // 3. Check existing mutes
    if (isMuted($user) || isMainMuted($user)) {
        return ['status' => 0, 'code' => 2, 'error' => 'User already muted'];
    }
    // 4. Apply mute
    if (!systemMainMute($user, $delay, $reason)) {
        return ['status' => 0, 'error' => 'Mute failed'];
    }
    // 5. Trigger events
	boomNotify('main_mute', [ 'target' => $user['user_id'], 'source' => 'mute', 'reason' => $reason, 'delay' => $delay, 'icon' => 'action' ]);
	boomHistory('main_mute', [ 'target' => $user['user_id'], 'delay' => $delay, 'reason' => $reason ]);
	boomConsole('main_mute', [ 'target' => $user['user_id'], 'reason' => $reason, 'delay' => $delay ]);
    return ['status' => 1, 'message' => 'Main chat mute applied'];
}
function unmuteAccountMain($id){
	global $mysqli;
	$user = userDetails($id);
	if(empty($user)){
		return 3;
	}
	if(!canMuteUser($user)){
		return 0;
	}
	if(!isMainMuted($user)){
		return 2;
	}
	systemMainUnmute($user);
	boomNotify('main_unmute', array('target'=> $user['user_id'], 'source'=> 'mute', 'icon'=> 'raction'));
	boomConsole('main_unmute', array('target'=> $user['user_id']));
	return 1;
}
function muteAccountPrivate($id, $delay, $reason = '') {
    global $mysqli, $data, $lang;
    // 1. Get user details
    $user = userDetails($id);
    if (empty($user)) {
		return [ 'status' => 0, 'code' => 404, 'error' => $lang['user_not_found'] ?? 'User not found' ];
    }
    // 2. Permission check
    if (!canMuteUser($user)) {
		return [ 'status' => 0, 'code' => 403, 'error' => $lang['no_mute_permission'] ?? 'No mute permission' ];
    }
    // 3. Check existing mutes
    if (isPrivateMuted($user)) {
		return [ 'status' => 0, 'code' => 409, 'error' => $lang['already_muted'] ?? 'User already muted' ];
    }
    // 4. Apply mute
    $mute_end = time() + ($delay * 60);
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_pmute = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $mute_end, $user['user_id']);
    if (!$stmt->execute()) {
		return [ 'status' => 0, 'code' => 500, 'error' => $lang['db_error'] ?? 'Database operation failed' ];
    }
    // 5. Only proceed if update was successful
    if ($stmt->affected_rows > 0) {
        // Trigger notifications
		boomNotify('private_mute', [ 'target' => $user['user_id'], 'source' => 'mute', 'reason' => $reason, 'delay' => $delay, 'icon' => 'action' ]);
		boomHistory('private_mute', [ 'target' => $user['user_id'], 'delay' => $delay, 'reason' => $reason, 'hunter' => $data['user_id'] ]);
		boomConsole('private_mute', [ 'target' => $user['user_id'], 'reason' => $reason, 'delay' => $delay ]);
        // Return success
        return [
            'status' => 1,
            'code' => 200,
            'message' => $lang['mute_success'] ?? 'Private mute applied',
			'data' => [ 'user_id' => $user['user_id'], 'mute_until' => date('Y-m-d H:i:s', $mute_end), 'duration' => $delay ]
        ];
    }
    return [
        'status' => 0,
        'code' => 500,
        'error' => $lang['mute_failed'] ?? 'Failed to apply mute'
    ];
}
/* function muteAccountPrivate($id, $delay, $reason = ''){
	global $mysqli;
	$user = userDetails($id);
	if(empty($user)){
		return 3;
	}
	if(!canMuteUser($user)){
		return 0;
	}
	if(isMuted($user) || isPrivateMuted($user)){
		return 2;
	}
	systemPrivateMute($user, $delay);
	boomNotify('private_mute', array('target'=> $user['user_id'], 'source'=> 'mute', 'reason'=> $reason, 'delay'=> $delay, 'icon'=> 'action'));
	boomHistory('private_mute', array('target'=> $user['user_id'], 'delay'=> $delay, 'reason'=> $reason));
	boomConsole('private_mute', array('target'=> $user['user_id'], 'reason'=>$reason, 'delay'=> $delay));
	return 1;
} */
function unmuteAccountPrivate($id){
	global $mysqli;
	$user = userDetails($id);
	if(empty($user)){
		return 3;
	}
	if(!canMuteUser($user)){
		return 0;
	}
	if(!isPrivateMuted($user)){
		return 2;
	}
	systemPrivateUnmute($user);
	boomNotify('private_unmute', array('target'=> $user['user_id'], 'source'=> 'mute', 'icon'=> 'raction'));
	boomConsole('private_unmute', array('target'=> $user['user_id']));
	return 1;
}
// like profile actions
function useLike(){
	global $data;
	if($data['use_like'] > 0){
		return true;
	}
}
function getProfileLikes($user, $type = 0){
    global $mysqli, $data;
    // Ensure user ID is an integer
    $user_id = (int) $user['user_id'];
    $hunter_id = (int) $data['user_id'];
    // Use prepared statements to secure the query
    $stmt = $mysqli->prepare("
        SELECT 
            (SELECT COUNT(id) FROM boom_pro_like WHERE target = ?) AS total_like,
            (SELECT COUNT(id) FROM boom_pro_like WHERE target = ? AND hunter = ?) AS liked
    ");
    $stmt->bind_param("iii", $user_id, $user_id, $hunter_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $c = $result->fetch_assoc();
    $c['liking'] = 0;
    if (canLikeUser($user)) {
        $c['liking'] = 1;
        $c['user'] = $user_id;
    }
    // Close statement
    $stmt->close();
    echo boomTemplate('element/pro_like', $c);
}

function canLikeUser($user){
	global $data;
	if(isMember($user) && isMember($data) && !mySelf($user['user_id'])){
		return true;
	}
}
function showUserLike($user){
	if(useLike() && isMember($user)){
		return true;
	}
}
// vpn function check
function canWhitelist($user){
	global $data;
	if(!empty($user) && useVpn() && canEditUser($user, $data['can_modvpn'], 1)){
		return true;
	}
}
function systemSoftKick($user, $delay, $reason = ''){
    global $mysqli;
    // Calculate the new kick delay
    $this_delay = max($user['user_kick'], calMinutesUp($delay));
    // Prepare the query to update the user's kick information
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_kick = ?, kick_msg = ?, user_action = user_action + 1 WHERE user_id = ?");
    // Bind the parameters: the new kick delay, reason, and user_id
    $stmt->bind_param('isi', $this_delay, $reason, $user['user_id']);
    // Execute the query
    $stmt->execute();
    // Optionally update the Redis cache
    // redisUpdateUser($user['user_id']);
}

function systemVpnKick($user){
	global $mysqli, $data;
	if(isKicked($user)){
		return false;
	}
	systemSoftKick($user, $data['flood_delay'], 'vpn');
	boomHistory('vpn_kick', array('hunter'=> $data['system_id'], 'target'=> $user['user_id'], 'delay'=> $data['vpn_delay']));
	boomConsole('vpn_kick', array('hunter'=>$data['system_id'], 'target'=> $user['user_id'], 'delay'=> $data['vpn_delay']));
}
function canVpn(){
	global $data;
	if($data['uvpn'] == 0){
		return true;
	}
	if(boomAllow(2)){
		return true;
	}
}
function userCanVpn($user){
	if(userBoomAllow($user, 2)){
		return true;
	}
}
function useVpn(){
	global $data;
	if($data['use_vpn'] > 0 && !empty($data['vpn_key'])){
		return true;
	}
}
function useLookup(){
	global $data;
	if(!empty($data['vpn_key'])){
		return true;
	}
}
function checkVpn($ip){
    global $mysqli, $data;
    if(useVpn()){
        // Prepare the query to check if the IP is in the VPN table
        $stmt = $mysqli->prepare("SELECT vtype FROM boom_vpn WHERE vip = ?");
        $stmt->bind_param('s', $ip); // Bind the IP parameter as a string
        $stmt->execute();
        $stmt->store_result();
        // Check if the IP exists in the VPN table
        if($stmt->num_rows > 0){
            $stmt->bind_result($vtype);
            $stmt->fetch();
            if($vtype == 2){
                return true;
            }
        } else {
            // Default type if VPN is not found in the table
            $type = 1;
            // Perform the external API check
            $check = doCurl('http://proxycheck.io/v2/' . $ip . '?key=' . $data['vpn_key'] . '&vpn=1&asn=1&inf=0&risk=1&days=7&tag=msg');
            $result = json_decode($check);
            if($result->status == 'ok'){
                if(isset($result->$ip->proxy) && $result->$ip->proxy == "yes"){
                    $type = 2;
                }
                // Prepare the query to insert the IP and type into the VPN table
                $stmt_insert = $mysqli->prepare("INSERT INTO boom_vpn (vip, vtype, vdate) VALUES (?, ?, ?)");
                $current_time = time();
                $stmt_insert->bind_param('sii', $ip, $type, $current_time);
                $stmt_insert->execute();
                if($type == 2){
                    return true;
                }
            }
        }
    }
}

function recheckVpn(){
    global $data, $mysqli;
    if(useVpn() && canVpn()){
        $ip = getIp();
        // Check if the IP is different from the session-stored IP
        if(!isset($_SESSION[BOOM_PREFIX . '_cip']) || $ip != $_SESSION[BOOM_PREFIX . '_cip']){
            // Check if the IP is flagged as a VPN
            if(checkVpn($ip)){
                systemVpnKick($data); // Kick the user due to VPN usage
                return true;
            } else {
                // Update session with the new IP
                $_SESSION[BOOM_PREFIX . '_cip'] = $ip;
            }
        }
    }
}

/* icons function */
function levelIcon(){
	return 'default_images/icons/level.svg';
}
function goldIcon(){
	return 'default_images/icons/gold.svg';
}
function myRoomIcon($a){
	if(defaultRoomIcon($a)){
		return 'default_images/rooms/' . $a;
	}
	return BOOM_DOMAIN . './upload/room_icon/' . $a;
}
function defaultRoomIcon($a){
	if(stripos($a, 'default') !== false){
		return true;
	}
}
function unlinkRoomIcon($file){
	if(!defaultRoomIcon($file)){
		$delete =  BOOM_PATH. './upload/room_icon/' . $file;
		if(file_exists($delete)){
			unlink($delete);
		}
	}
	return true;
}
function roomPass($room){
	if($room['password']){
		return true;
	}
}
function roomPinned($room, $type){
	if($room['pinned'] > 0){
		return '<img  class="' . $type .  '" src="default_images/rooms/pinned_room.svg">';
	}
}
function pinnedRoom($room){
	if($room['pinned'] > 0){
		return true;
	}
}
/*last active dj */
function checkAndUpdateBroadcaster($room_id, $user_id, $timeout_duration = 10) {
    global $db; // Assuming $db is your database connection

    // Fetch the active broadcaster in the room
    $db->where('room_id', $room_id);
    $db->where('status', 'active');
    $current_broadcaster = $db->getOne('dj', 'id, dj_id, start_time, mediatype, mediaurl, status, raised_hand_user_ids');
 
    if (is_array($current_broadcaster)) { // Ensure the result is an array
        $user = userDetails($current_broadcaster['dj_id']);
        
        // Decode the raised_hand_user_ids JSON
        $raised_hand_user_ids = json_decode($current_broadcaster['raised_hand_user_ids'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($raised_hand_user_ids)) {
            $raised_hand_user_ids = array(); // Set to empty array if JSON is invalid or not an array
        }

        if ($current_broadcaster['dj_id'] == $user_id) {
            // If the current broadcaster is the same DJ, update the start_time
            $update_data = array(
                "start_time" => time()
            );

            $db->where('id', $current_broadcaster['id']);
            if ($db->update('dj', $update_data)) {
                return array(
                    'status' => 200,
                    'msg' => 'Start time updated successfully for the current broadcaster.',
                    'dj_data' => array(
                        'id' => $current_broadcaster['id'],
                        'dj_id' => $current_broadcaster['dj_id'],
                        'start_time' => $update_data['start_time'],
                        'mediaType' => $current_broadcaster['mediatype'],
                        'mediaUrl' => $current_broadcaster['mediaurl'],
                        'status' => $current_broadcaster['status'],
                        'username' => '<p class="username user '.myColorFont($user).' ">'. $user['user_name'].' </p>',
                        'avatar' => myAvatar($user['user_tumb']),
                        'raised_hands' => getRaisedHandUsers($raised_hand_user_ids),
                    ),
                );
            } else {
                return array(
                    'status' => 500,
                    'msg' => 'Failed to update start time for the current broadcaster'
                );
            }
        } else {
            // Check if the current broadcaster is inactive
            $inactive_duration = time() - $current_broadcaster['start_time'];

            if ($inactive_duration > $timeout_duration) {
                // Current broadcaster is inactive, end their broadcast
                $update_data = array(
                    "end_time" => time(),
                    "status" => 'ended', // Optionally update the status to reflect the end of the broadcast
                    "raised_hand_user_ids" => 'null',
                    "mediaurl" => '',
                    "start_time" => 0,
                );

                $db->where('id', $current_broadcaster['id']);
                if ($db->update('dj', $update_data)) {
                    // Find a new active broadcaster in the same room
                    $db->where('room_id', $room_id);
                    $db->where('status', 'active');
                    $new_broadcaster = $db->getOne('dj', 'dj_id, start_time');

                    if (is_array($new_broadcaster)) { // Ensure the result is an array
                        return array(
                            'status' => 200,
                            'msg' => 'Broadcast ended due to inactivity. New active broadcaster found.',
                            'new_broadcaster' => $new_broadcaster['dj_id']
                        );
                    } else {
                        return array(
                            'status' => 200,
                            'msg' => 'Broadcast ended due to inactivity. No new broadcaster found.'
                        );
                    }
                } else {
                    return array(
                        'status' => 500,
                        'msg' => 'Failed to end the broadcast due to inactivity'
                    );
                }
            } else {
                // Current broadcaster is still active
                return array(
                    'status' => 200,
                    'msg' => 'Current broadcaster is still active',
                    'dj_data' => array(
                        'id' => $current_broadcaster['id'],
                        'dj_id' => $current_broadcaster['dj_id'],
                        'start_time' => $current_broadcaster['start_time'],
                        'mediaType' => $current_broadcaster['mediatype'],
                        'mediaUrl' => $current_broadcaster['mediaurl'],
                        'status' => $current_broadcaster['status'],
                        'username' => '<p class="username user '.myColorFont($user).' ">'. $user['user_name'].' </p>',
                        'avatar' => myAvatar($user['user_tumb']),
                        'raised_hands' => getRaisedHandUsers($raised_hand_user_ids) // Fetch the details of users who raised hands
                    ),
                );
            }
        }
    } else {
        // No broadcaster found in the room or invalid result
        return array(
            'status' => 404,
            'msg' => 'No broadcaster found in the room'
        );
    }
}

// Function to get the details of users who raised their hands
function getRaisedHandUsers($raisedHandData) {
    // Check if the input is a string (JSON) or already an array
    if (is_string($raisedHandData)) {
        $raisedHands = json_decode($raisedHandData, true); // Decode JSON to array
    } else {
        $raisedHands = $raisedHandData; // Assume it's already an array
    }
    $userDetailsList = array();
    if (!empty($raisedHands) && is_array($raisedHands)) {
        foreach ($raisedHands as $user_id) {
            $userDetails = userDetails($user_id);
            if (is_array($userDetails)) { // Check if userDetails returns an array
                $userDetailsList[] = array(
                    'user_id' => $user_id,
                    'username' => $userDetails['user_name'],
                    'avatar' => myAvatar($userDetails['user_tumb'])
                );
            }
        }
    }

    return $userDetailsList;
}
function updateUserGold() {
    global $mysqli, $data; // Access global variables
    // Set the time interval for gold rewards (in seconds)
    $rewardInterval = $data['gold_delay'] * 60; // 60 s * 1 minute
    // Get the current time
    $currentTime = time();
    // Calculate how much time has passed since the last reward (or last action)
    $timeSinceLastAction = $currentTime - $data['last_gold'];
    // Calculate how much time is left before the next reward
    $timeLeft = $rewardInterval - $timeSinceLastAction;
    // Check if it's time to give a reward
    if ($timeLeft <= 0) {
        // It's time to give the reward, reset the countdown
        $ip = getIp(); // Get the user's IP address
        // Check if gold rewards are enabled and the user qualifies
        if (useGold() && canGold()) {
            // Update gold and reset last_action and last_gold to current time
            $goldBase = (int)$data['gold_base']; // Ensure gold_base is an integer
            // Construct the SQL update query
            $query = "UPDATE boom_users 
                      SET user_gold = user_gold + $goldBase, 
                          last_gold = '$currentTime', 
                          user_ip = '$ip' 
                      WHERE user_id = '{$data['user_id']}'";

            // Execute the SQL update query
            if ($mysqli->query($query)) {
                // Update the $data array to reflect the new last_gold
                $data['last_gold'] = $currentTime;
                $data['user_gold'] += $goldBase;
                // Reset time_left to the reward interval (60 seconds)
                $timeLeft = $rewardInterval;
                // Return the updated gold amount and the new time_left
                return [
                    'status' => 'success',
                    'gold' => $data['user_gold'], // Updated gold amount
                    'time_left' => $timeLeft // Reset time_left to the reward interval
                ];
            } else {
                // Log error in case the SQL update fails
                error_log("Database error: " . $mysqli->error);
                return ['status' => 'error', 'message' => 'Failed to update user gold.'];
            }
        }
    }
    // If it's not yet time for a reward, return the remaining time
    return [
        'status' => 'no_action',
        'gold' => $data['user_gold'], // Current gold amount
        'time_left' => max($timeLeft, 0) // Ensure time_left doesn't go below 0
    ];
}

function get_rooms_notifications() {
    global $db;
    $res = array();

    // Fetch only room_id and rcaction from the rooms table
    $db->get('rooms', null, ['room_id', 'rcaction']); // Specify the columns to fetch
    if ($db->count > 0) {
        foreach ($db->get('rooms', null, ['room_id', 'rcaction']) as $raction) {
            $res[] = $raction; // Add each room's action data to the result array
        }
    }
    return $res; // Return the array with room_id and rcaction data
}

?>
