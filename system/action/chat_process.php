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
require_once("./../config_session.php");

if(mainBlocked()){
	die();
}

// Check if required POST parameters are set
if (!isset($_POST['content'], $_POST['snum'])) {
    die(); // or handle the error appropriately
}

// Validate and sanitize input content
$content = $_POST['content'];
$snum = $_POST['snum'];

// Validate content length
if (isTooLong($content, $data['max_main'])) {
    die(); // or handle the error appropriately
}

// Check if user is muted or room is muted
if (muted() || isRoomMuted($data)) {
    die(); // or handle the error appropriately
}

// Check for flood control
if (checkFlood()) {
    echo 100; // Flood control response
    die();
}

// Sanitize and filter content
$content = escape($content);
$content = wordFilter($content, 1);
$content = textFilter($content);

// Validate the content and room status
if (empty($content) && $content !== '0' || !inRoom()) {
    die(); // or handle the error appropriately
}

// Process and echo the chat post
echo userPostChat($content, array('snum' => $snum));

?>
