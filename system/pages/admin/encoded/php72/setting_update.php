<?php

require __DIR__ . "/../../../../config_session.php";

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
    $avail_update = 0;
    $dir = glob(BOOM_PATH . "/updates/*", GLOB_ONLYDIR);

    foreach ($dir as $dirnew) {
        $update = str_replace(BOOM_PATH . "/updates/", "", $dirnew);
        if ($data["version"] < $update && is_numeric($update)) {
            $avail_update++;
            $update_list .= boomTemplate("element/update_element", $update);
        }
    }

    if ($avail_update > 0) {
        return "<div>" . $update_list . "</div>";
    }

    return emptyZone($lang["no_update"]);
}

?>
