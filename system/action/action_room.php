<?php
$res =[];
if ($f == 'action_room') {
    function get_in_room(){
        global $mysqli, $data;
        $get_in_room = escape($_POST['get_in_room']);
        $room = escape($_POST['room']);
        if (!isset($room, $get_in_room)) {
            return;
        }
        $target = escape($room);
        $password = $_POST['pass'] ?? null;
        $userId = $data['user_id'];    
        $room = myRoomDetails($target);
        if ($room ===false) {
            echo boomCode(1);
            exit;
        }    
        if ($room['room_blocked'] > time()  || mustVerify()) {
            echo boomCode(99);
            exit;
        }
        $muted = $room['room_muted'] ?? 0;
        $role = $room['room_status'] ?? 0;
        if ($muted > 0) {
			$stmt = $mysqli->prepare("UPDATE boom_users SET room_mute = ? WHERE user_id = ?");
			$stmt->bind_param("ii", $muted, $userId); // Assuming both are integers
			$stmt->execute();
			$stmt->close();
        }
        $data['user_role'] = $role;
    
        if (!boomAllow($room['access'])) {
            echo boomCode(2);
            exit;
        }
        if (!empty($room['password'])) {
                if ($password === null || ($password !== $room['password'] && !canRoomPassword())) {
                    echo $password === null ? boomCode(4) : boomCode(5);
                    exit;
            }
        }
		$stmt = $mysqli->prepare("
			UPDATE boom_users 
			SET join_msg = 0, user_roomid = ?, last_action = ?, user_role = ?, room_mute = ? 
			WHERE user_id = ?
		");
		$time_now = time();
		$stmt->bind_param("siiii", $target, $time_now, $role, $muted, $userId);
		$stmt->execute();
		$stmt->close();
		$stmt = $mysqli->prepare("
			UPDATE boom_rooms 
			SET room_action = ? 
			WHERE room_id = ?
		");
		$stmt->bind_param("is", $time_now, $target);
		$stmt->execute();
		$stmt->close();

        leaveRoom();
        echo boomCode(10, ['name' => $room['room_name'], 'id' => $room['room_id']]);
        exit;
    } 
  if ($s == 'switchRoom' && boomLogged() === true) {
        $res = get_in_room();
        header("Content-type: application/json");
        echo json_encode($res);
        exit();
   } 
   if ($s == 'addRoom' && boomLogged() === true) {
	$set_pass = escape($_POST["set_pass"]);
	$set_type = escape($_POST["set_type"]);
	$set_name = escape($_POST['set_name']);
	$set_description = escape($_POST['set_description']);
    	if(!canRoom() || !roomType($set_type)){
    		$res['code'] = 0;
    		$res['msg'] = 'You do not have permission to add a room or your level is not allowed';
    		exit();
    	}
    	$room_system = 0;
    	if(boomAllow(100)){
    		$room_system = 1;
    	}
    	if(!validRoomName($set_name)){
    		$res['code'] = 0;
    		$res['msg'] = 'Room Name is not valid';
    		exit();
    	}
     	if(isToolong($set_description, $cody['max_description'])){
        	$res['code'] = 1;
    		$res['msg'] = 'description is too long';
    		exit();
    	}
    	$max_room = $mysqli->query("SELECT room_id FROM boom_rooms WHERE room_creator = '{$data['user_id']}'");
    	if($max_room->num_rows >= $cody['max_room'] && !boomAllow(70)){
    	    $res['code'] = 5;
    		$res['msg'] = 'Reached Max Rooms';
    		exit();
    	}
    	$check_duplicate = $mysqli->query("SELECT room_name FROM boom_rooms WHERE room_name = '$set_name'");
    	if($check_duplicate->num_rows > 0){
    	    $res['code'] = 6;
    		$res['msg'] = 'Error: Duplicate room name';
    		exit();
    	}
    	if(mb_strlen($set_pass) > 20){
    		$res['code'] = 1;
    		$res['msg'] = 'The password is more than 20 characters';
    		exit();
    	}
	    $mysqli->query("INSERT INTO boom_rooms (room_name, access, description, password, room_system, room_creator, room_action) VALUES ('$set_name', '$set_type', '$set_description', '$set_pass', '$room_system', '{$data['user_id']}', '" . time() . "')");
	    $last_id = $mysqli->insert_id;
  	    $mysqli->query("DELETE FROM boom_room_staff WHERE room_id = '$last_id'");
    	if(!boomAllow(90)){
    		$mysqli->query("UPDATE boom_users SET user_roomid = '$last_id', last_action = '" . time() . "', user_role = '6' WHERE user_id = '{$data['user_id']}'");
    		$mysqli->query("INSERT INTO boom_room_staff ( room_id, room_staff, room_rank) VAlUES ('$last_id', '{$data['user_id']}', '6')");
    	}else {
	    	$mysqli->query("UPDATE boom_users SET user_roomid = '$last_id', last_action = '" . time() . "' WHERE user_id = '{$data['user_id']}'");
	    } 
	    $groom = roomInfo($last_id);
	    boomConsole('create_room', array('room'=>$groom['room_id']));
	    $res['code'] = 7;
	    $res['msg'] = 'The room has been added successfully';
	    $res['r'] =array('name'=> $groom['room_name'], 'id'=> $groom['room_id']);
	    //echo boomCode(7, array('name'=> $groom['room_name'], 'id'=> $groom['room_id']));
        header("Content-type: application/json");
        echo json_encode($res);
        exit();
    }  
    if ($s == 'leave_room') {
    	$mysqli->query("UPDATE boom_users SET user_roomid = '0' WHERE user_id = '{$data['user_id']}'");
    	echo 1;
    	die();        
    }
    if ($s == 'access_room') {
            $res = get_in_room();
            header("Content-type: application/json");
            echo json_encode($res);
            exit();
    }
     if ($s == 'admin_addroom') {
    	$set_pass = escape($_POST["admin_set_pass"]);
    	$set_type = escape($_POST["admin_set_type"]);
    	$set_name = escape($_POST['admin_set_name']);
		$set_description = escape($_POST['admin_set_description']);
    	if(isTooLong($set_name, $cody['max_room_name']) || strlen($set_name) < 4){
        	$res['code'] = 2;
    		$res['msg'] = 'Room Name is too long';
    		exit();
    	}
        $check_duplicate = $mysqli->query("SELECT room_name FROM boom_rooms WHERE room_name = '$set_name'");
    	if($check_duplicate->num_rows > 0){
    		$res['code'] = 6;
    		$res['msg'] = 'Room Name already exist';
    		exit();
    	} 
    	if(isToolong($set_description, $cody['max_description'])){
    		$res['code'] = 0;
    		$res['msg'] = 'description is too long';
    		die();
    	}
    	if(mb_strlen($set_pass) > 20){
    		$res['code'] = 1;
    		$res['msg'] = 'The password is more than 20 characters';
    		exit();
    	}
        $mysqli->query("INSERT INTO boom_rooms (room_name, access, description, password, room_system, room_creator, room_action) VALUES ('$set_name', '$set_type', '$set_description', '$set_pass', '1', '{$data['user_id']}', '" . time() . "')");
        $last_id = $mysqli->insert_id;
        $mysqli->query("DELETE FROM boom_room_staff WHERE room_id = '$last_id'");
        $room = roomInfo($last_id);
    	if(empty($room)){
    		$res['code'] = 1;
    		exit();
    	}else {
		    boomConsole('create_room', array('room'=>$room['room_id']));
		    $res['html'] = boomTemplate('element/admin_room', $room);
            $res['msg'] = 'The room has been added successfully';
    	}        
        header("Content-type: application/json");
        echo json_encode($res);
        exit();
    } 
	if ($s == 'admin_update_tabs') {
		$room_tabs = escape($_POST['room_tabs']);
    	if(isset($room_tabs)){
			$update_tab = fu_update_dashboard(array(
					"use_room_tabs" => $room_tabs,
			));
			if($update_tab){
				$res['code'] = 1;
			}
			
		}
		 header("Content-type: application/json");
        echo json_encode($res);
        exit();		
	}
    if ($s == 'admin_update_room') {
    	$player_id = 0;
    	$target = escape($_POST['admin_set_room_id']);
    	$name = escape($_POST['admin_set_room_name']);
    	$description = escape($_POST['admin_set_room_description']);
    	$password = escape($_POST['admin_set_room_password']);
    	//add room keywords
    	$room_keywords = escape($_POST['admin_set_room_keywords']);

    	if(isset($_POST['admin_set_room_player'])){
    		$player = escape($_POST['admin_set_room_player']);
    		$getplayer = $mysqli->query("SELECT * FROM boom_radio_stream WHERE id = '$player'");
    		if($getplayer->num_rows > 0){
    			$play = $getplayer->fetch_assoc();
    			$player_id = $play['id'];
    		}
    		else {
    			$player_id = 0;
    		}
    	}
    	$room_access = escape($_POST['admin_set_room_access']);
    	$get_room = $mysqli->query("SELECT * FROM boom_rooms WHERE room_id = '{$data['user_roomid']}'");
    	$room = $get_room->fetch_assoc();
    	if(roomExist($name, $target)){
    		$res['code'] = 2;
    		exit();
    	}
    	if(isToolong($description, $cody['max_description'])){
    		$res['code'] = 0;
    		exit();
    	}
    	if($name == '' || isTooLong($name, $cody['max_room_name'])){
    		$res['code']= 4;
    		exit();
    	}
    	if($target == 1){
    		$password = '';
    		$room_access = 0;
    	}
    	$update = $mysqli->query("UPDATE boom_rooms SET room_name = '$name', description = '$description', password = '$password', room_player_id = '$player_id', access = '$room_access', room_keywords = '$room_keywords' WHERE room_id = '$target'"); 
         if($update){
             $res['code'] = 1;
         }
        header("Content-type: application/json");
        echo json_encode($res);
        exit();
        
    }
    if ($s == 'changeRoomRank') {
     	if(!canEditRoom()){
    		exit();
    	}
    	$target = escape($_POST['target']);
    	$rank = escape($_POST['room_staff_rank']);
    	$user = userRoomDetails($target);
    	if(empty($target)){
    		$res['code'] = 2;
    		exit();
    	}
    	if(!canRoomAction($user, 6)){
    		$res['code'] = 0;
    		exit();
    	}
    	if($rank > 0){
    		if(checkMod($user['user_id'])){
    			$mysqli->query("INSERT INTO boom_room_staff ( room_id, room_staff, room_rank) VALUES ('{$data['user_roomid']}', '{$user['user_id']}', '$rank')");
    		}
    		else {
    			$mysqli->query("UPDATE boom_room_staff SET room_rank = '$rank' WHERE room_id = '{$data['user_roomid']}' AND room_staff = '{$user['user_id']}'");
    		}
    		$mysqli->query("DELETE FROM boom_room_action WHERE action_user = '{$user['user_id']}' AND action_room = '{$data['user_roomid']}'");
    		$mysqli->query("UPDATE boom_users SET user_role = '$rank', room_mute = '0' WHERE user_id = '{$user['user_id']}' AND user_roomid = '{$data['user_roomid']}'");
    	}
    	else {
    		$mysqli->query("DELETE FROM boom_room_staff WHERE room_staff = '{$user['user_id']}' AND room_id = '{$data['user_roomid']}'");
    		$mysqli->query("UPDATE boom_users SET user_role = 0 WHERE user_id = '{$user['user_id']}' AND user_roomid = '{$data['user_roomid']}'");
    	}
    	boomConsole('change_room_rank', array('target'=> $user['user_id'], 'rank'=>$rank));
    	$res['code'] = 1;
        header("Content-type: application/json");
        echo json_encode($res);
        exit();        
    }
    if ($s == 'saveRoom') {
    	if(!canEditRoom()){
    		$res['msg'] = 'You do not have permission to add a room or your level is not allowed';
    		exit();
    	}
    	$player_check = 0;
    	$name = escape($_POST['set_room_name']);
    	$description = escape($_POST['set_room_description']);
    	$password = escape($_POST['set_room_password']);
    	if(isset($_POST['set_room_player'])){
    		$player = escape($_POST['set_room_player']);
    		$player_check = 1;
    	}
    	$get_room = $mysqli->query("SELECT * FROM boom_rooms WHERE room_id = '{$data['user_roomid']}'");
    	$room = $get_room->fetch_assoc();
    	
    	if(roomExist($name, $data['user_roomid'])){
    		$res['code'] =  2;
    		exit();
    	}
    	if(isToolong($description, $cody['max_description'])){
    		$res['code'] =  0;
    		exit();
    	}
    	if($name == '' || checkName($name) || strlen($name) > $cody['max_room_name'] ){
    		$res['code'] =  4;
    		exit();
    	}
    	if($data['user_roomid'] == 1){
    		$password = '';
    	}
    	if($player_check == 1){
    		if($player != 0){
    			if($player != $room['room_player_id']){
    				$check_player = $mysqli->query("SELECT * FROM boom_radio_stream WHERE id = '$player'");
    				if($check_player->num_rows > 0){
    					$setplay = $check_player->fetch_assoc();
    					$player_id = $setplay['id'];
    				}
    				else {
    					$player_id = $room['room_player_id'];
    				}
    			}
    			else {
    				$player_id = $room['room_player_id'];
    			}
    		}
    		else {
    			$player_id = 0;
    		}
    	}
    	else {
    		$player_id = 0;
    	}
    	$update= $mysqli->query("UPDATE boom_rooms SET room_name = '$name', description = '$description', password = '$password', room_player_id = '$player_id' WHERE room_id = '{$data['user_roomid']}'");
        if($update){
            $res['code'] = 1;
            $res['msg'] = 'The room has been Updated successfully';
        }
    	

        header("Content-type: application/json");
        echo json_encode($res);
        exit();        
        
    }
}

?>