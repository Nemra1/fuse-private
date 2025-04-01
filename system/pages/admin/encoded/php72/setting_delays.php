<?php


require __DIR__ . "/../../../../config_session.php";
if (!boomAllow(90)) {
    exit;
}
echo elementTitle($lang["delay_settings"]);
echo "<div class=\"page_full\">\r\n\t<div class=\"page_element\">\r\n\t\t<div class=\"boom_form\">\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["innactive_logout"];
echo "</p>\r\n\t\t\t\t<select id=\"set_act_delay\">\r\n\t\t\t\t\t<option value=\"0\">";
echo $lang["never"];
echo "</option>\r\n\t\t\t\t\t";
echo optionMinutes($data["act_delay"], [5, 10, 15, 30, 60, 120, 180, 360, 720, 1440, 2880, 10080]);
echo "\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["chat_delete"];
echo "</p>\r\n\t\t\t\t<select id=\"set_chat_delete\">\r\n\t\t\t\t\t<option value=\"0\">";
echo $lang["never"];
echo "</option>\r\n\t\t\t\t\t";
echo optionMinutes($data["chat_delete"], [30, 60, 180, 360, 720, 1440, 2880, 4320, 5760, 7200, 8640, 10080, 20160, 43200, 86400, 129600, 518400]);
echo "\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["private_delete"];
echo "</p>\r\n\t\t\t\t<select id=\"set_private_delete\">\r\n\t\t\t\t\t<option value=\"0\">";
echo $lang["never"];
echo "</option>\r\n\t\t\t\t\t";
echo optionMinutes($data["private_delete"], [30, 60, 180, 360, 720, 1440, 2880, 4320, 5760, 7200, 8640, 10080, 20160, 43200, 86400, 129600, 518400]);
echo "\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["wall_delete"];
echo "</p>\r\n\t\t\t\t<select id=\"set_wall_delete\">\r\n\t\t\t\t\t<option value=\"0\">";
echo $lang["never"];
echo "</option>\r\n\t\t\t\t\t";
echo optionMinutes($data["wall_delete"], [1440, 2880, 4320, 5760, 7200, 8640, 10080, 43200, 86400, 129600, 518400]);
echo "\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["member_delete"];
echo "</p>\r\n\t\t\t\t<select id=\"set_member_delete\">\r\n\t\t\t\t\t<option value=\"0\">";
echo $lang["never"];
echo "</option>\r\n\t\t\t\t\t";
echo optionMinutes($data["member_delete"], [43200, 86400, 129600, 518400]);
echo "\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["room_delete"];
echo "</p>\r\n\t\t\t\t<select id=\"set_room_delete\">\r\n\t\t\t\t\t<option value=\"0\">";
echo $lang["never"];
echo "</option>\r\n\t\t\t\t\t";
echo optionMinutes($data["room_delete"], [60, 120, 180, 360, 720, 1440, 2880, 4320, 5760, 7200, 8640, 10080, 20160, 43200, 86400, 129600]);
echo "\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t\t<button data=\"delays\" type=\"button\" class=\"save_admin reg_button theme_btn\"><i class=\"ri-save-line\"></i> ";
echo $lang["save"];
echo "</button>\r\n\t</div>\r\n</div>";

?>