<?php


require __DIR__ . "/../../../../config_session.php";
if (!boomAllow($cody["can_view_console"])) {
    exit;
}
echo elementTitle($lang["system_logs"]);
echo "<div class=\"page_full\">\r\n\t<div class=\"page_element\">\r\n\t\t";
if (boomAllow($cody["can_clear_console"])) {
    echo "\t\t<div class=\"bpad15\">\r\n\t\t\t<button onclick=\"clearConsole();\" class=\"reg_button delete_btn\"><i class=\"ri-delete-bin-2-fill\"></i> ";
    echo $lang["clear"];
    echo "</button>\r\n\t\t</div>\r\n\t\t";
}
echo "\t\t<div id=\"console_logs_box\">\r\n\t\t\t<div class=\"bpad15 console_logs_search\">\r\n\t\t\t\t<input onkeyup=\"searchSystemConsole();\" id=\"search_system_console\" placeholder=\"&#xf002;\" class=\"full_input\" type=\"text\"/>\r\n\t\t\t</div>\r\n\t\t\t<div id=\"console_results\" class=\"box_height\">\r\n\t\t\t</div>\r\n\t\t\t<div id=\"console_spinner\" class=\"vpad10 centered_element\">\r\n\t\t\t\t<i class=\"fa fa-spinner fa-spin text_jumbo\"></i>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t</div>\r\n</div>";

?>