<?php
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', __DIR__ . DS);
require BASE_PATH . 'vendor/autoload.php';
require_once("system/config.php");

$csrf_token = '';
$token = '';
$f = '';
$s = '';

// Collect and sanitize GET and POST data
if (isset($_GET['csrf_token']) || isset($_POST['csrf_token'])) {
    $csrf_token = cl_rn_strip($_GET['csrf_token'] ?? $_POST['csrf_token'], 0);
}
if (isset($_GET['token']) || isset($_POST['token'])) {
    $token = cl_rn_strip($_GET['token'] ?? $_POST['token'], 0);
}
if (isset($_GET['f']) || isset($_POST['f'])) {
    $f = cl_rn_strip($_GET['f'] ?? $_POST['f'], 0);
}
if (isset($_GET['s']) || isset($_POST['s'])) {
    $s = cl_rn_strip($_GET['s'] ?? $_POST['s'], 0);
}

$hash_id = $token; // Assuming token is also used as hash_id.

$allowed_actions = array(
    'wallet', 'payment_gateway', 'bot_speakers', 'one_signal', 'gifts', 'day_mode', 
    'action_member', 'store', 'room_icon', 'action_room', 'login_as', 'system_login'
);

$non_login_actions = array(
    'payment_gateway', // No login required for this action.
);
// Ensure the request is AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    exit("Restricted Area");
}
// Allow access only to valid actions
if (!in_array($f, $allowed_actions)) {
    exit("Restricted Area");
}

// Sanitize file name and validate existence
$action_file = 'system/action/' . $f . '.php';
if (file_exists($action_file) && is_readable($action_file)) {
    include $action_file;
} else {
    exit("Invalid action file.");
}

mysqli_close($mysqli); // Close database connection
unset($data); // Unset data object
exit();
?>
