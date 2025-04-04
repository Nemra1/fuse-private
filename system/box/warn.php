<?php
require('../config_session.php');
if(!isset($_POST['warn'])){
	die();
}
if(!canWarn()){
	die();
}
$target = escape($_POST['warn'], true);
$user = userDetails($target);

if(!canWarnUser($user)){
	return 0;
}
?>

<div class="modal_top extra_model_content">
	<div class="modal_top_empty">
		<div class="btable">
			<div class="avatar_top_mod">
				<img src="<?php echo myAvatar($user['user_tumb']); ?>"/>
			</div>
			<div class="avatar_top_name">
				<?php echo $user['user_name']; ?>
			</div>
		</div>
	</div>
	<div class="modal_top_element close_over">
		<i class="ri-close-circle-line i_btm"></i>
	</div>
</div>
<div class="pad_box">

	<div class="setting_element">
		<p class="label"><?php echo $lang['message']; ?></p>
		<textarea id="warn_reason" maxlength="300" class="full_textarea small_textarea" type="text"/></textarea>
	</div>
	<div class="tpad10">
		<button onclick="warnUser(<?php echo $user['user_id']; ?>);" class="reg_button delete_btn"><?php echo $lang['warn']; ?></button>
		<button class="close_over reg_button default_btn"><?php echo $lang['cancel']; ?></button>
	</div>
</div>