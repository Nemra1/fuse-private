<?php


require __DIR__ . "./../config_session.php";
require BOOM_PATH . "/system/language/" . $data["user_language"] . "/history.php";
if (isset($_POST["get_history"])) {
    echo userhistory();
    exit;
}
if (isset($_POST["remove_history"])) {
    echo removehistory();
    exit;
}
function renderHistoryText($history)
{
    global $data;
    global $hlang;
    global $lang;
    $ctext = $hlang[$history["htype"]];
    $ctext = str_replace("%hunter%", $history["user_name"], $ctext);
    $ctext = str_replace("%delay%", boomRenderMinutes($history["delay"]), $ctext);
    return $ctext;
}

function userHistory()
{
    global $mysqli;
    global $data;
    global $lang;
    global $hlang;
    global $cody;
    $id = escape($_POST["get_history"]);
    $user = userDetails($id);
    if (!canUserHistory($user)) {
        return 0;
    }
    $find_history = $mysqli->query("SELECT boom_history.*, boom_users.user_name, boom_users.user_tumb, boom_users.user_color FROM boom_history LEFT JOIN boom_users ON boom_history.hunter = boom_users.user_id WHERE boom_history.target = '" . $user["user_id"] . "' ORDER BY boom_history.history_date DESC LIMIT 200");
    $history_list = "";
    if (0 < $find_history->num_rows) {
        while ($history = $find_history->fetch_assoc()) {
            $history_list .= boomTemplate("element/history_log", $history);
        }
    } else {
        $history_list .= emptyZone($lang["no_data"]);
    }
    return $history_list;
}

function removeHistory()
{
    global $mysqli;
    global $data;
    global $lang;
    global $hlang;
    global $cody;
    $id = escape($_POST["remove_history"]);
    $target = escape($_POST["target"]);
    $user = userDetails($target);
    if (empty($user)) {
        return 0;
    }
    if (!boomAllow($cody["can_manage_history"])) {
        return 0;
    }
    $mysqli->query("DELETE FROM boom_history WHERE id = " . $id . " AND target = '" . $user["user_id"] . "'");
    return 1;
}

?>