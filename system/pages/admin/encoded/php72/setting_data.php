<?php


require __DIR__ . "/../../../../config_session.php";
if (!boomAllow(90)) {
    exit;
}
echo elementTitle($lang["database_management"]);
echo "<div class=\"page_full\">\r\n\t<div class=\"page_element\">\r\n\t\t<div class=\"boom_form\">\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["max_avatar"];
echo "</p>\r\n\t\t\t\t<select id=\"set_max_avatar\">\r\n\t\t\t\t\t";
echo optionCount($data["max_avatar"], 1, 6, 1, "mb");
echo "\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["max_cover"];
echo "</p>\r\n\t\t\t\t<select id=\"set_max_cover\">\r\n\t\t\t\t\t";
echo optionCount($data["max_cover"], 1, 6, 1, "mb");
echo "\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["max_file"];
echo "</p>\r\n\t\t\t\t<select id=\"set_max_file\">\r\n\t\t\t\t\t";
echo optionCount($data["file_weight"], 1, 50, 1, "mb");
echo "\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t\t<button data=\"data_setting\" type=\"button\" class=\"save_admin reg_button theme_btn\"><i class=\"ri-save-line\"></i> ";
echo $lang["save"];
echo "</button>\r\n\t</div>\r\n</div>";

?>