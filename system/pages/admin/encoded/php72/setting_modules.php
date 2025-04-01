<?php

require __DIR__ . "/../../../../config_session.php";
if (!boomAllow(90)) {
    exit;
}

echo elementTitle($lang["manage_modules"]);
?>

<div class="page_full">
    <div class="page_element">
        <div class="boom_form">
            <div class="setting_element">
                <p class="label">Profile like</p>
                <select id="set_use_like">
                    <?= onOff($data["use_like"]); ?>
                </select>
            </div>            
            <div class="setting_element">
                <p class="label"><?= $lang["room_system"]; ?></p>
                <select id="set_use_lobby">
                    <?= onOff($data["use_lobby"]); ?>
                </select>
            </div>
            <div class="setting_element">
                <p class="label"><?= $lang["wall_system"]; ?></p>
                <select id="set_use_wall">
                    <?= onOff($data["use_wall"]); ?>
                </select>
            </div>
            <div class="setting_element">
                <p class="label"><?= $lang["cookie_system"]; ?></p>
                <select id="set_cookie_law">
                    <?= onOff($data["cookie_law"]); ?>
                </select>
            </div>
			<div class="setting_element ">
				<p class="label">Geolocalisation</p>
				<select id="set_use_geo">
					<?php echo onOff($data['use_geo']); ?>
				</select>
			</div>            
        </div>
        <button data="modules" type="button" class="save_admin reg_button theme_btn">
            <i class="ri-save-line"></i> <?= $lang["save"]; ?>
        </button>
    </div>
</div>
