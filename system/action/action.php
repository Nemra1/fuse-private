<?php
require('./../config_session.php');

// Function to handle actions
function handleAction($action, $target) {
    global $mysqli; // Ensure access to $mysqli
    $action = escape($action);
    $target = escape($target);
    switch ($action) {
        case 'unban':
            return unbanAccount($target);
        case 'unmute':
            return unmuteAccount($target);
        case 'main_unmute':
            return unmuteAccountMain($target);  
        case 'private_unmute':
            return unmuteAccountPrivate($target);           
        case 'unghost':
            return unghostAccount($target);     
        case 'room_unmute':
            return unmuteRoom($target);
        case 'muted':
            return unmuteAccount($target);
        case 'banned':
            return unbanAccount($target);
        case 'room_unblock':
            return unblockRoom($target);
        case 'kicked':
        case 'unkick':
            return unkickAccount($target);
        default:
            return 0;
    }
}
function maintenanceStatus(){
	global $mysqli, $data;
	if($data['maint_mode'] == 0){
		return 1;
	}
	return 0;
}
function kickStatus(){
    global $mysqli, $data;
    if(!isKicked($data)){
        if($data['user_kick'] > 0){
            $stmt = $mysqli->prepare("UPDATE boom_users SET user_kick = 0 WHERE user_id = ?");
            $stmt->bind_param("i", $data['user_id']);
            $stmt->execute();
			//redisUpdateUser($data['user_id']);
        }
        return 1;
    }
    return 0;
}


// Handle action requests
if (isset($_POST['take_action'], $_POST['target'])) {
    $action = escape($_POST['take_action']);
    $target = escape($_POST['target']);
    echo handleAction($action, $target);
    die();
}

// Check if user is kicked
if(isset($_POST['check_kick'])){
	echo kickStatus();
	die();
}

// Check if maintenance mode is enabled
if (isset($_POST['check_maintenance'])) {
    echo maintenanceStatus();
    die();
}

// Handle kick requests
if (isset($_POST['kick'], $_POST['reason'], $_POST['delay'])) {
    $target = escape($_POST['kick']);
    $reason = escape($_POST['reason']);
    $delay = escape($_POST['delay']);
    echo kickAccount($target, $delay, $reason);
    die();
}
// Handle kick requests
if (isset($_POST['room_mute'], $_POST['reason'], $_POST['delay'])) {
    $target = escape($_POST['room_mute']);
    $reason = escape($_POST['reason']);
    $delay = escape($_POST['delay']);
    echo muteRoom($target, $delay, $reason);
    die();
}
// Handle room block requests
if(isset($_POST['room_block'], $_POST['reason'], $_POST['delay'])){
	$target = escape($_POST['room_block'], true);
	$reason = escape($_POST['reason']);
	$delay = escape($_POST['delay'], true);
	echo blockRoom($target, $delay, $reason);
	die();
}
// Handle mute requests
if (isset($_POST['mute'], $_POST['reason'], $_POST['delay'])) {
    $target = escape($_POST['mute']);
    $reason = escape($_POST['reason']);
    $delay = escape($_POST['delay']);
    echo muteAccount($target, $delay, $reason);
    die();
}
// Handle main mute requests
if (isset($_POST['main_mute'], $_POST['reason'], $_POST['delay'])) {
    $target = escape($_POST['main_mute']);
    $reason = escape($_POST['reason']);
    $delay = escape($_POST['delay']);
    echo muteAccountMain($target, $delay, $reason);
    die();
}
if (isset($_POST['private_mute'], $_POST['reason'], $_POST['delay'])) {
    $target = escape($_POST['private_mute']);
    $reason = escape($_POST['reason']);
    $delay = escape($_POST['delay']);
    echo muteAccountPrivate($target, $delay, $reason);
    die();
}
// Handle ghost requests
if(isset($_POST['ghost'], $_POST['reason'], $_POST['delay'])){
	$target = escape($_POST['ghost'], true);
	$reason = escape($_POST['reason']);
	$delay = escape($_POST['delay'], true);
	echo ghostAccount($target, $delay, $reason);
	die();
}
// Handle ban requests
if (isset($_POST['ban'], $_POST['reason'])) {
    $target = escape($_POST['ban']);
    $reason = escape($_POST['reason']);
    echo banAccount($target, $reason);
    die();
}
if(isset($_POST['warn'], $_POST['reason'])){
	$target = escape($_POST['warn'], true);
	$reason = escape($_POST['reason']);
	echo warnAccount($target, $reason);
	die();
}
// Remove room staff
if (isset($_POST['remove_room_staff'], $_POST['target'])) {
    $target = escape($_POST['target']);
    echo removeRoomStaff($target);
    die();
}


?>
