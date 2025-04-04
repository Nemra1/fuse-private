<?php
require(__DIR__ . '/../../config_admin.php');

if(!boomAllow(100)){
	die();
}
?>
<?php echo elementTitle($lang['level_settings']); ?>
<div class="page_full">
	<div class="page_element">
		<div class="form_content">
			<div class="setting_element ">
				<p class="label"><?php echo $lang['use_level']; ?></p>
				<select id="set_use_level">
					<?php echo onOff($data['use_level']); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label"><?php echo $lang['exp_chat']; ?></p>
				<select id="set_exp_chat">
					<?php echo optionCount($data['exp_chat'], 0, 10, 1, $lang['xp']); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label"><?php echo $lang['exp_priv']; ?></p>
				<select id="set_exp_priv">
					<?php echo optionCount($data['exp_priv'], 0, 10, 1, $lang['xp']); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label"><?php echo $lang['exp_post']; ?></p>
				<select id="set_exp_post">
					<?php echo optionCount($data['exp_post'], 0, 10, 1, $lang['xp']); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label"><?php echo $lang['exp_gift']; ?></p>
				<select id="set_exp_gift">
					<?php echo optionCount($data['exp_gift'], 0, 10, 1, $lang['xp']); ?>
				</select>
			</div>
		</div>
		<div class="form_control">
		<button data="xp_system" type="button" class="save_admin reg_button theme_btn"> <i class="ri-save-line"></i> <?php echo $lang['save']; ?></button>
		</div>
	</div>
</div>