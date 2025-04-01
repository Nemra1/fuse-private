<?php
require __DIR__ . "../../../config_admin.php";

if(!boomAllow(100)){
	die();
}

?>
<?php echo elementTitle($lang['gift_settings']); ?>
<div class="page_full">
	<div class="page_element">
		<div class="setting_element ">
			<p class="label"><?php echo $lang['use_gift']; ?>  <?php echo createInfo('gift'); ?></p>
			<select id="set_use_gift">
				<?php echo onOff($data['use_gift']); ?>
			</select>
		</div>
	</div>
	<div class="page_element">
		<div class="btable_auto brelative">
			<button onclick="addGift();" class="theme_btn reg_button"><i class="ri-add-circle-line"></i> <?php echo $lang['add_gift']; ?></button>
			<input id="add_gift" class="up_input" name="thumb_file" onchange="addGift();" type="file">
		</div>
	</div>
	<div class="page_full">
		<div class="page_element">
			<div id="gift_list">
				<?php echo listAdminGift(); ?>
			</div>
		</div>
	</div>
</div>