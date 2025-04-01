<?php


require __DIR__ . "./../config_session.php";

if (isset($_POST["new_guest_name"]) && isset($_POST["new_guest_password"]) && isset($_POST["new_guest_email"])) {
    echo guestregistration();
    exit;
}
echo 99;

function guestRegistration()
{
    global $mysqli;
    global $data;
    global $cody;
    $user_name = escape($_POST["new_guest_name"]);
    $user_password = escape($_POST["new_guest_password"]);
    $user_email = escape($_POST["new_guest_email"]);
    $user_ip = getIp();
    if (!guestCanRegister()) {
        return 0;
    }
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
    $ask = 0;
    if (defaultAvatar($data["user_tumb"])) {
        $data["user_rank"] = 1;
        resetAvatar($data);
    }
    if (strictGuest()) {
        $ask = $data["activation"];
    }
    $smail = smailProcess($user_email);
    $mysqli->query("UPDATE boom_users SET user_name = '" . $user_name . "', user_password = '" . $user_password . "', user_email = '" . $user_email . "', user_smail = '" . $smail . "', user_rank = '1', user_verify = '" . $ask . "' WHERE user_id = '" . $data["user_id"] . "'");
    setBoomCookie($data["user_id"], $user_password);
    return 1;
}

?>