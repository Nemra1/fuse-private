<?php
/**
* Codychat
*
* @package Codychat
* @author www.boomcoding.com
* @copyright 2020
* @terms any use of this script without a legal license is prohibited
* all the content of Codychat is the propriety of BoomCoding and Cannot be 
* used for another project.
*/

session_start();
$boom_access = 0;
require(dirname(dirname(__FILE__))."/vendor/autoload.php");
/*firewall*/
require "firewall.php";  // Load firewall on every request
/*firewall*/

require("database.php");
require("variable.php");
require("function.php");
require("function_all.php");
if ($cody['secure_header'] === 1) {
    require("secure_header.php");
}
if($check_install != 1){
	$chat_install = 2;
}
else {
	$mysqli = @new mysqli(BOOM_DHOST, BOOM_DUSER, BOOM_DPASS, BOOM_DNAME);
	$mysqli->query("SET NAMES 'utf8mb4'");

	if (mysqli_connect_errno()) {
		$chat_install = 3;
	}
	else{
		$chat_install = 1;
		if(isset($_COOKIE[BOOM_PREFIX . 'userid']) && isset($_COOKIE[BOOM_PREFIX . 'utk'])){
			$ident = escape($_COOKIE[BOOM_PREFIX . 'userid']);
			$pass = escape($_COOKIE[BOOM_PREFIX . 'utk']);
			$get_data = $mysqli->query("SELECT boom_setting.*, boom_users.* FROM boom_users, boom_setting WHERE boom_users.user_id = '$ident' AND boom_users.user_password = '$pass' AND boom_setting.id = '1'");
			if($get_data->num_rows > 0){
				$data = $get_data->fetch_assoc();
				$boom_access = 1;
			}
			else {
				$get_data = $mysqli->query("SELECT * FROM boom_setting WHERE boom_setting.id = '1'");
				$data = $get_data->fetch_assoc();
				sessionCleanup();
			}
		}
		else {
			$get_data = $mysqli->query("SELECT * FROM boom_setting WHERE boom_setting.id = '1'");
			$data = $get_data->fetch_assoc();
			sessionCleanup();
		}
		$cur_lang = getLanguage();
		require("language/" . $cur_lang . "/language.php");
	}
}
if($chat_install == 1){
	date_default_timezone_set("{$data['timezone']}");
}
else {
	date_default_timezone_set("America/Montreal");
}

?>