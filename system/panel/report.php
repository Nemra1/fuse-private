<?php
require_once('../config_session.php');

if (!boomAllow(70)) {
    die();
}

$report_list = '';
$query = "
    SELECT boom_report.*, boom_users.user_name, boom_users.user_id, boom_users.user_color, boom_users.user_tumb
    FROM boom_report
    JOIN boom_users ON boom_report.report_user = boom_users.user_id
    ORDER BY report_date DESC
    LIMIT 40
";

$find_report = $mysqli->query($query);

if ($find_report->num_rows > 0) {
    while ($report = $find_report->fetch_assoc()) {
        $report_list .= boomTemplate('element/report_notify', $report);
    }
} else {
    $report_list .= emptyZone($lang['no_report']);
}

mysqli_close($mysqli);
?>
<div class="boom_keep" id="container_report">
    <?php echo $report_list; ?>
</div>
