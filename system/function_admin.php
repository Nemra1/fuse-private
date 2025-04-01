<?php
function createInfo($v){
	return '<i class="ri-apps-2-add-line theme_color infopop" data="' . $v . '"></i>';
}
function adminRoomList(){
	global $mysqli, $lang;
	$list_rooms = '';
	$getrooms = $mysqli->query("SELECT boom_rooms.*
	FROM boom_rooms 
	ORDER BY pinned DESC, room_name ASC");
	if($getrooms->num_rows > 0){
		while($room = $getrooms->fetch_assoc()){
			$list_rooms .= boomTemplate('element/admin_room', $room);
		}
	}
	else {
		$list_rooms .= emptyZone($lang['empty']);
	}
	return $list_rooms;
}
/* bot system*/
function bot_adminRoomList(){
    global $data, $db,$lang;
	$res = array();
	$rooms = $db->get('rooms');
	if($db->count > 0){
		 foreach ($rooms as $room){
			 $res[] = $room;
		}	   
	}
	 return $res;
}
function getUpdateList(){
	global $mysqli, $setting, $lang;
	$update_list = '';
	$avail_update = 0;
	$dir = glob(BOOM_PATH . '/updates/*' , GLOB_ONLYDIR);
	foreach($dir as $dirnew){
		$update = str_replace(BOOM_PATH . '/updates/', '', $dirnew);
		if($update > $setting['version'] && is_numeric($update)){
			$avail_update++;
			$update_list .= boomTemplate('element/update_element', $update);
		}
	}
	if($avail_update > 0){
		return '<div>' . $update_list . '</div>';
	}
	else {
		return emptyZone($lang['no_update']);
	}
}
function adminAddonsList(){
	global $mysqli, $lang;
	$addons_list = '';
	$avail_update = 0;
	$dir = glob(BOOM_PATH . '/addons/*' , GLOB_ONLYDIR);
	foreach($dir as $dirnew){
		$install = 0;
		$addon = escape(str_replace(BOOM_PATH . '/addons/', '', $dirnew));
		if(file_exists(BOOM_PATH . '/addons/' . $addon . '/system/install.php')){
			$avail_update++;
			$checkaddons = $mysqli->query("SELECT * FROM boom_addons WHERE addons = '$addon'");
			if($checkaddons->num_rows > 0){
				$addons = $checkaddons->fetch_assoc();
				$addons_list .= boomTemplate('element/addons_uninstall', $addons);
			}
			else {
				$addons_list .= boomTemplate('element/addons_install', $addon);
			}
		}
	}
	if($avail_update > 0){
		return $addons_list;
	}
	else {
		return emptyZone($lang['no_addons']);
	}
}
function getDashboard(){
	global $mysqli;
	$delay = getDelay();
	if(boomAllow(90)){
		$request = $mysqli->query("SELECT
						( SELECT count(user_id) FROM boom_users ) as user_count,
						( SELECT count(user_id) FROM boom_users WHERE last_action >= $delay) as online_count,
						( SELECT count(user_id) FROM boom_users WHERE user_sex = 2 ) as female_count,
						( SELECT count(user_id) FROM boom_users WHERE user_sex = 1 ) as male_count,
						( SELECT count(id) FROM boom_private ) as private_count,
						( SELECT count(post_id) FROM boom_chat ) as chat_count,
						( SELECT count(post_id) FROM boom_post ) as post_count,
						( SELECT count(reply_id) FROM boom_post_reply ) as reply_count,
						( SELECT count(user_id) FROM boom_users WHERE user_ghost > " . time() ." ) as ghosted_users,
						( SELECT count(user_id) FROM boom_users WHERE user_banned > 0) as banned_users,
						( SELECT count(user_id) FROM boom_users WHERE user_mute > " . time() . " ) as muted_users,
						( SELECT count(user_id) FROM boom_users WHERE user_kick > " . time() . " ) as kicked_users
						");	
	}
	else {
		$request = $mysqli->query("SELECT
						( SELECT count(user_id) FROM boom_users ) as user_count,
						( SELECT count(user_id) FROM boom_users WHERE last_action >= $delay) as online_count,
						( SELECT count(user_id) FROM boom_users WHERE user_sex = 2 ) as female_count,
						( SELECT count(user_id) FROM boom_users WHERE user_sex = 1 ) as male_count,
						( SELECT count(user_id) FROM boom_users WHERE user_ghost > " . time() . " ) as ghosted_users,
						( SELECT count(user_id) FROM boom_users WHERE user_banned > 0) as banned_users,
						( SELECT count(user_id) FROM boom_users WHERE user_mute > " . time() . " ) as muted_users,
						( SELECT count(user_id) FROM boom_users WHERE user_kick > " . time() . " ) as kicked_users
						");
	}
	
	$dashboard = $request->fetch_assoc();
	return $dashboard;
}
function listLogin(){
	global $setting, $lang;
	$login_list = '';
	$dir = glob(BOOM_PATH . '/control/login/*' , GLOB_ONLYDIR);
	foreach($dir as $dirnew){
		$login = str_replace(BOOM_PATH . '/control/login/', '', $dirnew);
		if(file_exists(BOOM_PATH . '/control/login/' . $login . '/login.php')){
			$login_list .= '<option ' . selCurrent($setting['login_page'], $login) . ' value="' . $login . '">' . $login . '</option>';
		}
	}
	return $login_list;
}
function listDj(){
	global $mysqli, $lang;
	$list_members = '';
	$getmembers = $mysqli->query("SELECT * FROM boom_users WHERE user_dj = 1 ORDER BY user_onair DESC, user_name ASC");
	if($getmembers->num_rows > 0){
		while($members = $getmembers->fetch_assoc()){
			$list_members .= boomTemplate('element/admin_dj', $members);
		}
	}
	else {
		$list_members .= emptyZone($lang['empty']);
	}
	return $list_members;
}
function listContact(){
	global $mysqli, $lang, $data;
	$contact_list = '';
	$get_contact = $mysqli->query("SELECT * FROM boom_contact ORDER BY cdate ASC");
	if($get_contact->num_rows > 0){
		while($contact = $get_contact->fetch_assoc()){
			$contact_list .= boomTemplate('element/admin_contact', $contact);
		}
	}
	else {
		$contact_list .= emptyZone($lang['empty']);
	}
	return $contact_list;
}
function listFilter($type){
	global $data, $mysqli, $lang;
	$list_word = '';
	$getword = $mysqli->query("SELECT * FROM boom_filter WHERE word_type = '$type' ORDER BY word ASC");
	if($getword->num_rows > 0){
		while($word = $getword->fetch_assoc()){
			$list_word .= boomTemplate('element/word', $word);
		}
	}
	else {
		$list_word .= emptyZone($lang['empty']);
	}
	return $list_word;
}
function listAdminIp(){
	global $mysqli, $lang;
	$list_ip = '';
	$getip = $mysqli->query("SELECT * FROM boom_banned ORDER BY ip ASC");
	if($getip->num_rows > 0){
		while($ip = $getip->fetch_assoc()){
			$list_ip .= boomTemplate('element/admin_ip', $ip);
		}
	}
	else {
		$list_ip .= emptyZone($lang['empty']);
	}
	return $list_ip;
}
function listLastMembers(){
	global $mysqli, $lang;
	$list_members = '';
	$getmembers = $mysqli->query("SELECT * FROM boom_users WHERE user_rank != 0 AND user_bot = 0 ORDER BY user_join DESC LIMIT 50");
	if($getmembers->num_rows > 0){
		while($members = $getmembers->fetch_assoc()){
			$list_members .= boomTemplate('element/admin_user', $members);
		}
	}
	else {
		$list_members .= emptyZone($lang['empty']);
	}
	return $list_members;
}
function listStreamPlayer(){
	global $mysqli, $setting, $lang;
	$stream_list = '';
	$getstream = $mysqli->query("SELECT * FROM boom_radio_stream ORDER BY stream_alias ASC");
	if($getstream->num_rows > 0){
		while($stream = $getstream->fetch_assoc()){
			$stream['default'] = '';
			if($stream['id'] == $setting['player_id']){
				$stream['default'] = '<div class="sub_list_selected"><i class="fa fa-circle success"></i></div>';
			}
			$stream_list .= boomTemplate('element/stream_player', $stream);
		}
	}
	else {
		$stream_list .= emptyZone($lang['empty']);
	}
	return $stream_list;
}
function listAdminGift(){
	global $mysqli, $data, $lang;
	$list = '';
	$get_gift = $mysqli->query("SELECT * FROM boom_gift WHERE id > 0 ORDER BY id DESC");
	if($get_gift->num_rows > 0){
		while($gift = $get_gift->fetch_assoc()){
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


?>