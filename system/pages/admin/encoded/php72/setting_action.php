<?php


require __DIR__ . "/../../../../config_session.php";
if (!boomAllow(70)) {
    exit;
}
echo elementTitle($lang["manage_action"]);
echo "<div class=\"page_full\">\r\n\t<div>\t\t\r\n\t\t<div class=\"tab_menu\">\r\n\t\t\t<ul>\r\n\t\t\t\t<li class=\"tab_menu_item tab_selected\" data=\"action_filter\" data-z=\"muted_filter\">";
echo $lang["muted"];
echo "</li>\r\n\t\t\t\t";
if (canKick()) {
    echo "\t\t\t\t<li class=\"tab_menu_item\" data=\"action_filter\" data-z=\"kicked_filter\">";
    echo $lang["kicked"];
    echo "</li>\r\n\t\t\t\t";
}
echo "\t\t\t\t";
if (canBan()) {
    echo "\t\t\t\t<li class=\"tab_menu_item\" data=\"action_filter\" data-z=\"banned_filter\">";
    echo $lang["banned"];
    echo "</li>\r\n\t\t\t\t";
}
echo "\t\t\t</ul>\r\n\t\t</div>\r\n\t</div>\r\n\t<div id=\"action_filter\">\r\n\t\t<div id=\"muted_filter\" class=\"tab_zone\">\r\n\t\t\t<div class=\"page_element\">\r\n\t\t\t\t<div id=\"action_muted_list\">\r\n\t\t\t\t\t";
echo getActionList("muted");
echo "\t\t\t\t</div>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t\t";
if (canKick()) {
    echo "\t\t<div id=\"kicked_filter\" class=\"hide_zone tab_zone\">\r\n\t\t\t<div class=\"page_element\">\r\n\t\t\t\t<div id=\"action_muted_list\">\r\n\t\t\t\t\t";
    echo getActionList("kicked");
    echo "\t\t\t\t</div>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t\t";
}
echo "\t\t";
if (canBan()) {
    echo "\t\t<div id=\"banned_filter\" class=\"tab_zone hide_zone\">\r\n\t\t\t<div class=\"page_element\">\r\n\t\t\t\t<div id=\"action_banned_list\">\r\n\t\t\t\t\t";
    echo getActionList("banned");
    echo "\t\t\t\t</div>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t\t";
}
echo "\t</div>\r\n</div>";

?>