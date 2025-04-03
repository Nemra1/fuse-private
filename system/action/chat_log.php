<?php
/**
* FuseChat - Secure Version
* @package FuseChat
* @author www.nemra-1.com
* @copyright 2020
* @terms any use of this script without a legal license is prohibited
*/

require_once('../config_chat.php');

// Setting default values
$chat_history = 20;
$chat_substory = 20;
$private_history = 18;
$status_delay = $data['last_action'] + 21;
$out_delay = time() - 1800;
// Update user last active time
$last_active = updateLastActive($data['user_id']);
if (isset($_POST['last'], $_POST['snum'], $_POST['caction'], $_POST['fload'], $_POST['preload'], $_POST['priv'], $_POST['lastp'], $_POST['pcount'], $_POST['room'], $_POST['notify'])) {
    // Escape and validate POST data
    $last = isset($_POST['last']) ? (int) $_POST['last'] : 0;
    $fload = isset($_POST['fload']) ? (int) $_POST['fload'] : 0;
    $snum = isset($_POST['snum']) ? (int) $_POST['snum'] : 0;
    $caction = isset($_POST['caction']) ? htmlspecialchars($_POST['caction'], ENT_QUOTES, 'UTF-8') : '';
    $preload = isset($_POST['preload']) ? (int) $_POST['preload'] : 0;
    $priv = isset($_POST['priv']) ? (int) $_POST['priv'] : 0;
    $lastp = isset($_POST['lastp']) ? (int) $_POST['lastp'] : 0;
    $pcount = isset($_POST['pcount']) ? (int) $_POST['pcount'] : 0;
    $room = isset($_POST['room']) ? (int) $_POST['room'] : 0;
    $notify = isset($_POST['notify']) ? (int) $_POST['notify'] : 0;
    // Additional validation for variables like 'room' or 'priv' can be done here if needed
    if ($room != $data['user_roomid']) {
        echo json_encode(["check" => 199]);
        die();
    }
    // Start preparing the output data
    $d['mlogs'] = '';
    $d['plogs'] = '';
    $d['mlast'] = $last;
    $d['plast'] = $lastp;
    $d['rewards'] = updateUserGold();
    $d['rooms_updates'] = get_rooms_notifications();
    $gnotif = gift_notification();
    $main = 1;
    $private = 1;
    $ssnum = 0;
	// Update last action if needed
	if(time() > $status_delay || $fload == 0) {
		$ip = getIp(); // Store the IP in a variable
		if ($fload == 0 && $data['join_msg'] == 0 || $data['last_action'] < $out_delay) {
			joinRoom();
		}
		// Store the current time in a variable
		$current_time = time();
		// Update last action and user IP using prepared statements
		$stmt = $mysqli->prepare("UPDATE boom_users SET join_msg = 1, last_action = ?, user_ip = ? WHERE user_id = ?");
		$stmt->bind_param("isi", $current_time, $ip, $data['user_id']); // Pass the variables, not the expressions
		$stmt->execute();
	}
    // Use gold if enabled
    if (useGold()) {
        $d['gold'] = (int)$data['user_gold'];
    }
    // Notification check
    if ($notify < $data['naction']) {
        $stmt = $mysqli->prepare("
            SELECT 
                (SELECT COUNT(*) FROM boom_friends WHERE target = ? AND fstatus = 2 AND viewed = 0) AS friend_count,
                (SELECT COUNT(*) FROM boom_notification WHERE notified = ? AND notify_view = 0) AS notify_count,
                (SELECT COUNT(*) FROM boom_report) AS report_count,
                (SELECT COUNT(*) FROM boom_news WHERE news_date > ?) AS news_count
        ");
        $stmt->bind_param("iis", $data['user_id'], $data['user_id'], $data['user_news']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $fetch = $result->fetch_assoc();
            $d['use'] = 1;
            $d['friends'] = $fetch['friend_count'];
            $d['notify'] = $fetch['notify_count'];
            $d['news'] = $fetch['news_count'];
            $d['nnotif'] = $data['naction'];
            if (boomAllow(70)) {
                $d['report'] = $fetch['report_count'];
            }
        }
    }
    // Room info
    $d['r_info'] = array(
        "room_name" => $data['room_name'],
        "room_icon" => myRoomIcon($data['room_icon']),
        "max_user" => $data['max_user'],
    );
    // Chat logs query with prepared statement
    if ($fload == 0) {
        $add = (!isGhosted($data) && !canViewGhost()) ? 'AND pghost = 0' : '';
        $stmt = $mysqli->prepare("
            SELECT log.*, u.user_name, u.user_color, u.user_font, u.user_rank, u.bccolor, u.user_sex, u.user_age, 
                u.user_tumb, u.user_cover, u.country, u.user_bot, u.user_ghost, u.user_pmute, u.user_mmute, u.room_mute,
                u.warn_msg, u.photo_frame, u.user_level, u.user_exp, u.user_badge, u.name_wing1, u.name_wing2
            FROM boom_chat AS log
            LEFT JOIN boom_users AS u ON log.user_id = u.user_id
            WHERE post_roomid = ? AND post_id > ? $add
            ORDER BY post_id DESC LIMIT ?
        ");
        $stmt->bind_param("iii", $data['user_roomid'], $last, $chat_history);
        $stmt->execute();
        $log = $stmt->get_result();
        $ssnum = 1;
    } else {
        if ($caction != $data['rcaction']) {
            $add = (!isGhosted($data) && !canViewGhost()) ? 'AND pghost = 0' : '';
            $stmt = $mysqli->prepare("
                SELECT log.*, u.user_name, u.user_color, u.user_font, u.user_rank, u.bccolor, u.user_sex, u.user_age, 
                    u.user_tumb, u.user_cover, u.country, u.user_bot, u.user_ghost, u.user_pmute, u.user_mmute, u.room_mute,
                    u.warn_msg, u.photo_frame, u.user_level, u.user_exp, u.user_badge, u.name_wing1, u.name_wing2
                FROM boom_chat AS log
                LEFT JOIN boom_users AS u ON log.user_id = u.user_id
                WHERE post_roomid = ? AND post_id > ? $add
                ORDER BY post_id DESC LIMIT ?
            ");
            $stmt->bind_param("iii", $data['user_roomid'], $last, $chat_substory);
            $stmt->execute();
            $log = $stmt->get_result();
        } else {
            $main = 0;
        }
    }

    // Processing the chat logs
    if ($main == 1 && $log->num_rows > 0) {
        while ($chat = $log->fetch_assoc()) {
            $d['mlast'] = $chat['post_id'];
            if ($chat['snum'] != $snum || $ssnum == 1) {
                $d['mlogs'] .= createLog($data, $chat, $ignore);
            }
        }
    }

    // Private logs with prepared statement
    if ($preload == 1) {
        $stmt = $mysqli->prepare("
            SELECT log.*, u.user_id, u.user_name, u.user_color, u.user_tumb, u.user_bot, u.user_ghost, 
                u.user_pmute, u.user_mmute, u.room_mute
            FROM boom_private AS log
            LEFT JOIN boom_users AS u ON log.hunter = u.user_id
            WHERE (hunter = ? AND target = ?) OR (hunter = ? AND target = ?)
            ORDER BY id DESC LIMIT ?
        ");
        $stmt->bind_param("iiii", $data['user_id'], $priv, $priv, $data['user_id'], $private_history);
        $stmt->execute();
        $privlog = $stmt->get_result();
    } else {
        if ($pcount != $data['pcount'] && $priv != 0) {
            $stmt = $mysqli->prepare("
                SELECT log.*, u.user_id, u.user_name, u.user_color, u.user_tumb, u.user_bot, u.user_ghost, 
                    u.user_pmute, u.user_mmute, u.room_mute
                FROM boom_private AS log
                LEFT JOIN boom_users AS u ON log.hunter = u.user_id
                WHERE (hunter = ? AND target = ? AND id > ?) OR (hunter = ? AND target = ? AND id > ? AND file = 1)
                ORDER BY id DESC LIMIT ?
            ");
            $stmt->bind_param("iiiiiiii", $priv, $data['user_id'], $lastp, $data['user_id'], $priv, $lastp, $private_history);
            $stmt->execute();
            $privlog = $stmt->get_result();
        } else {
            $private = 0;
        }
    }

    // Process private logs
    if ($private == 1 && $privlog->num_rows > 0) {
        $mysqli->query("UPDATE boom_private SET status = 1 WHERE hunter = ? AND target = ?");
        while ($private = $privlog->fetch_assoc()) {
            $d['plogs'] .= privateLog($private, $data['user_id']);
            $d['plast'] = $private['id'];
        }
    }

    // Additional data processing and final response
    $d['pcount'] = $data['pcount'];
    $d['cact'] = $data['rcaction'];
    $d['act'] = $data['user_action'];
    $d['ses'] = $data['session_id'];
    $d['curp'] = $priv;
    $d['spd'] = (int)$data['speed'];
    $d['acd'] = $data['act_delay'];
    $d['pico'] = $data['private_count'];

    // Close the database connection
    mysqli_close($mysqli);

    // Return JSON response
    echo json_encode($d, JSON_UNESCAPED_UNICODE);
}
?>
