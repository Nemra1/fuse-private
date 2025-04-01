<?php


require __DIR__ . "/../../../../config_session.php";
if (!boomAllow(100)) {
    exit;
}
$good = "<i class=\"ri-chat-check-line success\"></i>";
$bad = "<i class=\"ri-close-circle-line error\"></i>";
$warn = "<i class=\"fa fa-exclamation warn\"></i>";
$check_upload = $good;
$check_avatar = $good;
$check_cover = $good;
$check_gd = $good;
$check_php = $good;
$check_curl = $good;
$check_zip = $good;
$check_mbstring = $good;
if (!is_writable(dirname(BOOM_PATH . "/avatar"))) {
    $check_avatar = $bad;
}
if (!is_writable(dirname(BOOM_PATH . "/cover"))) {
    $check_cover = $bad;
}
if (!is_writable(BOOM_PATH . "/system/database.php")) {
    $check_database = $bad;
}
if (!is_writable(dirname(BOOM_PATH . "/upload"))) {
    $check_upload = $bad;
}
if (!extension_loaded("gd") && !function_exists("gd_info")) {
    $check_gd = $bad;
}
if (!version_compare(PHP_VERSION, "5.6.0", ">=")) {
    $check_php = $bad;
}
if (version_compare(PHP_VERSION, "7.3.0", ">=")) {
    $check_php = $warn;
}
if (!function_exists("curl_init")) {
    $check_curl = $bad;
}
if (!extension_loaded("zip")) {
    $check_zip = $bad;
}
if (!extension_loaded("mbstring")) {
    $check_mbstring = $bad;
}
echo elementTitle($lang["system_diagnostic"]);
echo "<div class=\"page_full\">\r\n\t<div class=\"page_element\">\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\tPhp version 5.6 - 7.2\r\n\t\t\t</div>\r\n\t\t\t<div class=\"listing_reg_icon\">\r\n\t\t\t\t";
echo $check_php;
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\tGD is installed\r\n\t\t\t</div>\r\n\t\t\t<div class=\"listing_reg_icon\">\r\n\t\t\t\t";
echo $check_gd;
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\tCurl is installed\r\n\t\t\t</div>\r\n\t\t\t<div class=\"listing_reg_icon\">\r\n\t\t\t\t";
echo $check_curl;
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\tZip is installed\r\n\t\t\t</div>\r\n\t\t\t<div class=\"listing_reg_icon\">\r\n\t\t\t\t";
echo $check_zip;
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\tMbstring is installed\r\n\t\t\t</div>\r\n\t\t\t<div class=\"listing_reg_icon\">\r\n\t\t\t\t";
echo $check_mbstring;
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\tavatar folder is writable\r\n\t\t\t</div>\r\n\t\t\t<div class=\"listing_reg_icon\">\r\n\t\t\t\t";
echo $check_avatar;
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\tcover folder is writable\r\n\t\t\t</div>\r\n\t\t\t<div class=\"listing_reg_icon\">\r\n\t\t\t\t";
echo $check_cover;
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\tupload folder is writable\r\n\t\t\t</div>\r\n\t\t\t<div class=\"listing_reg_icon\">\r\n\t\t\t\t";
echo $check_upload;
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t</div>\r\n</div>\r\n";
echo elementTitle($lang["system_info"]);
echo "<div class=\"page_full\">\r\n\t<div class=\"page_element\">\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\t";
echo $lang["current_version"];
echo " ";
echo $data["version"];
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\t";
echo $lang["php_version"];
echo " ";
echo PHP_VERSION;
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t\t<div class=\"listing_reg\">\r\n\t\t\t<div class=\"listing_reg_content\">\r\n\t\t\t\t";
echo $lang["max_upload"];
echo " ";
echo ini_get("upload_max_filesize");
echo "\t\t\t</div>\r\n\t\t</div>\r\n\t</div>\r\n</div>";

?>