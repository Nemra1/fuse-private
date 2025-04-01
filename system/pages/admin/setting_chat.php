<?php

require __DIR__ . "../../../config_session.php";

if (!boomAllow(90)) {
    exit;
}

echo elementTitle($lang["chat_settings"]);
?>

<div class="page_full">
    <div class="page_element">
        <div class="boom_form">
            <div class="setting_element">
                <p class="label"><?php echo htmlspecialchars($lang["show_logs"], ENT_QUOTES, 'UTF-8'); ?></p>
                <select id="set_allow_logs">
                    <?php echo yesNo($data["allow_logs"]); ?>
                </select>
            </div>

            <div class="setting_element">
                <p class="label"><?php echo htmlspecialchars($lang["gender_icon"], ENT_QUOTES, 'UTF-8'); ?></p>
                <select id="set_gender_ico">
                    <?php echo yesNo($data["gender_ico"]); ?>
                </select>
            </div>

            <div class="setting_element">
                <p class="label"><?php echo htmlspecialchars($lang["flag_ico"], ENT_QUOTES, 'UTF-8'); ?></p>
                <select id="set_flag_ico">
                    <?php echo yesNo($data["flag_ico"]); ?>
                </select>
            </div>

            <div class="setting_element">
                <p class="label"><?php echo htmlspecialchars($lang["max_main"], ENT_QUOTES, 'UTF-8'); ?></p>
                <select id="set_max_main">
                    <?php echo optionCount($data["max_main"], 100, 1000, 100, ""); ?>
                </select>
            </div>

            <div class="setting_element">
                <p class="label"><?php echo htmlspecialchars($lang["max_private"], ENT_QUOTES, 'UTF-8'); ?></p>
                <select id="set_max_private">
                    <?php echo optionCount($data["max_private"], 100, 500, 50, ""); ?>
                </select>
            </div>

            <div class="setting_element">
                <p class="label"><?php echo htmlspecialchars($lang["max_offcount"], ENT_QUOTES, 'UTF-8'); ?></p>
                <select id="set_max_offcount">
                    <?php echo optionCount($data["max_offcount"], 0, 100, 5, ""); ?>
                </select>
            </div>

            <div class="setting_element">
                <p class="label"><?php echo htmlspecialchars($lang["speed"], ENT_QUOTES, 'UTF-8'); ?></p>
                <select id="set_speed">
                    <?php echo optionCount($data["speed"], 1500, 10000, 500, "ms"); ?>
                </select>
            </div>
        </div>

        <button data="chat" type="button" class="save_admin reg_button theme_btn">
            <i class="ri-save-line"></i> <?php echo htmlspecialchars($lang["save"], ENT_QUOTES, 'UTF-8'); ?>
        </button>
    </div>
</div>

<?php
?>
