<?php
/**
 * FuseChat
 *
 * @package FuseChat
 * @author www.nemra-1.com
 * @copyright 2020
 * @terms Unauthorized use of this script without a valid license is prohibited.
 * All content of FuseChat is the property of BoomCoding and cannot be used in another project.
 */

session_start();
require dirname(__DIR__) . "/vendor/autoload.php";
require "database.php";
require "variable.php";
require "function.php";
if (!checkToken() || !isset($_COOKIE[BOOM_PREFIX . 'userid']) || !isset($_COOKIE[BOOM_PREFIX . 'utk']) || !validateAuth()) {
    echo json_encode(["check" => 99]);
    exit();
}
$mysqli = new mysqli(BOOM_DHOST, BOOM_DUSER, BOOM_DPASS, BOOM_DNAME);
$mysqli->query("SET NAMES 'utf8mb4'");
if ($mysqli->connect_errno) {
    echo json_encode(["check" => 199]);
    exit();
}
$time = time();
$pass = escape($_COOKIE[BOOM_PREFIX . 'utk']);
$ident = escape($_COOKIE[BOOM_PREFIX . 'userid']);

// Define the query and parameters
$query = "
    SELECT 
        s.system_id, s.default_theme, s.site_description, s.domain, s.guest_talk, s.allow_logs, s.allow_private, s.use_wings, 
        s.allow_main, s.bbfv, s.can_raction, s.use_vpn, s.language, s.timezone, s.speed, s.gender_ico, s.act_delay, 
        s.bot_delay, s.allow_bot, s.can_ghost, s.can_vghost, s.use_gold,s.allow_gold,s.use_frame,s.use_level,s.gold_delay,s.gold_base,s.can_gold,
        u.user_id, u.user_name, u.user_join, u.join_msg, u.last_action, u.user_language, u.user_timezone, 
        u.user_status, u.user_color, u.user_rank, u.user_roomid, u.user_sound, u.session_id, u.pcount,
        u.user_news, u.user_mute, u.user_regmute, u.user_banned, u.user_kick, u.user_role, u.user_action, 
        u.room_mute, u.naction, u.user_ghost, u.user_pmute, u.user_mmute,u.user_gold,u.room_mute,u.warn_msg,u.user_level,u.user_exp,u.user_badge,u.last_gold,u.name_wing1,u.name_wing2,u.is_live,
        r.topic, r.room_id, r.rcaction, r.rldelete, r.rltime,r.room_name,r.room_icon,r.max_user,r.slug,
        (SELECT COUNT(DISTINCT hunter) FROM boom_private WHERE target = ? AND hunter != ? AND status = '0') as private_count
    FROM boom_users u
    JOIN boom_setting s ON s.id = 1
    JOIN boom_rooms r ON r.room_id = u.user_roomid
    WHERE u.user_id = ? AND u.user_password = ?
";

// Prepare the statement
$stmt = $mysqli->prepare($query);

// Bind the parameters
$stmt->bind_param("ssss", $ident, $ident, $ident, $pass);

// Execute the query
$stmt->execute();

// Get the result
$get_data = $stmt->get_result();
if (!$get_data) {
    // Log or display the error
    echo "SQL Error: " . $mysqli->error;
    echo json_encode(["check" => 99]);
    exit();
}

if ($get_data->num_rows > 0) {
    $data = $get_data->fetch_assoc();
    require "language/{$data['user_language']}/language.php";
    date_default_timezone_set($data['user_timezone']);
    $boom_access = 1;
    $ignore = getIgnore();
    session_write_close();
} else {
    echo json_encode(["check" => 99]);
    exit();
}

?>
