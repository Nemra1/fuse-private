<?php
/**
 * Codychat
 *
 * @package Codychat
 * @author www.boomcoding.com
 * @copyright 2020
 * @terms any use of this script without a legal license is prohibited
 * all the content of Codychat is the property of BoomCoding and cannot be 
 * used for another project.
 */
require_once('../config_session.php');
// Start output buffering
ob_start();
$check_action = getDelay();
$online_delay = time() - (86400 * 7);
$online_count = $onair_count = $owner_count = $supers_count = $admins_count = $moderators_count = $premium_count = $vip_count = $free_users_count = 0;
$online_user = $offline_user = $onair_user = $owner_users = $supers_users = $admins_users = $moderators_users = $vip_users = $premium_users = $free_users = '';

if ($data['last_action'] < getDelay()) {
    $mysqli->query("UPDATE boom_users SET last_action = '" . time() . "' WHERE user_id = '{$data['user_id']}'");
}
$user_query = 'user_name, user_mobile, user_color, user_font, user_rank, user_dj, user_onair, user_join, user_tumb, user_status, user_sex, user_age, user_cover, country,
           user_id, user_mute, user_regmute, room_mute, last_action, user_bot, user_role, user_mood, user_verify ,user_ghost,user_mmute,user_pmute,user_level,photo_frame,name_wing1,name_wing2,is_live';
$data_list_query = "
    SELECT $user_query
    FROM boom_users
    WHERE user_roomid = {$data["user_roomid"]}
      AND last_action > '$check_action'
      AND (user_status != 99 OR user_bot = 1)
    ORDER BY user_rank DESC, user_role DESC, user_name ASC
";

$data_list = $mysqli->query($data_list_query);

if ($data['max_offcount'] > 0) {
    $offline_list_query = "
        SELECT $user_query
        FROM boom_users
        WHERE user_roomid = {$data["user_roomid"]}
          AND last_action > '$online_delay'
          AND last_action < '$check_action'
          AND user_status != 99
          AND user_rank != 0
          AND user_bot = 0
        ORDER BY last_action DESC
        LIMIT {$data['max_offcount']}
    ";

    $offline_list = $mysqli->query($offline_list_query);
}

mysqli_close($mysqli);

function categorizeUsers($list) {
    global $data, $onair_user, $onair_count, $owner_users, $owner_count, $supers_users, $supers_count, 
           $admins_users, $admins_count, $moderators_users, $moderators_count, $vip_users, $premium_users,
           $vip_count,$premium_count, $free_users, $free_users_count, $online_count, $lazy_state, $lazy_min, $online_user;

    $room_id = $data["user_roomid"];       
    $userid = $list['user_id'];
    $check_staff = check_roomStaff($room_id, $userid);

    if ($list['user_dj'] == 1 && $list['user_onair'] == 1) {
        // DJ User On Air
        appendUserToList($onair_user, $onair_count, $list);
    } elseif ($list['user_rank'] == 100) {
        // Owner User
        appendUserToList($owner_users, $owner_count, $list);
    } elseif ($list['user_rank'] == 90 || $check_staff == 6) {
        // Super Users
        appendUserToList($supers_users, $supers_count, $list);
    } elseif ($list['user_rank'] == 80 || $check_staff == 5) {
        // Admins
        appendUserToList($admins_users, $admins_count, $list);
    } elseif ($list['user_rank'] == 70 || $check_staff == 4) {
        // Moderators
        appendUserToList($moderators_users, $moderators_count, $list);
    } elseif ($list['user_rank'] == 60 || $list['user_rank'] == 61 || $list['user_rank'] == 62) {
        // Premium Users
        appendUserToList($premium_users, $premium_count, $list);
    } elseif ($list['user_rank'] == 50 || $list['user_rank'] == 51 || $list['user_rank'] == 52 ) {
        // VIP
        appendUserToList($vip_users, $vip_count, $list);
    }elseif ($list['user_rank'] == 1 || $list['user_rank'] == 0 || $list['user_rank'] == 69) {
        // Free Users
        appendUserToList($free_users, $free_users_count, $list);
    } else {
        // Other Users
        if ($lazy_state < $lazy_min) {
            $online_user .= createUserlist($list);
        } else {
            $online_user .= createUserlist($list, true); // `true` may indicate the user is lazy
        }
        $online_count++;
        $lazy_state++;
    }
}
function appendUserToList(&$user_list, &$user_count, $list) {
    global $lazy_state, $lazy_min;

    if ($lazy_state < $lazy_min) {
        $user_list .= createUserlist($list);
    } else {
        $user_list .= createUserlist($list, true); // Handle lazy users
    }
    $user_count++;
    $lazy_state++;
}
if ($data_list->num_rows > 0) {
    while ($list = $data_list->fetch_assoc()) {
        categorizeUsers($list);
    }
}

if ($data['max_offcount'] > 0 && $offline_list->num_rows > 0) {
    while ($offlist = $offline_list->fetch_assoc()) {
        $offline_user .= createUserlist($offlist);
    }
}
?>
<style>
hr { border: 0; height: 20px; background-repeat: no-repeat; background-size: contain; width: 100%; background-position: center; }
hr.supers_hr { background-image: url(default_images/hr/supers_hr.png); }
hr.owner_hr { background-image: url(default_images/hr/owner_hr.png); }
hr.admins_hr { background-image: url(default_images/hr/admins_hr.png); }
.list_ghost { height: 15px; width: auto; }
.user_head{display: inline-block;color: #ff720e;padding-left: 5px;}
.rank_sign { display: flex; justify-content: flex-end; }
</style>
<div id="container_user">
    <?php if ($onair_user) { ?>
        <div class="user_count">
            <div class="bcell">
                <span class="ucount theme_btn"><?php echo $onair_count; ?></span><?php echo $lang['onair']; ?>
            </div>
            <div class="rank_sign"><img class="list_rank" src="default_images/rank/dj.gif"></div>
        </div>
        <div class="online_user"><?php echo $onair_user; ?></div>
    <?php } ?>
   
    <?php if ($owner_users) { ?>
    
        <div class="user_count">
            <div class="bcell">
                <span class="ucount theme_btn"><?php echo $owner_count; ?></span><div class="user_head"><?php echo $lang['owner']; ?></div>
            </div>
            <div class="rank_sign"><img class="list_rank" src="default_images/rank/owner.gif"></div>
        </div>
        
        <div class="online_user"><?php echo $owner_users; ?></div>
         <hr class="owner_hr" title="Owners">
    <?php } ?>

    <?php if ($supers_users) { ?>
        <div class="user_count">
            <div class="bcell">
                <span class="ucount theme_btn"><?php echo $supers_count; ?></span><div class="user_head"><?php echo $lang['super_admin']; ?> </div>
            </div>
            <div class="rank_sign"><img class="list_rank" src="default_images/rank/super.gif"></div>
        </div>
        <div class="online_user"><?php echo $supers_users; ?></div>
        <hr class="supers_hr"  title="<?php echo $lang['super_admin']; ?>">
    <?php } ?>

    <?php if ($admins_users) { ?>
        <div class="user_count">
            <div class="bcell">
                <span class="ucount theme_btn"><?php echo $admins_count; ?></span><div class="user_head"><?php echo $lang['admin']; ?></div>
            </div>
             <div class="rank_sign"><img class="list_rank" src="default_images/rank/admin.gif"></div>
        </div>
        <div class="online_user"><?php echo $admins_users; ?></div>
        <hr class="admins_hr"  title="<?php echo $lang['admin']; ?>">
    <?php } ?>

    <?php if ($moderators_users) { ?>
        <div class="user_count">
            <div class="bcell">
                <span class="ucount theme_btn"><?php echo $moderators_count; ?></span><div class="user_head"><?php echo $lang['mod']; ?></div>
            </div>
            <div class="rank_sign"><img class="list_rank" src="default_images/rank/mod.gif"></div>
        </div>
        <div class="online_user"><?php echo $moderators_users; ?></div>
        
    <?php } ?>

    <?php if ($premium_users) { ?>
        <div class="user_count">
            <div class="bcell">
                <span class="ucount theme_btn"><?php echo $premium_count; ?></span><div class="user_head"><?php echo $lang['premium']; ?></div>
            </div>
            <div class="rank_sign"><img class="list_rank" src="default_images/rank/premium_elite.gif"></div>
        </div>
        <div class="online_user"><?php echo $premium_users; ?></div>
    <?php } ?>
    <?php if ($vip_users) { ?>
        <div class="user_count">
            <div class="bcell">
                <span class="ucount theme_btn"><?php echo $vip_count; ?></span><div class="user_head"><?php echo $lang['vip']; ?></div>
            </div>
            <div class="rank_sign"><img class="list_rank" src="default_images/rank/vip_elite.gif"></div>
        </div>
        <div class="online_user"><?php echo $vip_users; ?></div>
    <?php } ?>
    <?php if ($free_users) { ?>
        <div class="user_count">
            <div class="bcell">
                <span class="ucount theme_btn"><?php echo $free_users_count; ?></span><div class="user_head"><?php echo $lang['user']; ?></div>
            </div>
        </div>
        <div class="online_user"><?php echo $free_users; ?></div>
    <?php } ?>

    <?php if ($offline_user) { ?>
        <div class="user_count">
            <div class="bcell">
                <?php echo $lang['offline']; ?>
            </div>
        </div>
        <div class="online_user"><?php echo $offline_user; ?></div>
    <?php } ?>

    <div class="clear"></div>
</div>
<?php
// End buffering and output the content
ob_end_flush();
?>
