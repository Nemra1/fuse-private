<?php

require __DIR__ . "../../../config_session.php";

if (!boomAllow($cody["can_manage_addons"])) {
    exit;
}

echo elementTitle($lang["addons_management"]);
?>

<div class="page_full">
    <div class="page_element">
        <div id="addons_list">
            <?php echo adminAddonsList(); ?>
        </div>
    </div>
</div>

<?php

function adminAddonsList()
{
    global $mysqli, $lang;

    $addons_list = "";
    $avail_update = 0;
    $dir = glob(BOOM_PATH . "/addons/*", GLOB_ONLYDIR);

    foreach ($dir as $dirnew) {
        $install = 0;
        $addon = str_replace(BOOM_PATH . "/addons/", "", $dirnew);

        if (file_exists(BOOM_PATH . "/addons/" . $addon . "/system/install.php")) {
            $avail_update++;
            
            // Use prepared statements to avoid SQL injection
            $stmt = $mysqli->prepare("SELECT * FROM boom_addons WHERE addons = ?");
            $stmt->bind_param("s", $addon);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $addons = $result->fetch_assoc();
                $addons_list .= boomTemplate("element/addons_uninstall", $addons);
            } else {
                $addons_list .= boomTemplate("element/addons_install", $addon);
            }

            $stmt->close();
        }
    }

    if ($avail_update > 0) {
        return $addons_list;
    }

    return emptyZone($lang["no_addons"]);
}

?>
