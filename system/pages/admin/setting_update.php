<?php

require __DIR__ . "../../../config_session.php";

if (!boomAllow(100)) {
    exit;
}

echo elementTitle($lang["update_zone"]);
?>

<div class="page_full">
    <div class="page_element">
        <div id="update_list">
            <?= getUpdateList(); ?>
        </div>
    </div>
</div>

<?php

function getUpdateList()
{
    global $mysqli, $data, $lang;

    $update_list = "";
    $available_updates = 0;
    $updateDirs = glob(BOOM_PATH . "/updates/*", GLOB_ONLYDIR);

    foreach ($updateDirs as $dir) {
        $updateVersion = basename($dir);
        if (is_numeric($updateVersion) && $data["version"] < $updateVersion) {
            $available_updates++;
            $update_list .= boomTemplate("element/update_element", $updateVersion);
        }
    }

    return $available_updates > 0 
        ? "<div>{$update_list}</div>" 
        : emptyZone($lang["no_update"]);
}
?>
