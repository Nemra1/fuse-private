<?php
/**
* FuseChat
*
* @package FuseChat
* @author www.nemra-1.com
* @copyright 2020
* @terms any use of this script without a legal license is prohibited
* all the content of FuseChat is the propriety of BoomCoding and Cannot be 
* used for another project.
*/
session_start();
// Ensure $load_addons is defined
if (!isset($load_addons)) {
    echo json_encode(['status' => 'error', 'message' => 'Addons not loaded properly.']);
    exit();
}
$boom_access = 0;
require(dirname(dirname(__FILE__)) . "/vendor/autoload.php");
require("database.php");
require("variable.php");
require("function.php");
require("function_all.php");
if (!isset($_COOKIE[BOOM_PREFIX . 'userid']) || !isset($_COOKIE[BOOM_PREFIX . 'utk'])) {
    echo json_encode(['status' => 'error', 'message' => 'User authentication failed.']);
    exit();
}
if (!checkToken()) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid token.']);
    exit();
}
require(BOOM_PATH . "/addons/" . $load_addons . "/system/addons_function.php");
$mysqli = @new mysqli(BOOM_DHOST, BOOM_DUSER, BOOM_DPASS, BOOM_DNAME);
$mysqli->query("SET NAMES 'utf8mb4'");
if (mysqli_connect_errno()) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}
// Securely escape the cookie values
$pass = escape($_COOKIE[BOOM_PREFIX . 'utk']);
$ident = escape($_COOKIE[BOOM_PREFIX . 'userid']);
// Using a prepared statement to avoid SQL injection
$stmt = $mysqli->prepare("SELECT boom_setting.*, boom_users.*, boom_addons.* 
                          FROM boom_users 
                          JOIN boom_setting ON boom_setting.id = 1
                          JOIN boom_addons ON boom_addons.addons = ? 
                          WHERE boom_users.user_id = ? 
                          AND boom_users.user_password = ?");
$stmt->bind_param("sis", $load_addons, $ident, $pass);
$stmt->execute();
$get_data = $stmt->get_result();
if ($get_data->num_rows > 0) {
    $data = $get_data->fetch_assoc();
    $boom_access = 1;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user credentials or no matching record.']);
    exit();
}
require("language/{$data['user_language']}/language.php");
require(addonsLang($load_addons));
date_default_timezone_set($data['user_timezone']);
?>
