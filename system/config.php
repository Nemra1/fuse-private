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
	////header("X-Frame-Options: DENY"); // Or use SAMEORIGIN if you need some frames
	//header("X-XSS-Protection: 1; mode=block");
	// Improved Content-Security-Policy
	//header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'; style-src 'self' 'unsafe-inline';");
}
if ($check_install != 1) {
    $chat_install = 2;
} else {
    // Secure Database Connection with proper error handling
    $mysqli = @new mysqli(BOOM_DHOST, BOOM_DUSER, BOOM_DPASS, BOOM_DNAME);
    $mysqli->query("SET NAMES 'utf8mb4'");

    // Check if the connection is successful
    if (mysqli_connect_errno()) {
        $chat_install = 3;
    } else {
        $chat_install = 1;

        // Only proceed if valid cookies exist
        if (isset($_COOKIE[BOOM_PREFIX . 'userid']) && isset($_COOKIE[BOOM_PREFIX . 'utk'])) {
            // Escape inputs to prevent SQL injection
            $ident = escape($_COOKIE[BOOM_PREFIX . 'userid']);
            $pass = escape($_COOKIE[BOOM_PREFIX . 'utk']);
            
            // Use prepared statements to prevent SQL injection
            $stmt = $mysqli->prepare("SELECT boom_setting.*, boom_users.* FROM boom_users, boom_setting WHERE boom_users.user_id = ? AND boom_users.user_password = ? AND boom_setting.id = '1'");
            $stmt->bind_param("ss", $ident, $pass); // 'ss' means two string parameters
            $stmt->execute();
            $get_data = $stmt->get_result();

            // Check if any data is returned
            if ($get_data->num_rows > 0) {
                $data = $get_data->fetch_assoc();
                $boom_access = 1;
            } else {
                // Handle invalid credentials or session
                $get_data = $mysqli->query("SELECT * FROM boom_setting WHERE boom_setting.id = '1'");
                $data = $get_data->fetch_assoc();
                sessionCleanup();  // Clean up session if not found
            }
        } else {
            // No valid cookies, proceed to retrieve default settings
            $get_data = $mysqli->query("SELECT * FROM boom_setting WHERE boom_setting.id = '1'");
            $data = $get_data->fetch_assoc();
            sessionCleanup();  // Clean up session
        }

        // Determine the current language and load the corresponding language file
        $cur_lang = getLanguage();
        require("language/" . $cur_lang . "/language.php");
    }
}

// Setting the default timezone based on chat settings
if ($chat_install == 1) {
    date_default_timezone_set("{$data['timezone']}");
} else {
    date_default_timezone_set("America/Montreal");
}
?>
