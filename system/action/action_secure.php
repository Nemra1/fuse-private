<?php


require __DIR__ . "./../config_session.php";

if (isset($_POST["actual_pass"]) && isset($_POST["new_pass"]) && isset($_POST["repeat_pass"]) && isset($_POST["change_password"])) {
    echo changemypassword();
    exit;
}
if (isset($_POST["save_email"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    echo changemyemail();
    exit;
}
if (isset($_POST["delete_account_password"]) && isset($_POST["delete_my_account"])) {
    echo deletemyaccount();
    exit;
}
if (isset($_POST["cancel_delete_account"])) {
    echo canceldelete();
    exit;
}
if (isset($_POST["secure_name"]) && isset($_POST["secure_password"]) && isset($_POST["secure_email"])) {
    echo accountsecurity();
    exit;
}

function accountSecurity()
{
    global $mysqli;
    global $data;
    global $cody;
    $user_name = escape($_POST["secure_name"]);
    $user_password = escape($_POST["secure_password"]);
    $user_email = escape($_POST["secure_email"]);

    $user_ip = getIp();
    if (!validName($user_name)) {
        return 4;
    }
    if (!validEmail($user_email)) {
        return 6;
    }
    if (!checkEmail($user_email)) {
        return 10;
    }
    if (!checkSmail($user_email)) {
        return 10;
    }
    if (!boomValidPassword($user_password)) {
        return 17;
    }
    if (!boomOkRegister($user_ip)) {
        return 16;
    }
    if (!boomUsername($user_name) && !boomSame($user_name, $data["user_name"])) {
        return 5;
    }
    $user_password = encrypt($user_password);
    $smail = smailProcess($user_email);
    $mysqli->query("UPDATE boom_users SET user_name = '" . $user_name . "', user_password = '" . $user_password . "', user_email = '" . $user_email . "', user_smail = '" . $smail . "' WHERE user_id = '" . $data["user_id"] . "'");
    setBoomCookie($data["user_id"], $user_password);
    return 1;
}

function changeMyPassword()
{
    global $mysqli;
    global $data;
    global $cody;
    $pass = escape($_POST["actual_pass"]);
    $new_pass = escape($_POST["new_pass"]);
    $repeat_pass = escape($_POST["repeat_pass"]);
    $actual_encrypt = encrypt($pass);

    if ($actual_encrypt != $data["user_password"] && $pass != $data["temp_pass"]) {
        return 5;
    }
    if ($pass == "" || $new_pass == "" || $repeat_pass == "") {
        return 2;
    }
    if ($new_pass != $repeat_pass) {
        return 3;
    }
    if (30 < strlen($new_pass) || strlen($new_pass) < 4) {
        return 4;
    }
    if ($pass == "0" || $new_pass == "0" || $repeat_pass == "0") {
        return 5;
    }
    $new_encrypted_pass = encrypt($new_pass);
    $mysqli->query("UPDATE boom_users SET user_password = '" . $new_encrypted_pass . "', temp_pass = '0' WHERE user_id = '" . $data["user_id"] . "'");
    setBoomCookie($data["user_id"], $new_encrypted_pass);
    return 1;
}

function changeMyEmail()
{
    global $mysqli;
    global $data;
    global $cody;
    $email = escape($_POST["email"]);
    $password = escape($_POST["password"]);

    if (mustVerify()) {
        return "";
    }
    if (!isMember($data)) {
        return "";
    }
    if (!userPassword($password)) {
        return 3;
    }
    if (!validEmail($email)) {
        return 2;
    }
    if (!checkEmail($email) && !boomSame($email, $data["user_email"])) {
        return 4;
    }
    $smail = smailProcess($email);
    $mysqli->query("UPDATE boom_users SET user_email = '" . $email . "', user_smail = '" . $smail . "' WHERE user_id = '" . $data["user_id"] . "'");
    return 1;
}

function deleteMyAccount()
{
    global $mysqli;
    global $data;
    global $cody;
    $pass = escape($_POST["delete_account_password"]);
    $delay = calDayUp(7);
    if (boomAllow(100) || isBot($data)) {
        return "";
    }
    if (!userPassword($pass)) {
        return 2;
    }
    $mysqli->query("UPDATE boom_users SET user_delete = '" . $delay . "' WHERE user_id = '" . $data["user_id"] . "' AND user_delete = 0");
    return 1;
}

function cancelDelete()
{
    global $mysqli;
    global $data;
    global $cody;
    $mysqli->query("UPDATE boom_users SET user_delete = '0' WHERE user_id = '" . $data["user_id"] . "'");
    return 1;
}

?>