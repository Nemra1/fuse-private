<?php

require __DIR__ . "./../config_session.php";

if (isset($_POST["del_post"]) && isset($_POST["type"])) {
    echo chatdeletepost();
    exit;
}
if (isset($_POST["private_delete"])) {
    echo privatedeletion();
    exit;
}
if (isset($_POST["clear_private"])) {
    echo privateclearing();
    exit;
}
if (isset($_POST["del_private"]) && isset($_POST["target"])) {
    echo deleteprivatehistory();
    exit;
}
function deletePrivateHistory()
{
    global $mysqli;
    global $data;
    global $cody;
    if (!canDeletePrivate()) {
        return 0;
    }
    $target = escape($_POST["target"]);
    $stmt = $mysqli->prepare("SELECT * FROM boom_report WHERE (report_user = ? AND report_target = ?) OR (report_user = ? AND report_target = ?)");
    $stmt->bind_param("ssss", $data["user_id"], $target, $target, $data["user_id"]);
    $stmt->execute();
    $check_private = $stmt->get_result();
    if (0 < $check_private->num_rows) {
        $priv = $check_private->fetch_assoc();
        if (!selfManageReport($priv["report_target"])) {
            return 0;
        }
    }
    $mysqli->query("DELETE FROM boom_report WHERE report_user = '" . $data["user_id"] . "' AND report_target = '" . $target . "' OR report_user = '" . $target . "' AND report_target = '" . $data["user_id"] . "'");
    updateStaffNotify();
    clearPrivate($data["user_id"], $target);
    return 1;
}

function chatDeletePost()
{
    global $mysqli;
    global $data;
    $post = escape($_POST["del_post"]);
    $type = escape($_POST["type"]);
    $log = logInfo($post);
    if (empty($log)) {
        return "";
    }
    if (!canDeleteLog() && !canDeleteRoomLog() && !canDeleteSelfLog($log)) {
        return "";
    }

    $room = roomInfo($data["user_roomid"]);
    if (empty($room)) {
        return "";
    }
    $mysqli->query("DELETE FROM boom_chat WHERE post_id = '" . $post . "' AND post_roomid = '" . $data["user_roomid"] . "'");
    $mysqli->query("DELETE FROM boom_report WHERE report_post = '" . $post . "' AND report_type = '1' AND report_room = '" . $data["user_roomid"] . "'");
    if (0 < $mysqli->affected_rows) {
        updateStaffNotify();
    }
    if (!delExpired($room["rltime"])) {
        $mysqli->query("UPDATE boom_rooms SET rldelete = CONCAT(rldelete, '," . $post . "'), rltime = '" . time() . "' WHERE room_id = '" . $data["user_roomid"] . "'");
    } else {
        $mysqli->query("UPDATE boom_rooms SET rldelete = '" . $post . "', rltime = '" . time() . "' WHERE room_id = '" . $data["user_roomid"] . "'");
    }
    boomConsole("delete_log", ["target" => $log["user_id"], "room" => $data["user_roomid"], "reason" => strip_tags($log["post_message"])]);
    removeRelatedFile($post, "chat");
}

function privateDeletion()
{
    global $mysqli;
    global $data;
    $item = escape($_POST["private_delete"]);
    $mysqli->query("UPDATE `boom_private` SET `status` = 3, `view` = 1  WHERE `hunter` = '" . $item . "' AND `target` = '" . $data["user_id"] . "' AND `status` < 3");
    return 1;
}

function privateClearing()
{
    global $mysqli;
    global $data;
    $mysqli->query("UPDATE `boom_private` SET `status` = 3, `view` = 1  WHERE `target` = '" . $data["user_id"] . "' AND `status` < 3");
    return 1;
}

?>
