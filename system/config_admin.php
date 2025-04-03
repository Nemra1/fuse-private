<?php
/**
* FuseChat
*
* @package FuseChat
* @author www.nemra-1.com
* @copyright 2020
* @terms Any use of this script without a legal license is prohibited.
* All the content of FuseChat is the property of BoomCoding and cannot be 
* used for another project.
*/

session_start();

// **Improve Session Security**
$boom_access = 0;
require dirname(dirname(__FILE__)) . "/vendor/autoload.php";
require "database.php";
require "variable.php";
require "function.php";
require "function_all.php";
require "function_admin.php";
// **Validate Cookies & Token**
if (!checkToken() || empty($_COOKIE[BOOM_PREFIX . 'userid']) || empty($_COOKIE[BOOM_PREFIX . 'utk'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid session.']);
    exit();
}
// **Secure Database Connection**
$mysqli = new mysqli(BOOM_DHOST, BOOM_DUSER, BOOM_DPASS, BOOM_DNAME);
if ($mysqli->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit();
}
// **Securely Fetch User Credentials**
$ident = $_COOKIE[BOOM_PREFIX . 'userid'];
$pass = $_COOKIE[BOOM_PREFIX . 'utk'];
$stmt = $mysqli->prepare("
    SELECT boom_setting.*, boom_users.* 
    FROM boom_users 
    JOIN boom_setting ON boom_setting.id = 1
    WHERE boom_users.user_id = ? AND boom_users.user_password = ?
");
$stmt->bind_param("is", $ident, $pass);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $boom_access = 1;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Authentication failed.']);
    exit();
}
// **Load Language File**
require "language/{$data['user_language']}/language.php";
// **Set Timezone Securely**
if (!empty($data['user_timezone'])) {
    date_default_timezone_set($data['user_timezone']);
} else {
    date_default_timezone_set("UTC"); // Default fallback
}
?>
