<?php


require __DIR__ . "/../../../../config_session.php";
if (!boomAllow(70)) {
    exit;
}
$result = getdashboard();
echo elementTitle($lang["dashboard"]);
echo "<div class=\"page_full\">\r\n\t<div class=\"page_element\">\r\n\t\t";
echo spbox("ri-group-3-line", $lang["registered"], $result["user_count"], "sp_member");
echo "\t\t";
echo spbox("ri-wechat-pay-line", $lang["online"], $result["online_count"], "sp_online");
echo "\t\t";
echo spbox("ri-women-line", $lang["female"], $result["female_count"], "sp_female");
echo "\t\t";
echo spbox("ri-men-line", $lang["male"], $result["male_count"], "sp_male");
echo "\t\t";
echo spbox("ri-shield-check-fill", $lang["verified"], $result["verified_count"], "sp_verified");
echo "\t\t";
echo spbox("ri-blur-off-line", $lang["muted"], $result["muted_users"], "sp_muted");
echo "\t\t";
echo spbox("ri-flashlight-line", $lang["kicked"], $result["kicked_users"], "sp_kicked");
echo "\t\t";
echo spbox("ri-forbid-line", $lang["banned"], $result["banned_count"], "sp_banned");
echo "\t\t";
if (boomAllow(90)) {
    echo "\t\t\t";
    echo spbox("ri-wechat-line", $lang["chat_logs"], $result["chat_count"], "sp_chat");
    echo "\t\t\t";
    echo spbox("ri-chat-history-line", $lang["private_logs"], $result["private_count"], "sp_private");
    echo "\t\t\t";
    echo spbox("ri-rss-fill", $lang["post_count"], $result["post_count"], "sp_post");
    echo "\t\t\t";
    echo spbox("ri-chat-quote-line", $lang["post_reply"], $result["reply_count"], "sp_reply");
    echo "\t\t";
}
echo "\t\t<div class=\"clear\"></div>\r\n\t</div>\r\n</div>\r\n";
if (boomAllow(90)) {
    echo "<div class=\"page_full\">\r\n\t<div class=\"page_element\">\r\n\t\t<div class=\"listing_reg_content\">\r\n\t\t\t";
    echo $lang["current_version"];
    echo " ";
    echo $data["version"];
    echo "\t\t</div>\r\n\t</div>\r\n</div>\r\n";
}
function spBox($icon, $txt, $val, $cl)
{
    global $lang;
    return "<div class=\"sp_box\">\r\n\t\t\t\t<div class=\"sp_content\">\r\n\t\t\t\t\t<div class=\"sp_icon bcell_mid " . $cl . "\">\r\n\t\t\t\t\t\t<i class=\"" . $icon . "\"></i></div><div class=\"sp_info bcell_mid\">\r\n\t\t\t\t\t\t<p class=\"sp_title\">" . $txt . "</p>\r\n\t\t\t\t\t\t<p class=\"sp_count\">" . $val . "</p>\r\n\t\t\t\t\t</div>\r\n\t\t\t\t</div>\r\n\t\t\t</div>";
}

function getDashboard()
{
    global $mysqli;
    $delay = getDelay();
    if (boomAllow(90)) {
        $request = $mysqli->query("SELECT\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users ) as user_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE last_action >= " . $delay . ") as online_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_sex = 2 ) as female_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_sex = 1 ) as male_count,\r\n\t\t\t\t\t\t( SELECT count(id) FROM boom_private ) as private_count,\r\n\t\t\t\t\t\t( SELECT count(post_id) FROM boom_chat ) as chat_count,\r\n\t\t\t\t\t\t( SELECT count(post_id) FROM boom_post ) as post_count,\r\n\t\t\t\t\t\t( SELECT count(reply_id) FROM boom_post_reply ) as reply_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE verified = 1 ) as verified_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_banned > 0) as banned_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_mute > " . time() . " ) as muted_users,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_kick > " . time() . " ) as kicked_users\r\n\t\t\t\t\t\t");
    } else {
        $request = $mysqli->query("SELECT\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users ) as user_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE last_action >= " . $delay . ") as online_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_sex = 2 ) as female_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_sex = 1 ) as male_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE verified = 1 ) as verified_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_banned > 0) as banned_count,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_mute > " . time() . " ) as muted_users,\r\n\t\t\t\t\t\t( SELECT count(user_id) FROM boom_users WHERE user_kick > " . time() . " ) as kicked_users\r\n\t\t\t\t\t\t");
    }
    $dashboard = $request->fetch_assoc();
    return $dashboard;
}

?>