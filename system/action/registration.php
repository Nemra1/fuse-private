<?php


require __DIR__ . "./../config.php";
if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["age"]) && isset($_POST["gender"])) {
    echo userregistration();
    exit;
}
echo 2;

function userRegistration(){
    global $mysqli, $data, $cody;
    $user_ip = getIp();
    $user_name = escape($_POST["username"]);
    $user_password = escape($_POST["password"]);
    $dlang = getLanguage();
    $user_email = escape($_POST["email"]);
    $user_gender = escape($_POST["gender"]);
    $user_age = escape($_POST["age"]);
    // Check if referrer_id exists from the form
    $referrer_id = isset($_POST['referrer_id']) ? intval(escape($_POST['referrer_id'])) : NULL;

    if (!registration()) {
        return 0;
    }
    if (!boomCheckRecaptcha()) {
        return 7;
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
    if (!validAge($user_age)) {
        return 13;
    }
    if (!validGender($user_gender)) {
        return 14;
    }
    if (!boomOkRegister($user_ip)) {
        return 16;
    }
    if (!boomUsername($user_name)) {
        return 5;
    }
    $user_password = encrypt($user_password);
    $system_user = ["name" => $user_name, "password" => $user_password, "email" => $user_email, "language" => $dlang, "gender" => $user_gender, "avatar" => genderAvatar($user_gender), "age" => $user_age, "verify" => $data["activation"], "ip" => $user_ip];
    $user = boomInsertUser($system_user);
    if (empty($user)) {
        return 0;
    }
    return 1;
}

?>