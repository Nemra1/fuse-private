<?php


require __DIR__ . "/../../../../config_session.php";
if (!boomAllow(80)) {
    exit;
}
echo elementTitle($lang["ban_management"]);
echo "<div class=\"page_full\">\r\n\t<div class=\"page_element\">\r\n\t\t<div id=\"ip_search\">\r\n\t\t\t<div class=\"search_bar\">\r\n\t\t\t\t<input id=\"search_ip\" placeholder=\"&#xf002;\" class=\"full_input\" type=\"text\"/>\r\n\t\t\t\t<div class=\"clear\"></div>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t</div>\r\n\t<div class=\"page_element\">\r\n\t\t<div id=\"ip_list\">\r\n\t\t\t";
echo listadminip();
echo "\t\t</div>\r\n\t</div>\r\n</div>";

function listAdminIp()
{
    global $mysqli;
    global $lang;
    $list_ip = "";
    $getip = $mysqli->query("SELECT * FROM boom_banned ORDER BY ip ASC");
    if (0 < $getip->num_rows) {
        while ($ip = $getip->fetch_assoc()) {
            $list_ip .= boomTemplate("element/admin_ip", $ip);
        }
    } else {
        $list_ip .= emptyZone($lang["empty"]);
    }
    return $list_ip;
}

?>