<?php
require('../config_session.php');

if (!canManageReport()) {
    echo json_encode(['status' => 'error', 'message' => 'Permission denied.']);
    exit();
}

if (isset($_POST['chat_report'])) {
    $id = escape($_POST['chat_report']);
    // Fetch report info using a prepared statement
    $stmt = $mysqli->prepare("SELECT * FROM boom_report WHERE report_id = ? AND report_type = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Report not found.']);
        exit();
    }
    $report = $result->fetch_assoc();
    // Get the post ID from the report
    $post_id = $report['report_post'];
    // Fetch chat details using a prepared statement
    $stmt_chat = $mysqli->prepare("
        SELECT boom_chat.*, boom_users.*
        FROM boom_chat
        LEFT JOIN boom_users ON boom_chat.user_id = boom_users.user_id
        WHERE boom_chat.post_id = ? LIMIT 1
    ");
    $stmt_chat->bind_param("i", $post_id);
    $stmt_chat->execute();
    $chat_result = $stmt_chat->get_result();
    if ($chat_result->num_rows > 0) {
        $rep = $chat_result->fetch_assoc();
        $repp = array_merge($report, $rep);
        // Optionally, you could return the merged result for further processing
        echo json_encode(['status' => 'success', 'report' => $repp]);
    } else {
        // If no chat data is found, delete the report and notify staff
        $delete_stmt = $mysqli->prepare("DELETE FROM boom_report WHERE report_id = ? AND report_type = 1");
        $delete_stmt->bind_param("i", $id);
        $delete_stmt->execute();
        updateStaffNotify();
        echo json_encode(['status' => 'success', 'message' => 'Report deleted and staff notified.']);
    }
    // Close statements
    $stmt->close();
    $stmt_chat->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Chat report ID is missing.']);
    exit();
}
?>

<div class="pad20">
	<div class="head_report pad10 vmargin10 background_box">
		<?php echo boomTemplate('element/log_chat', $repp); ?>
	</div>
	<div class="btable tpad10" id="report_control">
		<div class="bcell report_action">
			<button onclick="removeReport(1,<?php echo $repp['report_id']; ?>, <?php echo $repp['user_id']; ?>);" class="remove_report reg_button delete_btn"><?php echo $lang['delete']; ?></button>
			<button onclick="unsetReport(<?php echo $repp['report_id']; ?>, 1);" class="unset_report reg_button default_btn"><?php echo $lang['action_none']; ?></button>
		</div>
	</div>
</div>