<?php
/* call functions */

function callDelay(){
	return 20;
}
function useCall(){
	global $data;
	if($data['use_call'] > 0){
		return true;
	}
}
function canCall(){
	global $data, $data;
	if(featureBlock($data['bcall'])){
		return false;
	}
	if(boomAllow($data['can_vcall']) || boomAllow($data['can_acall'])){
		return true;
	}
}
function canVideoCall(){
	global $data;
	if(boomAllow($data['can_vcall'])){
		return true;
	}
}
function canAudioCall(){
	global $data;
	if(boomAllow($data['can_acall'])){
		return true;
	}
}
function callTimeout($call){
	if($call['call_time'] < time() - callDelay()){
		return true;
	}
}
function callExpired($call){
    global $data;
    // Ensure $data['call_max'] is set and valid
    if (!isset($data['call_max']) || $data['call_max'] <= 0) {
        error_log("Invalid or missing call_max value");
        return false; // Assume the call has not expired if no max duration is defined
    }
    // Calculate the expiration threshold for the maximum call duration
    $max_duration_threshold = $call['call_time'] + ($data['call_max'] * 60);
    // Calculate the expiration threshold for idle timeout (e.g., 30 seconds of inactivity)
    $idle_timeout = 30; // Idle timeout in seconds
    $idle_threshold = $call['call_active'] + $idle_timeout;
    // Get the current time
    $current_time = time();
    // Check if the call has exceeded the maximum duration
    if ($current_time > $max_duration_threshold) {
        return true; // Call has expired due to exceeding the maximum duration
    }
    // Check if the call has been idle for too long
    if ($current_time > $idle_threshold) {
        return true; // Call has expired due to inactivity
    }
    return false; // Call has not expired
}

function update_active_call($call){
	global $mysqli,$data,$lang;
	$call_id = $call['call_id'];
	$time = time();
	$mysqli->query("UPDATE boom_call SET call_status = '1', call_active = '$time' WHERE call_id = '$call_id'");
    // Deduct cost if a full minute has passed since the last deduction
    $current_time = time();	
	// Deduct call cost from the caller's wallet
	if($call['last_deduction_time'] == 0 || ($current_time - $call['last_deduction_time']) >= 60) {
		$hunter_info =  fuse_user_data($call['call_hunter']);
        // Check if the caller has sufficient balance
        if ($hunter_info['user_gold'] < $data['call_cost']) {
			// End the call if the caller has insufficient balance
			endCall($call_id, 'Insufficient balance');
			echo fu_json_results(['code' => 99, 'message' => 'Call ended due to insufficient balance']);
		}
        // Deduct the cost from the caller's wallet
        cl_update_user_data($hunter_info['user_id'], [
            'user_gold' => $hunter_info['user_gold'] - $data['call_cost']
        ]);		
        // Update the last_deduction_time in the database
        $stmt = $mysqli->prepare("UPDATE boom_call SET last_deduction_time = ? WHERE call_id = ?");
        $stmt->bind_param("ii", $current_time, $call_id);
        $stmt->execute();		
	}
}
function canCallUser($user){
	global $data;
	if(empty($user)){
		return false;
	}
	if(myself($user['user_id'])){
		return false;
	}
	if(isBot($user)){
		return false;
	}
	if($user['last_action'] < getDelay()){
		return false;
	}
	if($user['user_call'] == 0){ 
		return false;
	}
	if($user['user_call'] == 2 && !haveFriendship($user)){
		return false;
	}
	if($user['user_call'] == 3 && isGuest($data)){
		return false;
	}
	if(ignored($user) || ignoring($user)){
		return false;
	}
	return true;
}
function acceptCall($call){
	global $mysqli;
	$time = time();
	$mysqli->query("UPDATE boom_call SET call_status = '1', call_active = '$time' WHERE call_id = '{$call['call_id']}'");
}
function endCall($call, $reason){
	global $mysqli;
	$time = time();
	$mysqli->query("UPDATE boom_call SET call_status = '2', call_reason = '$reason' , call_last = '$time' WHERE call_id = '{$call['call_id']}'");
}
function endAllCall($reason){
	global $mysqli;
	$mysqli->query("UPDATE boom_call SET call_status = '2', call_last = '$time',  call_reason = '$reason' WHERE call_status < 2");
}
function callDetails($id){
	global $mysqli;
	$call = [];
	$get_call = $mysqli->query("SELECT * FROM boom_call WHERE call_id = '$id'");
	if($get_call->num_rows > 0){
		$call = $get_call->fetch_assoc();
	}
	return $call;
}
function incomingCallDetails(){
	global $mysqli, $data;
	$delay = time() - callDelay();
	$call = [];
	$get_call = $mysqli->query("
		SELECT boom_call.*, boom_users.* 
		FROM boom_call 
		LEFT JOIN boom_users ON boom_call.call_hunter = boom_users.user_id 
		WHERE boom_call.call_target = '{$data['user_id']}' AND boom_call.call_time >= '$delay' 
		ORDER BY boom_call.call_time DESC LIMIT 1;
	");
	if($get_call->num_rows > 0){
		$call = $get_call->fetch_assoc();
	}
	return $call;
}
function useCallBalance(){
	global $data;
	if($data['call_cost'] > 0 && useWallet()){
		return true;
	}
}

function canInitCall($type){
    global $data,$lang;
    $res = [
        'error' => '',
        'code' => 150,
        'return' => false,
    ];
    if (!canCall()) {
        $res['error'] = $lang['no_call_perm'];
        $res['code'] = 150; // Action limit or general restriction
        return $res;
    }
    if ($type == 1 && !canVideoCall()) {
        $res['error'] = $lang['cannot_vcall'];
        $res['code'] = 150; // Video call not allowed
        return $res;
    }
    if ($type == 2 && !canAudioCall()) {
        $res['error'] = $lang['cannot_acall'];
        $res['code'] = 150; // Audio call not allowed
        return $res;
    }
    if (!useCallBalance()) {
        $res['error'] = $lang['no_bal_req'];
        $res['code'] = 150; // Balance not required (success case)
        $res['return'] = true;
        return $res;
    }
    if (!walletBalance($data['call_method'], $data['call_cost'])) {
        $res['error'] = $lang['no_gold'];
        $res['code'] = 150; // Insufficient balance
        return $res;
    }
    // If all checks pass
    $res['code'] = 200; // Success
    $res['return'] = true;
    return $res;
}
function callType($t){
	global $lang;
	switch($t){
		case 1:
			return $lang['video_call'];
		case 2:
			return $lang['audio_call'];
		default:
			return 'N/A';
	}
}
function minCall(){
	global $data;
	return min($data['can_vcall'], $data['can_acall']);
}
function costTag($type, $amount, $class = ''){
	$tg = [
		'icon' 	=> walletIcon($type),
		'amount' => $amount,
		'class' => $class,
	];
	return boomTemplate('element/call/cost_tag', $tg);
}
function walletIcon($type){
	switch($type){
		case 1:
			return goldIcon();
/* 		case 2:
			return rubyIcon();
 */		default:
			return goldIcon();
	}
}
function featureBlock($v){
	if($v == 1){
		return true;
	}
}

function playSound($val){
	global $data;
	if(preg_match('@[' . $val . ']@i', $data['user_sound'])){
		return true;
	}
}

function isAVCallPurchased() {
    global $cody;
    // Check if the webmaster has purchased the voice/video call feature
    return !empty($cody['fuse_voice_call_purchased']);
}
?>