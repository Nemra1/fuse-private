<?php

require __DIR__ . "../../../config_session.php";

if (!boomAllow(100)) {
    exit;
}

// Diagnostic icons
$icons = [
    'good' => '<i class="ri-chat-check-line success"></i>',
    'bad' => '<i class="ri-close-circle-line error"></i>',
    'warn' => '<i class="ri-error-warning-line warn"></i>',
];

// Default status
$statuses = [
    'upload' => $icons['good'],
    'avatar' => $icons['good'],
    'cover' => $icons['good'],
    'gd' => $icons['good'],
    'php' => $icons['good'],
    'curl' => $icons['good'],
    'zip' => $icons['good'],
    'mbstring' => $icons['good'],
];

// Checks
$statuses['avatar'] = !is_writable(dirname(BOOM_PATH . "/avatar")) ? $icons['bad'] : $icons['good'];
$statuses['cover'] = !is_writable(dirname(BOOM_PATH . "/cover")) ? $icons['bad'] : $icons['good'];
$statuses['database'] = !is_writable(BOOM_PATH . "/system/database.php") ? $icons['bad'] : $icons['good'];
$statuses['upload'] = !is_writable(dirname(BOOM_PATH . "/upload")) ? $icons['bad'] : $icons['good'];
$statuses['gd'] = !extension_loaded("gd") && !function_exists("gd_info") ? $icons['bad'] : $icons['good'];
$statuses['php'] = version_compare(PHP_VERSION, "5.6.0", "<") ? $icons['bad'] : (version_compare(PHP_VERSION, "7.3.0", ">=") ? $icons['warn'] : $icons['good']);
$statuses['curl'] = !function_exists("curl_init") ? $icons['bad'] : $icons['good'];
$statuses['zip'] = !extension_loaded("zip") ? $icons['bad'] : $icons['good'];
$statuses['mbstring'] = !extension_loaded("mbstring") ? $icons['bad'] : $icons['good'];

function renderDiagnosticRow($label, $status) {
    return "
    <div class=\"listing_reg\">
        <div class=\"listing_reg_content\">
            $label
        </div>
        <div class=\"listing_reg_icon\">
            $status
        </div>
    </div>";
}

echo elementTitle($lang["system_diagnostic"]);
echo "<div class=\"page_full\">
    <div class=\"page_element\">
        " . renderDiagnosticRow('Php version 5.6 - 7.2', $statuses['php']) . "
        " . renderDiagnosticRow('GD is installed', $statuses['gd']) . "
        " . renderDiagnosticRow('Curl is installed', $statuses['curl']) . "
        " . renderDiagnosticRow('Zip is installed', $statuses['zip']) . "
        " . renderDiagnosticRow('Mbstring is installed', $statuses['mbstring']) . "
        " . renderDiagnosticRow('Avatar folder is writable', $statuses['avatar']) . "
        " . renderDiagnosticRow('Cover folder is writable', $statuses['cover']) . "
        " . renderDiagnosticRow('Upload folder is writable', $statuses['upload']) . "
    </div>
</div>";

echo elementTitle($lang["system_info"]);
echo "<div class=\"page_full\">
    <div class=\"page_element\">
        " . renderDiagnosticRow($lang["current_version"] . " " . $data["version"], '') . "
        " . renderDiagnosticRow($lang["php_version"] . " " . PHP_VERSION, '') . "
        " . renderDiagnosticRow($lang["max_upload"] . " " . ini_get("upload_max_filesize"), '') . "
    </div>
</div>";

?>
