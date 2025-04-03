<?php
require('../config_session.php');

$show_report = '';

if (!canManageReport()) {
    echo json_encode(['status' => 'error', 'message' => 'Permission denied.']);
    exit();
}

if (isset($_POST['private_report'])) {
    $id = escape($_POST['private_report']);  // Sanitize the report ID input
    // Fetch report info securely using prepared statements
    $stmt_report = $mysqli->prepare("SELECT * FROM boom_report WHERE report_id = ? AND report_type = 3");
    $stmt_report->bind_param("i", $id);
    $stmt_report->execute();
    $result_report = $stmt_report->get_result();
    if ($result_report->num_rows < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Report not found or has been deleted.']);
        exit();
    }
    $report = $result_report->fetch_assoc();
    // Fetch private log data securely
    $stmt_privlog = $mysqli->prepare("
        SELECT log.*, boom_users.user_id, boom_users.user_name, boom_users.user_color, boom_users.user_tumb, boom_users.user_bot 
        FROM ( 
            SELECT * FROM boom_private 
            WHERE (hunter = ? AND target = ?) OR (hunter = ? AND target = ?) 
            ORDER BY id DESC LIMIT ?
        ) AS log 
        LEFT JOIN boom_users ON log.hunter = boom_users.user_id 
        ORDER BY log.time DESC
    ");
    // Securely bind the parameters for the private log query
    $stmt_privlog->bind_param("iiiii", $report['report_user'], $report['report_target'], $report['report_target'], $report['report_user'], $cody['report_history']);
    $stmt_privlog->execute();
    $result_privlog = $stmt_privlog->get_result();
    if ($result_privlog->num_rows > 0) {
        while ($log = $result_privlog->fetch_assoc()) {
            $show_report .= privateLog($log, $report['report_user']);
        }
    } else {
        // Delete report if no private logs found and notify staff
        $stmt_delete = $mysqli->prepare("DELETE FROM boom_report WHERE report_id = ? AND report_type = 3");
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();
        updateStaffNotify();
        echo json_encode(['status' => 'success', 'message' => 'No logs found, report deleted.']);
        exit();
    }
    echo json_encode(['status' => 'success', 'report' => $show_report]);
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Private report ID is missing.']);
    exit();
}
?>

<div>
    <div id="preport_box" class="background_box box_height300 pad20">
        <?php echo $show_report; ?>
    </div>
    <div class="btable pad20" id="report_control">
        <div class="bcell report_action">
            <button onclick="removeReport(3,<?php echo $report['report_id']; ?>, <?php echo $report['report_target']; ?>);" class="reg_button delete_btn"><?php echo $lang['do_action']; ?></button>
            <button onclick="unsetReport(<?php echo $report['report_id']; ?>, 3);" class="unset_report reg_button default_btn"><?php echo $lang['action_none']; ?></button>
        </div>
    </div>
</div>
