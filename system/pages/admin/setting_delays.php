<?php

require __DIR__ . "../../../config_session.php";


if (!boomAllow(90)) {
    exit;
}

echo elementTitle($lang["delay_settings"]);
?>

<div class="page_full">
    <div class="page_element">
        <div class="boom_form">
            <?php
            $settings = [
                'act_delay' => $lang["innactive_logout"],
                'chat_delete' => $lang["chat_delete"],
                'private_delete' => $lang["private_delete"],
                'wall_delete' => $lang["wall_delete"],
                'member_delete' => $lang["member_delete"],
                'room_delete' => $lang["room_delete"]
            ];

            $options = [
                'act_delay' => [5, 10, 15, 30, 60, 120, 180, 360, 720, 1440, 2880, 10080],
                'chat_delete' => [30, 60, 180, 360, 720, 1440, 2880, 4320, 5760, 7200, 8640, 10080, 20160, 43200, 86400, 129600, 518400],
                'private_delete' => [30, 60, 180, 360, 720, 1440, 2880, 4320, 5760, 7200, 8640, 10080, 20160, 43200, 86400, 129600, 518400],
                'wall_delete' => [1440, 2880, 4320, 5760, 7200, 8640, 10080, 43200, 86400, 129600, 518400],
                'member_delete' => [43200, 86400, 129600, 518400],
                'room_delete' => [60, 120, 180, 360, 720, 1440, 2880, 4320, 5760, 7200, 8640, 10080, 20160, 43200, 86400, 129600]
            ];

            foreach ($settings as $key => $label) {
                $value = $data[$key] ?? 0;
                echo "<div class=\"setting_element\">
                        <p class=\"label\">$label</p>
                        <select id=\"set_$key\">
                            <option value=\"0\">" . htmlspecialchars($lang["never"], ENT_QUOTES, 'UTF-8') . "</option>";
                echo optionMinutes($value, $options[$key]);
                echo "  </select>
                      </div>";
            }
            ?>
			<div class="setting_element">
				<p class="label">Keep all users online <span class="badge">New</span></p>
				<select id="online_forever">
				<?php echo yesNo($data["online_forever"]); ?>
				</select>
			</div>
            <button data="delays" type="button" class="save_admin reg_button theme_btn">
                <i class="ri-save-line"></i> <?php echo htmlspecialchars($lang["save"], ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </div>
    </div>
</div>

<?php
?>
