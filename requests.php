<?php
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', __DIR__ . DS);
require BASE_PATH.'vendor/autoload.php';
//require BASE_PATH.'system/config_session.php';
require_once("system/config.php");

$token ='';
$f = '';
$s = '';
if (isset($_GET['token'])) {
    $token = cl_rn_strip($_GET['token'], 0);
}
if (isset($_POST['token'])) {
    $token = cl_rn_strip($_POST['token'], 0);
}
if (isset($_GET['f'])) {
    $f = cl_rn_strip($_GET['f'], 0);
}
if (isset($_GET['s'])) {
    $s = cl_rn_strip($_GET['s'], 0);
}
if (isset($_POST['f'])) {
    $f = cl_rn_strip($_POST['f'], 0);
}
if (isset($_POST['s'])) {
    $s = cl_rn_strip($_POST['s'], 0);
}
$hash_id = '';
if (!empty($_POST['token'])) {
    $hash_id = cl_rn_strip($_POST['token'], 0);
} else if (!empty($_GET['token'])) {
    $hash_id = cl_rn_strip($_GET['token'], 0);
} else if (!empty($_GET['token'])) {
    $hash_id = cl_rn_strip($_GET['token'], 0);
} else if (!empty($_POST['token'])) {
    $hash_id = cl_rn_strip($_POST['token'], 0);
}
$allow_array = array(
    'wallet',
    'payment_gateway',
    'bot_speakers',
    'one_signal',
    'gifts',
    'day_mode',
    'action_member',
    'store',
    'room_icon',
    'action_room',
    'login_as',
	'action',
	'action_private',
	'admin_actions',
);
$non_login_array = array(
	'payment_gateway',
);
if (!in_array($f, $allow_array)) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            exit("Restrcited Area");
        }
    } else {
        exit("Restrcited Area");
    }
}

$files = scandir('system/action');
unset($files[0]);
unset($files[1]);
if (file_exists('system/action/' . $f . '.php') && in_array($f . '.php', $files)) {
    include 'system/action/' . $f . '.php';
}
mysqli_close($mysqli);
unset($data);
exit();

?>
