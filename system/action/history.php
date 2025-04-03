<?php
require __DIR__ . "./../config_session.php";
require BOOM_PATH . "/system/language/" . $data["user_language"] . "/history.php";

// Check if the get_history POST parameter is set and sanitize it
if (isset($_POST["get_history"]) && is_numeric($_POST["get_history"])) {
    echo userHistory();
    exit;
}

// Check if the remove_history POST parameter is set and sanitize it
if (isset($_POST["remove_history"]) && is_numeric($_POST["remove_history"]) && isset($_POST["target"]) && is_numeric($_POST["target"])) {
    echo removeHistory();
    exit;
}

function renderHistoryText($history) {
    global $mysqli, $data, $lang, $hlang, $cody;
    $ctext = $hlang[$history["htype"]];
    $ctext = str_replace("%hunter%", $history["user_name"], $ctext);
    $ctext = str_replace("%delay%", boomRenderMinutes($history["delay"]), $ctext);
    return $ctext;
}

function userHistory() {
    global $mysqli, $data, $lang, $hlang, $cody;
    // Sanitize the 'get_history' parameter and ensure it is a valid integer
    $id = (int)$_POST["get_history"];
    // Fetch user details (ensure they are valid)
    $user = userDetails($id);
    // Check if user has permission to view history
    if (!canUserHistory($user)) {
        return json_encode(['error' => 'You do not have permission to view history']);
    }
    // Use prepared statement for the database query to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT boom_history.*, boom_users.user_name, boom_users.user_tumb, boom_users.user_color FROM boom_history LEFT JOIN boom_users ON boom_history.hunter = boom_users.user_id WHERE boom_history.target = ? ORDER BY boom_history.history_date DESC LIMIT 200");
    $stmt->bind_param("i", $user["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $history_list = "";
    if ($result->num_rows > 0) {
        while ($history = $result->fetch_assoc()) {
            $history_list .= boomTemplate("element/history_log", $history);
        }
    } else {
        $history_list .= emptyZone($lang["no_data"]);
    }
    $stmt->close(); // Close statement
    return $history_list;
}

function removeHistory() {
    global $mysqli, $data, $lang, $hlang, $cody;
    // Sanitize inputs for 'remove_history' and 'target'
    $id = (int)$_POST["remove_history"];
    $target = (int)$_POST["target"];
    // Fetch user details (ensure they are valid)
    $user = userDetails($target);
    if (empty($user)) {
        return json_encode(['error' => 'Target user not found']);
    }
    // Check permissions: user must have the ability to manage history
    if (!boomAllow($cody["can_manage_history"])) {
        return json_encode(['error' => 'You do not have permission to manage history']);
    }
    // Use prepared statement to prevent SQL injection
    $stmt = $mysqli->prepare("DELETE FROM boom_history WHERE id = ? AND target = ?");
    $stmt->bind_param("ii", $id, $user["user_id"]);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        return json_encode(['success' => 'History record has been deleted successfully']);
    } else {
        $stmt->close();
        return json_encode(['error' => 'Failed to delete the history record']);
    }
}
?>
