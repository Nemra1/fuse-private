<?php
require(__DIR__ . '/../../config_admin.php');

if(!boomAllow(100)){
	die();
}
?>
<?php echo elementTitle($lang['call_settings']); ?>
<div class="page_full">
	<div>		
		<div class="tab_menu">
			<ul>
				<li class="tab_menu_item tab_selected" data="call_tab" data-z="call_zone"><?php echo $lang['settings']; ?></li>
				<li class="tab_menu_item" data="call_tab" data-z="callm_zone"><?php echo $lang['manage_call']; ?></li>
			</ul>
		</div>
	</div>
	<div id="call_tab">
		<div id="call_zone" class="tab_zone">
			<div class="page_element">
				<div class="form_content">
					<div class="setting_element ">
						<p class="label"><?php echo $lang['use_call']; ?> <?php echo createInfo('agora'); ?></p>
						<select id="set_use_call">
							<?php echo onOff($data['use_call']); ?>
						</select>
					</div>
					<div class="setting_element ">
						<p class="label">Server Type</p>
							<select id="set_call_server_type">
								<option value="agora" <?php if($data['call_server_type']=='agora'){echo 'selected';}?>>Agora</option>
								<option value="peerjs" <?php if($data['call_server_type']=='peerjs'){echo 'selected';}?>>Socket.io</option>
							</select>

					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_vcall']; ?></p>
						<select id="set_can_vcall">
							<?php echo listRank($data['can_vcall']); ?>
						</select>
					</div>
					<div class="setting_element ">
						<p class="label"><?php echo $lang['can_acall']; ?></p>
						<select id="set_can_acall">
							<?php echo listRank($data['can_acall']); ?>
						</select>
					</div>
					<div class="setting_element agora-settings">
						<p class="label"><?php echo $lang['call_appid']; ?></p>
						<input id="set_call_appid" class="full_input" value="<?php echo $data['call_appid']; ?>" type="text"/>
					</div>
					<div class="setting_element agora-settings">
						<p class="label"><?php echo $lang['call_secret']; ?></p>
						<input id="set_call_secret" class="full_input" value="<?php echo $data['call_secret']; ?>" type="text"/>
					</div>
					<div class="setting_element ">
						<p class="label"><?php echo $lang['call_max']; ?></p>
						<select id="set_call_max">
							<?php echo optionMinutes($data['call_max'], array(5,10,15,30,45,60,120,180,360,720,1440)); ?>
						</select>
					</div>
					<div class="setting_element ">
						<p class="label"><?php echo $lang['payment_method']; ?></p>
						<select id="set_call_method">
							<option value="1" <?php echo selCurrent($data['call_method'], 1); ?>><?php echo $lang['gold']; ?></option>
						</select>
					</div>
					<div class="setting_element ">
						<p class="label"><?php echo $lang['call_acost']; ?></p>
						<select id="set_call_cost">
							<?php echo optionCount($data['call_cost'], 0, 9, 1); ?>
							<?php echo optionCount($data['call_cost'], 10, 100, 5); ?>
							<?php echo optionCount($data['call_cost'], 150, 1000, 50); ?>
							<?php echo optionCount($data['call_cost'], 1100, 10000, 100); ?>
						</select>
					</div>
				</div>
				<div class="form_control">
					<button data="call_system" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> <?php echo $lang['save']; ?></button>
				</div>
			</div>
		</div>


		<div id="callm_zone" class="tab_zone hide_zone">
			<div class="page_element">
				<div class="bpad15">
					<button onclick="reloadAdminCall();" type="button" class="reg_button theme_btn "><i class="ri-restart-fill"></i> <?php echo $lang['reload']; ?></button>
				</div>
				<div id="admin_calls">
				<?php echo listAdminCall(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script data-cfasync="false">
reloadAdminCall = function(){
	$.post(FU_Ajax_Requests_File(), {
			f:"action_call",
			s:"reload_call",
			reload_call: 1,
		}, function(response) {
			if(response.code == 0){
				callSaved(system.error,3);
			}
			else {
				$('#admin_calls').html(response.content);
			}
	});	
}
adminCancelCall = function(id){
		$.post(FU_Ajax_Requests_File(), {
			f:"action_call",
			s:"reload_call",
			admin_cancel: id,
		}, function(response) {
			if(response.code == 0){
				callSaved(system.error,3);
			}
			else {
				callSaved(system.actionComplete,1);
				$('#admincall'+id).replaceWith(response);
				hideModal();
			}
	});	
}
getCallInfo = function(id){
	$.post('system/box/call_info.php', {
			call_id: id,
		}, function(response) {
			if(response == 0){
				callSaved(system.error,3);
			}
			else {
				showModal(response);
			}
	});	
}
$(document).ready(function () {
    // Listen for changes in the dropdown
    $('#set_call_server_type').on('change', function () {
        const selectedValue = $(this).val(); // Get the selected value
        if (selectedValue === 'peerjs') {
            // Show PeerJS settings and hide Agora settings
            $('.peerjs-settings').show();
            $('.agora-settings').hide();
        } else if (selectedValue === 'agora') {
            // Show Agora settings and hide PeerJS settings
            $('.agora-settings').show();
            $('.peerjs-settings').hide();
        }
    });
    // Trigger the change event on page load to set initial visibility
    $('#set_call_server_type').trigger('change');
});
</script>