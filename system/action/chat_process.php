<?php
/**
 * FuseChat
 *
 * @package FuseChat
 * @author www.nemra-1.com
 * @copyright 2020
 * @terms Unauthorized use of this script without a valid license is prohibited.
 * All content of FuseChat is the property of BoomCoding and cannot be used in another project.
 */
require_once("./../config_session.php");

if(mainBlocked()){
    die(json_encode(['error' => 'System unavailable']));
}
// Check if required POST parameters are set
if (!isset($_POST['content'], $_POST['snum'])) {
    die(); // or handle the error appropriately
}

// Validate and sanitize input content
$content = $_POST['content'];
$snum = $_POST['snum'];

// Length Validation
if (isTooLong($content, $data['max_main']) || empty(trim($content))) {
    die(json_encode(['error' => 'invalid_length']));
}
// Check if user is muted or room is muted
if (muted() || isRoomMuted($data)) {
    die(json_encode(['error' => 'muted']));
}
// Check for flood control
// 3. Rate Limiting
if(checkFlood()) {
    die(json_encode(['error' => 'flood'])); // Consistent with your 100 code
}
// Sanitize and filter content
$content = escape($content);
$content = wordFilter($content, 1);
$content = textFilter($content);
// Validate the content and room status
if (empty($content) && $content !== '0' || !inRoom()) {
	 die(json_encode(['error' => 'Validate the content and room status']));
}

// Process and echo the chat post
echo userPostChat($content, array('snum' => $snum));

?>
