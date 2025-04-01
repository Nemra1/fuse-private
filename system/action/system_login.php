<?php
if ($f == 'system_login') {
	$res =[];
	if ($s == 'member_login') {
		$res['code'] = 2;
		$res['password'] = encrypt(escape($_POST["password"]));
		$res['username'] = escape($_POST["username"]);
		$res['user_ip'] = getIp();
		if (empty($res['password']) || empty($res['username']) || $res['password'] == "0") {
			$res['error'] ='Bad login';
			$res['code'] = 1;
		}
		if (isEmail($res['username'])) {
			$validate = $mysqli->query("SELECT * FROM boom_users WHERE user_password = '" . $res['password'] . "' AND user_email = '" . $res['username']. "'");
		} else {
			$validate = $mysqli->query("SELECT * FROM boom_users WHERE user_password = '" . $res['password']. "' AND user_name = '" . $res['username']. "' || temp_pass = '" . $res['password']. "' AND user_name = '" . $res['username'] . "' AND temp_pass != '0'");
		}
		if (0 < $validate->num_rows) {
			$valid = $validate->fetch_assoc();
			$post_time = date("H:i", time());
			$ssesid = $valid["session_id"] + 1;
			$id = $valid["user_id"];
			if ($valid["temp_pass"] == $res['password']) {
				$mysqli->query("UPDATE boom_users SET temp_pass = '0', user_password = '" . $res['password'] . "', user_ip = '" . $res['user_ip'] . "', join_msg = '0', user_roomid = '0', `session_id` = '" . $ssesid . "' WHERE `user_id` = '" . $id . "'");
			} else {
				$mysqli->query("UPDATE boom_users SET user_ip = '" . $res['user_ip'] . "', session_id = '" . $ssesid . "', join_msg = '0', user_roomid = '0' WHERE user_id = '" . $id . "'");
			}
			setBoomCookie($id, $res['password']);
			$res['code'] = 3;
		}
        header("Content-type: application/json");
        echo json_encode($res);
        exit();
		
	}
if ($s == 'guest_login') {
    $res = [];
    $res['code'] = 1;
    $res['guest_lang'] = getLanguage();
    $res['guest_ip'] = getIp();
    $create = 0;

    if (!allowGuest()) {
        $res['code'] = 0;
    }
    if (!boomCheckRecaptcha()) {
        $res['code'] = 6;
    }
    if (!okGuest($res['guest_ip'])) {
        $res['code'] = 16; // Prevent new guest login if already exists
    }

    $res['guest_name'] = trim(escape($_POST["guest_name"]));
    $res['guest_gender'] = trim(escape($_POST["guest_gender"]));
    $res['guest_age'] = trim(escape($_POST["guest_age"]));

    if (!validName($res['guest_name'])) {
        $res['code'] = 4;
    }
    if (!boomUsername($res['guest_name'])) {
        $res['code'] = 5;
    }
    if (guestForm()) {
        if (!validAge($res['guest_age'])) {
            $res['code'] = 13;
        }
        if (!validGender($res['guest_gender'])) {
            $res['code'] = 14;
        }
    }

    // Prevent new guest creation if already exists
    if ($res['code'] == 1) {
        $guest_user = [
            "name" => $res['guest_name'],
            "password" => randomPass(),
            "language" => $res['guest_lang'],
            "ip" => $res['guest_ip'],
            "rank" => 0,
            "avatar" => "default_guest.png",
            "email" => ""
        ];

        if (guestForm()) {
            $guest_user["age"] = $res['guest_age'];
            $guest_user["gender"] = $res['guest_gender'];
        }

        $user = boomInsertUser($guest_user);
        if (empty($user)) {
            $res['code'] = 2;
        }
    }

    header("Content-type: application/json");
    echo json_encode($res);
    exit();
}	
}


?>