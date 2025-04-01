<?php
require __DIR__ . "../../../config_admin.php";

if(!boomAllow(100)){
	die();
}
?>
<?php echo elementTitle($lang['gold_settings']); ?>
<div class="page_full">
	<div class="page_element">
		<div class="form_content">
			<div class="setting_element ">
				<p class="label"><?php echo $lang['use_gold']; ?></p>
				<select id="set_use_gold">
					<?php echo onOff($data['use_gold']); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label"><?php echo $lang['can_sgold']; ?></p>
				<select id="set_can_sgold">
					<?php echo listRank($data['can_sgold']); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label"><?php echo $lang['can_rgold']; ?></p>
				<select id="set_can_rgold">
					<?php echo listRank($data['can_rgold']); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label"><?php echo $lang['allow_gold']; ?></p>
				<select id="set_allow_gold">
					<?php echo listRank($data['allow_gold']); ?>
				</select>
			</div>
			<div class="setting_element">
				<p class="label"><?php echo $lang['can_vgold']; ?></p>
				<select id="set_can_vgold">
					<?php echo listRank($data['can_vgold']); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label"><?php echo $lang['gold_delay']; ?></p>
				<select id="set_gold_delay">
					<?php echo optionMinutes($data['gold_delay'], array(1,2,3,4,5,10,15,20,25,30,60)); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label"><?php echo $lang['gold_base']; ?></p>
				<select id="set_gold_base">
					<?php echo optionCount($data['gold_base'], 1, 10, 1); ?>
				</select>
			</div>
		</div>
		<div class="form_control">
		    <button data="admin_gold" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i>  <?php echo $lang['save']; ?></button>
		</div>
	</div>
</div>