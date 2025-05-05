<?php
/**
* FuseChat
*
* @package FuseChat
* @author www.nemra-1.com
* @copyright 2020
* @terms any use of this script without a legal license is prohibited
* all the content of FuseChat is the propriety of BoomCoding and Cannot be 
* used for another project.
*/
define('SYSTEM_DIR', __DIR__ . '/system/');
define('CONTROL_DIR', __DIR__ . '/control/');

$page_info = array(
	'page'=> 'home',
	'page_nohome'=> 1,
);
require_once(SYSTEM_DIR . "config.php");


if($chat_install != 1){
	include('builder/installer.php');
	die();
}
$chat_room = getRoomId();
if($chat_room > 0){
	$data['user_roomid'] = $chat_room;
	$page_info['page'] = 'chat';
}
// loading head tag element
include(CONTROL_DIR . 'head_load.php');

// loading page content
if($page['page'] == 'chat'){
	include(CONTROL_DIR . 'chat.php');
}
else {
    include(CONTROL_DIR . 'lobby.php');
}
// close page body
include(CONTROL_DIR . 'body_end.php')
?>