<?php
/**
 * FuseChat
 *
 * @package FuseChat
 * @author www.nemra-1.com
 * @copyright 2020
 * @terms Unauthorized use of this script without a valid license is prohibited.
 * All content of FuseChat is the property of BoomCoding and cannot be used in another project.
 */

session_start();

$boom_access = 0;
require(dirname(dirname(__FILE__)) . "/vendor/autoload.php");
require("database.php");
require("variable.php");
require("function.php");
require("function_all.php");
require(dirname(dirname(__FILE__)) . "/system/webrtc/voice_call/function_call.php");
require "socket.php";



// Enable caching in production mode
if ($cody['dev_mode'] === 1) {
	header("Cache-Control: no-cache, no-store, must-revalidate");
	header("Pragma: no-cache");
	header("Expires: 0");
} else {
	header("Cache-Control: public, max-age=31536000, immutable"); // Cache for production
}
if($cody['secure_header'] === 1) {
	// Security headers
	//header("X-Content-Type-Options: nosniff");
	//header("X-Frame-Options: DENY"); // Or use SAMEORIGIN if you need some frames
	//header("X-XSS-Protection: 1; mode=block");
	// Improved Content-Security-Policy
	//header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'; style-src 'self' 'unsafe-inline';");
}

// Validate authentication and CSRF token
if (!checkToken() || !isset($_COOKIE[BOOM_PREFIX . 'userid']) || !isset($_COOKIE[BOOM_PREFIX . 'utk']) || !validateAuth()) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access.']));
}
// Secure database connection
$mysqli = new mysqli(BOOM_DHOST, BOOM_DUSER, BOOM_DPASS, BOOM_DNAME);
$mysqli->set_charset("utf8mb4");
if ($mysqli->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed.']));
}
// Securely retrieve user authentication credentials
$pass = $_COOKIE[BOOM_PREFIX . 'utk'] ?? '';
$ident = $_COOKIE[BOOM_PREFIX . 'userid'] ?? '';
// Use a prepared statement to prevent SQL injection
$stmt = $mysqli->prepare("
    SELECT boom_setting.*, boom_users.* 
    FROM boom_users 
    JOIN boom_setting ON boom_setting.id = 1
    WHERE boom_users.user_id = ? 
    AND boom_users.user_password = ?
");
$stmt->bind_param("ss", $ident, $pass);
$stmt->execute();
$get_data = $stmt->get_result();

if ($get_data->num_rows > 0) {
    $data = $get_data->fetch_assoc();
    $boom_access = 1;
} else {
    die(json_encode(['status' => 'error', 'message' => 'Invalid credentials.']));
}
// Sanitize and load language settings
require("language/" . htmlspecialchars($data['user_language']) . "/language.php");
date_default_timezone_set($data['user_timezone']);

?>
