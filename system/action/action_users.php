<?php


require __DIR__ . "./../config_session.php";
// Handle vpn permisson requests
if(isset($_POST['target'], $_POST['set_user_vpn'])){
	$user_vpn= escape($_POST['set_user_vpn'], true);
	$target = escape($_POST['target'], true);
	$update = cl_update_user_data($target,(array(
	    "uvpn" => escape($user_vpn),
	    )));
    echo $update;
exit;
}

if (isset($_POST["change_rank"]) && isset($_POST["target"])) {
    echo boomchangeuserrank();
} else {
    if (isset($_POST["user_color"]) && isset($_POST["user_font"]) && isset($_POST["user"])) {
        echo boomchangecolor();
        exit;
    }
    if (isset($_POST["account_status"]) && isset($_POST["target"])) {
        echo boomchangeuserverify();
    } else {
        if (isset($_POST["delete_user_account"])) {
            echo boomdeleteaccount();
            exit;
        }
        if (isset($_POST["set_user_email"]) && isset($_POST["set_user_id"])) {
            echo staffuseremail();
            exit;
        }
        if (isset($_POST["set_user_about"]) && isset($_POST["target_about"])) {
            echo staffuserabout();
            exit;
        }
        if (isset($_POST["target_id"]) && isset($_POST["user_new_password"])) {
            echo staffchangepassword();
            exit;
        }
        if (isset($_POST["target_id"]) && isset($_POST["user_new_mood"])) {
            echo staffchangemood();
            exit;
        }
        if (isset($_POST["target_id"]) && isset($_POST["user_new_name"])) {
            echo staffchangeusername();
            exit;
        }
        if (isset($_POST["create_user"]) && isset($_POST["create_name"]) && isset($_POST["create_password"]) && isset($_POST["create_email"]) && isset($_POST["create_age"]) && isset($_POST["create_gender"])) {
            echo staffcreateuser();
            exit;
        }
    }
}
if (isset($_POST["user_language"]) && isset($_POST["user_country"]) && isset($_POST["user_timezone"])) {
    echo setuserlocation();
    exit;
}
exit;

function setUserLocation(){
    global $mysqli;
    global $data;
    $language = boomSanitize($_POST["user_language"]);
    $country = escape($_POST["user_country"]);
    $new_timezone = escape($_POST["user_timezone"]);
    require BOOM_PATH . "/system/element/timezone.php";
    $refresh = 0;
    if (file_exists(BOOM_PATH . "/system/language/" . $language . "/language.php")) {
        $mysqli->query("UPDATE boom_users SET user_language = '" . $language . "' WHERE user_id = '" . $data["user_id"] . "'");
        setBoomLang($language);
        if ($language != $data["user_language"]) {
            $refresh++;
        }
    }
    if (in_array($new_timezone, $timezone)) {
        $mysqli->query("UPDATE boom_users SET user_timezone = '" . $new_timezone . "' WHERE user_id = '" . $data["user_id"] . "'");
        if ($new_timezone != $data["user_timezone"]) {
            $refresh++;
        }
    }
    if (validCountry($country)) {
        $mysqli->query("UPDATE boom_users SET country = '" . $country . "' WHERE user_id = '" . $data["user_id"] . "'");
    }
    if (0 < $refresh) {
        return 1;
    }
    return 0;
}

function boomChangeUserRank(){
    global $mysqli;
    global $data;
    $target = escape($_POST["target"]);
    $rank = escape($_POST["change_rank"]);

            $user = userDetails($target);
   
            if (empty($user)) {
                return 3;
            }
            if (!canRankUser($user)) {
                return 0;
            }
            if ($user["user_rank"] == $rank) {
                return 2;
            }
            userReset($user, $rank);
            boomNotify("rank_change", ["target" => $target, "source" => "rank_change", "rank" => $rank]);
            if (isStaff($rank)) {
                $mysqli->query("UPDATE boom_users SET room_mute = '0', user_private = 1, user_mute = 0, user_regmute = 0 WHERE user_id = '" . $target . "'");
                $mysqli->query("DELETE FROM boom_room_action WHERE action_user = '" . $target . "'");
                $mysqli->query("DELETE FROM boom_ignore WHERE ignored = '" . $target . "'");
            }
            boomConsole("change_rank", ["target" => $user["user_id"], "rank" => $rank]);
            return 1;
}

function boomChangeUserVerify(){
    global $mysqli;
    global $data;
    $target = escape($_POST["target"]);
    $status = escape($_POST["account_status"]);
    if (!boomAllow(80)) {
        return 0;
    }
    $verify = 0;
    $addthis = "";
    if ($status != 1 && $status != 0) {
        return 0;
    }
    $user = userDetails($target);
    if (!canEditUser($user, 80)) {
        return 0;
    }
    if (empty($user)) {
        return 3;
    }
    if ($status == 0) {
        $vefify = 0;
        $addthis = "";
        if (userHaveEmail($user)) {
            $verify = $data["activation"];
        }
        if ($verify == 1) {
            $addthis = ", user_action = user_action + 1";
        }
        $mysqli->query("UPDATE boom_users SET verified = '0', user_verify = '" . $verify . "' " . $addthis . " WHERE user_id = '" . $user["user_id"] . "'");
    } else {
        $mysqli->query("UPDATE boom_users SET verified = '1', user_verify = '0' WHERE user_id = '" . $user["user_id"] . "'");
    }
    boomConsole("change_verify", ["target" => $user["user_id"]]);
    return 1;
}

function boomChangeColor(){
    global $mysqli;
    global $data;
    global $cody;
    $color = escape($_POST["user_color"]);
    $font = escape($_POST["user_font"]);
    $id = escape($_POST["user"]);
    $user = userDetails($id);

    if (!canModifyColor($user)) {
        return 0;
    }
    if (!validNameColor($color)) {
        return 0;
    }
    if (!validNameFont($font)) {
        echo 0;
        exit;
    }
    $mysqli->query("UPDATE boom_users SET user_color = '" . $color . "', user_font = '" . $font . "' WHERE user_id = '" . $id . "'");
    boomConsole("change_color", ["target" => $id]);
    return 1;
}

function staffUserEmail(){
    global $mysqli;
    global $data;
    global $cody;
    $user_email = escape($_POST["set_user_email"]);
    $user_id = escape($_POST["set_user_id"]);
    $user = userDetails($user_id);
    if (!checkEmail($user_email) && !boomSame($user_email, $user["user_email"])) {
        return 2;
    }
    if (!isEmail($user_email)) {
        return 3;
    }
    if (!canModifyEmail($user)) {
        return 0;
    }
    $smail = smailProcess($user_email);
    $mysqli->query("UPDATE boom_users SET user_email = '" . $user_email . "', user_smail = '" . $smail . "' WHERE user_id = '" . $user_id . "'");
    boomConsole("edit_profile", ["target" => $user_id]);
    return 1;
}

function staffUserAbout(){
    global $mysqli;
    global $data;
    global $cody;
    $user_about = clearBreak($_POST["set_user_about"]);
    $user_about = escape($user_about);
    $user_id = escape($_POST["target_about"]);
    $user = userDetails($user_id);
    if (!canModifyAbout($user)) {
        return 0;
    }
    if (isTooLong($user_about, 900)) {
        return 0;
    }
    if (isBadText($user_about)) {
        echo 2;
        exit;
    }
    $mysqli->query("UPDATE boom_users SET user_about = '" . $user_about . "' WHERE user_id = '" . $user_id . "'");
    boomConsole("edit_profile", ["target" => $user["user_id"]]);
    return 1;
}

function staffChangeUsername()
{
    global $mysqli;
    global $data;
    $new_name = escape($_POST["user_new_name"]);
    $target = escape($_POST["target_id"]);
    $user = userDetails($target);
    if (empty($user)) {
        return 0;
    }
    if (!canModifyName($user)) {
        return 0;
    }
    if ($new_name == $user["user_name"]) {
        return 1;
    }
    if (!validName($new_name)) {
        return 2;
    }
    if (!boomSame($new_name, $user["user_name"]) && !boomUsername($new_name)) {
        return 3;
    }
    $mysqli->query("UPDATE boom_users SET user_name = '" . $new_name . "' WHERE user_id = '" . $user["user_id"] . "'");
    if (isBot($user)) {
        $mysqli->query("UPDATE boom_addons SET bot_name = '" . $new_name . "' WHERE bot_id = '" . $user["user_id"] . "'");
    }
    boomConsole("rename_user", ["target" => $user["user_id"], "custom" => $user["user_name"]]);
    clearNotifyAction($user["user_id"], "name_change");
    boomNotify("name_change", ["target" => $user["user_id"], "source" => "name_change", "custom" => $new_name]);
    changeNameLog($user, $new_name);
    return 1;
}

function staffChangeMood()
{
    global $mysqli;
    global $data;
    $mood = escape($_POST["user_new_mood"]);
    $target = escape($_POST["target_id"]);
    $user = userDetails($target);
    if (!canModifyMood($user)) {
        return 0;
    }
    if ($mood == $user["user_mood"]) {
        return getMood($user);
    }
    if (isBadText($mood)) {
        return 2;
    }
    if (isTooLong($mood, 40)) {
        return 0;
    }
    $mysqli->query("UPDATE boom_users SET user_mood = '" . $mood . "' WHERE user_id = '" . $user["user_id"] . "'");
    boomConsole("mood_user", ["target" => $user["user_id"], "custom" => $user["user_name"]]);
    $u = userDetails($user["user_id"]);
    return getMood($u);
}

function staffChangePassword()
{
    global $mysqli;
    global $data;
    $pass = escape($_POST["user_new_password"]);
    $target = escape($_POST["target_id"]);
    $user = userDetails($target);
    if (!canModifyPassword($user)) {
        return 0;
    }
    if (!boomValidPassword($pass)) {
        return 2;
    }
    $new_pass = encrypt($pass);
    $mysqli->query("UPDATE boom_users SET user_password = '" . $new_pass . "' WHERE user_id = '" . $user["user_id"] . "'");
    boomConsole("pass_user", ["target" => $user["user_id"], "custom" => $user["user_name"]]);
    return 1;
}

function boomDeleteAccount()
{
    global $mysqli;
    global $data;
    global $cody;
    $id = escape($_POST["delete_user_account"]);
    $user = userDetails($id);

    if (empty($user)) {
        return 3;
    }
    if (!canDeleteUser($user)) {
        return 0;
    }
    clearUserData($user);
    boomConsole("delete_account", ["target" => $id, "custom" => $user["user_name"]]);
    return 1;
}

function staffCreateUser()
{
    global $mysqli;
    global $data;
    $name = escape($_POST["create_name"]);
    $pass = escape($_POST["create_password"]);
    $email = escape($_POST["create_email"]);
    $age = escape($_POST["create_age"]);
    $gender = escape($_POST["create_gender"]);

    if (!boomAllow(90)) {
        return 2;
    }
    if ($name == "" || $pass == "" || $email == "") {
        return 2;
    }
    if (!validName($name)) {
        return 3;
    }
    if (!boomUsername($name)) {
        return 4;
    }
    if (!isEmail($email)) {
        return 5;
    }
    if (!checkEmail($email)) {
        return 6;
    }
    if (!checkSmail($email)) {
        return 6;
    }
    if (!validAge($age)) {
        $age = 0;
    }
    if (!validGender($gender)) {
        $gender = 1;
    }
    $enpass = encrypt($pass);
    $system_user = ["name" => $name, "password" => $enpass, "email" => $email, "language" => $data["language"], "verified" => 1, "cookie" => 0, "gender" => $gender, "avatar" => genderAvatar($gender), "age" => $age];
    $user = boomInsertUser($system_user);
    boomConsole("create_user", ["target" => $user["user_id"]]);
    return 1;
}

?>