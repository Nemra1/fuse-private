<?php

require __DIR__ . "../../../config_session.php";

if (!boomAllow(70)) {
    exit;
}

$result = getDashboard();
echo elementTitle($lang["dashboard"]);
?>

<div class="page_full">
    <div class="page_element">
        <?php echo spBox("ri-group-3-line", $lang["registered"], $result["user_count"], "sp_member"); ?>
        <?php echo spBox("ri-wechat-pay-line", $lang["online"], $result["online_count"], "sp_online"); ?>
        <?php echo spBox("ri-women-line", $lang["female"], $result["female_count"], "sp_female"); ?>
        <?php echo spBox("ri-men-line", $lang["male"], $result["male_count"], "sp_male"); ?>
        <?php echo spBox("ri-shield-check-fill", $lang["verified"], $result["verified_count"], "sp_verified"); ?>
        <?php echo spBox("ri-blur-off-line", $lang["muted"], $result["muted_users"], "sp_muted"); ?>
        <?php echo spBox("ri-flashlight-line", $lang["kicked"], $result["kicked_users"], "sp_kicked"); ?>
        <?php echo spBox("ri-forbid-line", $lang["banned"], $result["banned_count"], "sp_banned"); ?>
        <?php if (boomAllow(90)): ?>
            <?php echo spBox("ri-wechat-line", $lang["chat_logs"], $result["chat_count"], "sp_chat"); ?>
            <?php echo spBox("ri-chat-history-line", $lang["private_logs"], $result["private_count"], "sp_private"); ?>
            <?php echo spBox("ri-rss-fill", $lang["post_count"], $result["post_count"], "sp_post"); ?>
            <?php echo spBox("ri-chat-quote-line", $lang["post_reply"], $result["reply_count"], "sp_reply"); ?>
        <?php endif; ?>
        <div class="clear"></div>
    </div>
</div>

<?php if (boomAllow(90)): ?>
    <div class="page_full">
        <div class="page_element">
            <div class="listing_reg_content">
                <?php echo htmlspecialchars($lang["current_version"], ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($data["version"], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
function spBox($icon, $txt, $val, $cl)
{
    return "<div class=\"sp_box\">
                <div class=\"sp_content\">
                    <div class=\"sp_icon bcell_mid $cl\">
                        <i class=\"$icon\"></i>
                    </div>
                    <div class=\"sp_info bcell_mid\">
                        <p class=\"sp_title\">" . htmlspecialchars($txt, ENT_QUOTES, 'UTF-8') . "</p>
                        <p class=\"sp_count\">" . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . "</p>
                    </div>
                </div>
            </div>";
}

function getDashboard()
{
    global $mysqli;
    $delay = getDelay();
    $currentTime = time();

    $baseQuery = "SELECT
        (SELECT COUNT(user_id) FROM boom_users) AS user_count,
        (SELECT COUNT(user_id) FROM boom_users WHERE last_action >= $delay) AS online_count,
        (SELECT COUNT(user_id) FROM boom_users WHERE user_sex = 2) AS female_count,
        (SELECT COUNT(user_id) FROM boom_users WHERE user_sex = 1) AS male_count,
        (SELECT COUNT(user_id) FROM boom_users WHERE verified = 1) AS verified_count,
        (SELECT COUNT(user_id) FROM boom_users WHERE user_banned > 0) AS banned_count,
        (SELECT COUNT(user_id) FROM boom_users WHERE user_mute > $currentTime) AS muted_users,
        (SELECT COUNT(user_id) FROM boom_users WHERE user_kick > $currentTime) AS kicked_users";

    if (boomAllow(90)) {
        $baseQuery .= ",
        (SELECT COUNT(post_id) FROM boom_chat) AS chat_count,
        (SELECT COUNT(post_id) FROM boom_post) AS post_count,
        (SELECT COUNT(reply_id) FROM boom_post_reply) AS reply_count,
        (SELECT COUNT(user_id) FROM boom_users WHERE user_mute > $currentTime) AS private_count";
    }

    $request = $mysqli->query($baseQuery);
    return $request->fetch_assoc();
}
?>
