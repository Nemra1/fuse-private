<?php

require __DIR__ . "/../../../../config_session.php";

if (!boomAllow(90)) {
    exit;
}

echo elementTitle($lang["player_settings"]);
?>
<div class="page_full">
    <div class="page_element">
        <div class="boom_form">
            <div class="setting_element">
                <p class="label"><?= $lang["default_stream"]; ?></p>
                <select id="set_default_player">
                    <?= adminPlayer($data["player_id"], 2); ?>
                </select>
            </div>
        </div>
        <button data="player" type="button" class="save_admin reg_button theme_btn">
            <i class="ri-save-line"></i> <?= $lang["save"]; ?>
        </button>
        <button type="button" onclick="openAddPlayer();" class="reg_button default_btn">
            <i class="ri-save-3-fill"></i> <?= $lang["add_player_stream"]; ?>
        </button>
    </div>
    <div class="page_element">
        <div id="admin_stream_list">
            <?= listStreamPlayer(); ?>
        </div>
    </div>
</div>

<?php

function listStreamPlayer() {
    global $mysqli, $lang, $data;

    $stream_list = "";
    $getstream = $mysqli->query("SELECT * FROM boom_radio_stream ORDER BY stream_alias ASC");

    if ($getstream->num_rows > 0) {
        while ($stream = $getstream->fetch_assoc()) {
            $stream["default"] = ($stream["id"] == $data["player_id"]) 
                ? "<div class=\"sub_list_selected\"><i class=\"ri-circle-fill success\"></i></div>" 
                : "";
            $stream_list .= boomTemplate("element/stream_player", $stream);
        }
    } else {
        $stream_list .= emptyZone($lang["empty"]);
    }

    return $stream_list;
}

?>
