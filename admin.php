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
require_once("system/config.php");

// Ensure $cody['can_view_console'] and $cody['can_manage_addons'] are properly set and validated
$cody_can_view_console = isset($cody['can_view_console']) ? (int)$cody['can_view_console'] : 0;
$cody_can_manage_addons = isset($cody['can_manage_addons']) ? (int)$cody['can_manage_addons'] : 0;

$page_info = array(
    'page' => 'admin',
    'page_load' => 'system/pages/admin/setting_dashboard.php',
    'page_menu' => 1,
    'page_rank' => 70,
    'page_nohome' => 1,
);

// loading head tag element
include('control/head_load.php');

// load page header
include('control/header.php');

// Create page menu
$side_menu = '';
$side_menu .= pageMenu('admin/setting_dashboard.php', 'ri-dashboard-2-fill', htmlspecialchars($lang['dashboard'], ENT_QUOTES, 'UTF-8'), 70);

// menu drop 1
$drop1 = pageDropItem('admin/setting_main.php', htmlspecialchars($lang['main_settings'], ENT_QUOTES, 'UTF-8'), 100);
$drop1 .= pageDropItem('admin/setting_registration.php', htmlspecialchars($lang['registration_settings'], ENT_QUOTES, 'UTF-8'), 90);
$drop1 .= pageDropItem('admin/setting_display.php', htmlspecialchars($lang['display_settings'], ENT_QUOTES, 'UTF-8'), 100);
$drop1 .= pageDropItem('admin/setting_security.php', htmlspecialchars($lang['security'], ENT_QUOTES, 'UTF-8'), 100);
$drop1 .= pageDropItem('admin/setting_email.php', htmlspecialchars($lang['email_settings'], ENT_QUOTES, 'UTF-8'), 100);
$drop1 .= pageDropItem('admin/setting_data.php', htmlspecialchars($lang['database_management'], ENT_QUOTES, 'UTF-8'), 90);
$drop1 .= pageDropItem('admin/setting_delays.php', htmlspecialchars($lang['delay_settings'], ENT_QUOTES, 'UTF-8'), 90);
$drop1 .= pageDropItem('admin/setting_notifications.php', htmlspecialchars($lang['notification'], ENT_QUOTES, 'UTF-8'), 100);
$side_menu .= pageDropMenu('ri-apps-2-line', htmlspecialchars($lang['system_config'], ENT_QUOTES, 'UTF-8'), $drop1, 90);
$dropx ='';
$dropx .= pageDropItem('admin/setting_members.php', htmlspecialchars($lang['users_management'], ENT_QUOTES, 'UTF-8'), 100);
$dropx .= pageDropItem('admin/setting_online.php', htmlspecialchars($lang['online'], ENT_QUOTES, 'UTF-8'), 100);

$side_menu .= pageDropMenu('ri-apps-2-line', htmlspecialchars($lang['users_management'], ENT_QUOTES, 'UTF-8'), $dropx, 90);


$side_menu .= pageMenu('admin/setting_websocket.php', 'ri-shield-flash-fill', htmlspecialchars('WebSocket', ENT_QUOTES, 'UTF-8'), 90);

$side_menu .= pageMenu('admin/setting_action.php', 'ri-auction-line', htmlspecialchars($lang['manage_action'], ENT_QUOTES, 'UTF-8'), 70);
$side_menu .= pageMenu('admin/setting_chat.php', 'ri-message-3-line', htmlspecialchars($lang['chat_settings'], ENT_QUOTES, 'UTF-8'), 90);
$side_menu .= pageMenu('admin/setting_rooms.php', 'ri-kakao-talk-line', htmlspecialchars($lang['room_management'], ENT_QUOTES, 'UTF-8'), 90);

// menu drop 2
$drop2 = pageDropItem('admin/setting_filter.php', htmlspecialchars($lang['filter'], ENT_QUOTES, 'UTF-8'), 80);
$drop2 .= pageDropItem('admin/setting_ip.php', htmlspecialchars($lang['ban_management'], ENT_QUOTES, 'UTF-8'), 80);
$drop2 .= pageDropItem('admin/setting_console.php', htmlspecialchars($lang['system_logs'], ENT_QUOTES, 'UTF-8'), $cody_can_view_console);
$drop2 .= pageDropItem('admin/setting_info.php', htmlspecialchars($lang['system_diagnostic'], ENT_QUOTES, 'UTF-8'), 100);
$side_menu .= pageDropMenu('ri-tools-line', htmlspecialchars($lang['system_tools'], ENT_QUOTES, 'UTF-8'), $drop2, min(80, $cody_can_view_console));

// menu drop 3
$drop3 = pageDropItem('admin/setting_limit.php', htmlspecialchars($lang['member_permission'], ENT_QUOTES, 'UTF-8'), 100);
$drop3 .= pageDropItem('admin/setting_staff.php', htmlspecialchars($lang['staff_permission'], ENT_QUOTES, 'UTF-8'), 100);
$side_menu .= pageDropMenu('ri-shield-star-fill', htmlspecialchars($lang['permission'], ENT_QUOTES, 'UTF-8'), $drop3, 100);
// menu drop 4
$drop4  = pageDropItem('admin/setting_gold.php', 'Gold Settings', 100);
$drop4 .= pageDropItem('admin/setting_gift.php', 'Gift Settings', 100);
$drop4 .= pageDropItem('admin/setting_wallet.php', 'Wallet', 100);
$drop4 .= pageDropItem('admin/setting_level.php', 'Level XP', 100);
$drop4 .= pageDropItem('admin/setting_rewards.php', 'Gold Rewards', 100);
$side_menu .= pageDropMenu('ri-copper-coin-line', 'Gold System', $drop4, min(100, 100));

$side_menu .= pageMenu('admin/setting_dj.php', 'ri-headphone-fill', $lang['manage_dj'], $data['can_dj']);
$side_menu .= pageMenu('admin/setting_bot.php', 'ri-robot-2-line', 'Bot Speakers', 100);
$side_menu .= pageMenu('admin/setting_player.php', 'ri-disc-line', htmlspecialchars($lang['player_settings'], ENT_QUOTES, 'UTF-8'), 90);
$side_menu .= pageMenu('admin/setting_modules.php', 'ri-box-3-fill', htmlspecialchars($lang['manage_modules'], ENT_QUOTES, 'UTF-8'), 90);
$side_menu .= pageMenu('admin/setting_store.php', 'ri-app-store-line', htmlspecialchars('Store', ENT_QUOTES, 'UTF-8'), 90);
$side_menu .= pageMenu('admin/setting_addons.php', 'ri-puzzle-line', htmlspecialchars($lang['addons_management'], ENT_QUOTES, 'UTF-8'), $cody_can_manage_addons);
$side_menu .= pageMenu('admin/setting_pages.php', 'ri-page-separator', htmlspecialchars($lang['page'], ENT_QUOTES, 'UTF-8'), 100);
$side_menu .= pageMenu('admin/setting_update.php', 'ri-download-cloud-2-fill', htmlspecialchars($lang['update_zone'], ENT_QUOTES, 'UTF-8'), 100);

$side_menu .= pageMenuFunction("openLinkPage('documentation.php');", 'ri-questionnaire-fill', htmlspecialchars($lang['manual'], ENT_QUOTES, 'UTF-8'),80);

// load page content
echo boomTemplate('element/base_page_menu', $side_menu);
?>
<!-- load page script -->
<script data-cfasync="false" src="js/function_admin.js<?php echo htmlspecialchars($bbfv, ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php
// close page body
include('control/body_end.php');
?>
