<?php


require __DIR__ . "./../config_session.php";
if (isset($_POST["unset_report"]) && isset($_POST["type"])) {
    echo unsetreport();
    exit;
}
if (isset($_POST["remove_report"]) && isset($_POST["type"]) && isset($_POST["report"])) {
    $type = escape($_POST["type"]);
    if ($type == 1) {
        echo removechatreport();
        exit;
    }
    if ($type == 2) {
        echo removewallreport();
        exit;
    }
    if ($type == 3) {
        echo removeprivatereport();
        exit;
    }
    exit;
}
if (isset($_POST["send_report"]) && isset($_POST["type"]) && isset($_POST["report"]) && isset($_POST["reason"])) {
    $type = escape($_POST["type"]);
    if ($type == 1) {
        echo makechatreport();
        exit;
    }
    if ($type == 2) {
        echo makewallreport();
        exit;
    }
    if ($type == 3) {
        echo makeprivatereport();
        exit;
    }
    if ($type == 4) {
        echo makeprofilereport();
        exit;
    }
    exit;
}

function unsetReport()
{
    global $mysqli;
    global $data;
    global $cody;
    $report = escape($_POST["unset_report"]);
    $type = escape($_POST["type"]);
    if (!canManageReport()) {
        return 0;
    }

    $get_report = $mysqli->query("SELECT * FROM boom_report WHERE report_id = '" . $report . "' LIMIT 1");
    if (0 < $get_report->num_rows) {
        $rep = $get_report->fetch_assoc();
        if (!selfManageReport($rep["report_target"])) {
            return 0;
        }
    }
    $mysqli->query("DELETE FROM boom_report WHERE report_id = '" . $report . "' AND report_type = '" . $type . "'");
    updateStaffNotify();
    return 1;
}

function removeChatReport()
{
    global $mysqli;
    global $data;
    $report = escape($_POST["report"]);
    if (!canManageReport()) {
        return 0;
    }

    $get_report = $mysqli->query("SELECT boom_report.*, boom_rooms.* FROM boom_report, boom_rooms WHERE report_id = '" . $report . "' AND report_type = 1 AND boom_rooms.room_id = boom_report.report_room LIMIT 1");
    if (0 < $get_report->num_rows) {
        $rep = $get_report->fetch_assoc();
        if (!selfManageReport($rep["report_target"])) {
            return 0;
        }
        $mysqli->query("DELETE FROM boom_chat WHERE post_id = '" . $rep["report_post"] . "'");
        if (!delExpired($rep["rltime"])) {
            $mysqli->query("UPDATE boom_rooms SET rldelete = CONCAT(rldelete, ', '" . $rep["report_post"] . "'), rltime = '" . time() . "' WHERE room_id = '" . $rep["report_room"] . "'");
        } else {
            $mysqli->query("UPDATE boom_rooms SET rldelete = '" . $rep["report_post"] . "', rltime = '" . time() . "' WHERE room_id = '" . $rep["report_room"] . "'");
        }
    }
    $mysqli->query("DELETE FROM boom_report WHERE report_id = '" . $report . "' AND report_type = 1");
    updateStaffNotify();
    return 1;
}

function removeWallReport()
{
    global $mysqli;
    global $data;
    $report = escape($_POST["report"]);
    if (!canManageReport()) {
        return 0;
    }
    $get_report = $mysqli->query("SELECT * FROM boom_report WHERE report_id = '" . $report . "' AND report_type = 2 LIMIT 1");
    if (0 < $get_report->num_rows) {
        $rep = $get_report->fetch_assoc();
        if (!selfManageReport($rep["report_target"])) {
            return 0;
        }
        $mysqli->query("DELETE FROM boom_post WHERE `post_id` = '" . $rep["report_post"] . "'");
        $mysqli->query("DELETE FROM `boom_post_reply` WHERE `parent_id` = '" . $rep["report_post"] . "'");
        $mysqli->query("DELETE FROM `boom_notification` WHERE `notify_id` = '" . $rep["report_post"] . "'");
        $mysqli->query("DELETE FROM `boom_post_like` WHERE `like_post` = '" . $rep["report_post"] . "'");
        $mysqli->query("DELETE FROM boom_report WHERE report_post = '" . $rep["report_post"] . "' AND report_type = 2");
    }
    $mysqli->query("DELETE FROM boom_report WHERE report_id = '" . $report . "' AND report_type = 2");
    updateStaffNotify();
    return 1;
}

function removePrivateReport()
{
    global $mysqli;
    global $data;
    global $cody;
    $report = escape($_POST["report"]);
    if (!canManageReport()) {
        return 0;
    }
    $get_report = $mysqli->query("SELECT * FROM boom_report WHERE report_id = '" . $report . "' AND report_type = 3 LIMIT 1");
    if (0 < $get_report->num_rows) {
        $rep = $get_report->fetch_assoc();
        if (!selfManageReport($rep["report_target"])) {
            return 0;
        }
        $mysqli->query("DELETE FROM boom_private WHERE hunter = '" . $rep["report_user"] . "' AND target = '" . $rep["report_target"] . "'");
        $mysqli->query("DELETE FROM boom_private WHERE hunter = '" . $rep["report_target"] . "' AND target = '" . $rep["report_user"] . "'");
    }
    $mysqli->query("DELETE FROM boom_report WHERE report_id = '" . $report . "' AND report_type = 3");
    updateStaffNotify();
    return 1;
}

function makeWallReport()
{
    global $mysqli;
    global $data;
    if (!canSendReport()) {
        return 3;
    }
    $post = escape($_POST["report"]);
    $reason = escape($_POST["reason"]);
    if (!validReport($reason)) {
        return 5;
    }
    if (!canPostAction($post)) {
        return 0;
    }
    $check_report = $mysqli->query("SELECT * FROM boom_report WHERE report_post = '" . $post . "' AND report_type = 2");
    if (0 < $check_report->num_rows) {
        return 1;
    }
    $valid_post = $mysqli->query("SELECT post_id, post_user FROM boom_post WHERE post_id = '" . $post . "'");
    if (0 < $valid_post->num_rows) {
        $wall = $valid_post->fetch_assoc();
        $mysqli->query("INSERT INTO boom_report (report_type, report_user, report_target, report_post, report_reason, report_date, report_room) VALUES (2, '" . $data["user_id"] . "', '" . $wall["post_user"] . "', '" . $post . "', '" . $reason . "', '" . time() . "', 0)");
        updateStaffNotify();
        return 1;
    }
    return 0;
}

function makeChatReport()
{
    global $mysqli;
    global $data;
    if (!canSendReport()) {
        return 3;
    }
    $post = escape($_POST["report"]);
    $reason = escape($_POST["reason"]);
    if (!validReport($reason)) {
        return 5;
    }
    $check_report = $mysqli->query("SELECT * FROM boom_report WHERE report_post = '" . $post . "' AND report_type = 1");
    if (0 < $check_report->num_rows) {
        return 1;
    }
    $log = logInfo($post);
    if (!empty($log)) {
        $mysqli->query("INSERT INTO boom_report (report_type, report_user, report_target, report_post, report_reason, report_date, report_room) VALUES (1, '" . $data["user_id"] . "', '" . $log["user_id"] . "', '" . $post . "', '" . $reason . "', '" . time() . "', '" . $data["user_roomid"] . "')");
        updateStaffNotify();
        return 1;
    }
    return 0;
}

function makeProfileReport()
{
    global $mysqli;
    global $data;
    if (!canSendReport()) {
        return 3;
    }
    $id = escape($_POST["report"]);
    $reason = escape($_POST["reason"]);
    if (mySelf($id)) {
        return 3;
    }
    if (!validReport($reason)) {
        return 5;
    }
    $check_report = $mysqli->query("SELECT * FROM boom_report WHERE report_target = '" . $id . "' AND report_type = 4");
    if (0 < $check_report->num_rows) {
        return 1;
    }
    $user = userDetails($id);
    if (empty($user)) {
        return 0;
    }
    if (isBot($user)) {
        return 3;
    }
    $mysqli->query("INSERT INTO boom_report (report_type, report_user, report_target, report_reason, report_date) VALUES (4, '" . $data["user_id"] . "', '" . $user["user_id"] . "', '" . $reason . "', '" . time() . "')");
    updateStaffNotify();
    return 1;
}

function makePrivateReport()
{
    global $mysqli;
    global $data;
    global $cody;
    $target = escape($_POST["report"]);
    $reason = escape($_POST["reason"]);
    if (!canSendReport()) {
        return 3;
    }
    $user = userDetails($target);
    if (empty($user)) {
        return 0;
    }
    if (!validReport($reason)) {
        return 5;
    }
    $check_private = $mysqli->query("SELECT hunter FROM boom_private WHERE hunter = '" . $data["user_id"] . "' AND target = '" . $target . "' || hunter = '" . $target . "' AND target = '" . $data["user_id"] . "' LIMIT 1");
    if ($check_private->num_rows < 1) {
        return 76;
    }
    $check_report = $mysqli->query("SELECT * FROM boom_report WHERE report_user = '" . $data["user_id"] . "' AND report_target = '" . $target . "'");
    if (0 < $check_report->num_rows) {
        return 1;
    }
    $mysqli->query("INSERT INTO boom_report (report_type, report_user, report_target, report_reason, report_date, report_room) VALUES ('3', '" . $data["user_id"] . "', '" . $target . "', '" . $reason . "', '" . time() . "', '0')");
    updateStaffNotify();
    return 1;
}

?>