<?php
require __DIR__ . "../../../config_session.php";
if (!boomAllow(100)) {
    exit;
}
?>
<?php echo elementTitle($lang['staff_permission']); ?>
<div class="page_full">

	<div>		
		<div class="tab_menu">
			<ul>
				<li class="tab_menu_item tab_selected" data="staffperm" data-z="staff_act"><?php echo $lang['do_action']; ?></li>
				<li class="tab_menu_item" data="staffperm" data-z="staff_profile"><?php echo $lang['pro_action']; ?></li>
				<li class="tab_menu_item" data="staffperm" data-z="staff_system"><?php echo $lang['system']; ?></li>
				<li class="tab_menu_item" data="staffperm" data-z="staff_security" style="display: none;"><?php echo $lang['display']; ?></li>
				<li class="tab_menu_item" data="staffperm" data-z="staff_other" style="display: none;"><?php echo $lang['other']; ?></li>
			</ul>
		</div>
	</div>
	<div class="page_element">
		<div id="staffperm">
			<div id="staff_act" class="tab_zone">
				<div class="form_content">
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_mute']; ?></p>
						<select id="set_can_mute">
							<?php echo listRankStaff($data['can_mute']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_warn']; ?></p>
						<select id="set_can_warn">
							<?php echo listRankStaff($data['can_warn']); ?>
						</select>
					</div>
					
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_kick']; ?></p>
						<select id="set_can_kick">
							<?php echo listRankStaff($data['can_kick']); ?>
						</select>
					</div>
					<div class="setting_element" >
						<p class="label"><?php echo $lang['can_ghost']; ?></p>
						<select id="set_can_ghost">
								<?php echo listRankStaff($data["can_ghost"], 1);?>
						</select>
					</div>
					<div class="setting_element" >
						<p class="label"><?php echo $lang['can_vghost']; ?></p>
						<select id="set_can_vghost">
								<?php echo listRankStaff($data["can_vghost"], 1);?>
						</select>
					</div>					
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_ban']; ?></p>
						<select id="set_can_ban">
							<?php echo listRankStaff($data['can_ban']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_delete']; ?></p>
						<select id="set_can_delete">
							<?php echo listRankStaff($data['can_delete']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_rank']; ?></p>
						<select id="set_can_rank">
							<?php echo listRankStaff($data['can_rank']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_raction']; ?></p>
						<select id="set_can_raction">
							<?php echo listRankStaff($data['can_raction']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_flood']; ?></p>
						<select id="set_can_flood">
							<?php echo listRankStaff($data['can_flood']); ?>
						</select>
					</div>					
				</div>
				<div class="form_control">
					<button data="staff_limitation" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-3-fill"></i> <?php echo $lang['save']; ?></button>
				</div>
			</div>
			<div id="staff_profile" class="hide_zone tab_zone">
				<div class="form_content">
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_modavat']; ?></p>
						<select id="set_can_modavat">
							<?php echo listRankStaff($data['can_modavat']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_modcover']; ?></p>
						<select id="set_can_modcover">
							<?php echo listRankStaff($data['can_modcover']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_modmood']; ?></p>
						<select id="set_can_modmood">
							<?php echo listRankStaff($data['can_modmood']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_modabout']; ?></p>
						<select id="set_can_modabout">
							<?php echo listRankStaff($data['can_modabout']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_modcolor']; ?></p>
						<select id="set_can_modcolor">
							<?php echo listRankStaff($data['can_modcolor']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_modname']; ?></p>
						<select id="set_can_modname">
							<?php echo listRankStaff($data['can_modname']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_modemail']; ?></p>
						<select id="set_can_modemail">
							<?php echo listRankStaff($data['can_modemail']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_modpass']; ?></p>
						<select id="set_can_modpass">
							<?php echo listRankStaff($data['can_modpass']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_modvpn']; ?></p>
						<select id="set_can_modvpn">
							<?php echo listRankStaff($data['can_modvpn']); ?>
						</select>
					</div>
					<div class="setting_element" style="display: none;">
						<p class="label"><?php echo $lang['can_modblock']; ?></p>
						<select id="set_can_modblock">
							<?php echo listRankStaff($data['can_modblock']); ?>
						</select>
					</div>
					<div class="setting_element" style="display: none;">
						<p class="label"><?php echo $lang['can_verify']; ?></p>
						<select id="set_can_verify">
							<?php echo listRankStaff($data['can_verify']); ?>
						</select>
					</div>
					<div class="setting_element" style="display: none;">
						<p class="label"><?php echo $lang['can_note']; ?></p>
						<select id="set_can_note">
							<?php echo listRankStaff($data['can_note']); ?>
						</select>
					</div>
				</div>
				<div class="form_control">
					<button  data="staff_limitation" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-3-fill"></i> <?php echo $lang['save']; ?></button>
				</div>
			</div>
			<div id="staff_security" class="hide_zone tab_zone" style="display: none;">
				<div class="form_content">
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_vip']; ?></p>
						<select id="set_can_vip">
							<?php echo listRankStaff($data['can_vip']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_vemail']; ?></p>
						<select id="set_can_vemail">
							<?php echo listRankStaff($data['can_vemail']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_vname']; ?></p>
						<select id="set_can_vname">
							<?php echo listRankStaff($data['can_vname']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_vhistory']; ?></p>
						<select id="set_can_vhistory">
							<?php echo listRankStaff($data['can_vhistory']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_vother']; ?></p>
						<select id="set_can_vother">
							<?php echo listRankStaff($data['can_vother']); ?>
						</select>
					</div>
				</div>
				<div class="form_control">
					<button onclick="saveAdminStaffPermission();" type="button" class="reg_button theme_btn"><i class="fa fa-floppy-o"></i> <?php echo $lang['save']; ?></button>
				</div>
			</div>
			<div id="staff_system" class="hide_zone tab_zone" style="display: none;">
				<div class="form_content">
					<div class="setting_element"  style="display: none;">
						<p class="label"><?php echo $lang['can_news']; ?></p>
						<select id="set_can_news">
							<?php echo listRankSuper($data['can_news']); ?>
						</select>
					</div>
					<div class="setting_element"  style="display: none;">
						<p class="label"><?php echo $lang['can_mcontact']; ?></p>
						<select id="set_can_mcontact">
							<?php echo listRankSuper($data['can_mcontact']); ?>
						</select>
					</div>
					<div class="setting_element"  style="display: none;">
						<p class="label"><?php echo $lang['can_mip']; ?></p>
						<select id="set_can_mip">
							<?php echo listRankSuper($data['can_mip']); ?>
						</select>
					</div>
					<div class="setting_element"  style="display: none;">
						<p class="label"><?php echo $lang['can_mplay']; ?></p>
						<select id="set_can_mplay">
							<?php echo listRankSuper($data['can_mplay']); ?>
						</select>
					</div>
					<div class="setting_element"  style="display: none;">
						<p class="label"><?php echo $lang['can_mlogs']; ?></p>
						<select id="set_can_mlogs">
							<?php echo listRankSuper($data['can_mlogs']); ?>
						</select>
					</div>
					<div class="setting_element"  style="display: none;">
						<p class="label"><?php echo $lang['can_mroom']; ?></p>
						<select id="set_can_mroom">
							<?php echo listRankSuper($data['can_mroom']); ?>
						</select>
					</div>
					<div class="setting_element"  style="display: none;">
						<p class="label"><?php echo $lang['can_mfilter']; ?></p>
						<select id="set_can_mfilter">
							<?php echo listRankSuper($data['can_mfilter']); ?>
						</select>
					</div>
					<div class="setting_element"  style="display: none;">
						<p class="label"><?php echo $lang['can_maddons']; ?></p>
						<select id="set_can_maddons">
							<?php echo listRankSuper($data['can_maddons']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_dj']; ?></p>
						<select id="set_can_dj">
							<?php echo listRankSuper($data['can_dj']); ?>
						</select>
					</div>
					<div class="setting_element"  style="display: none;">
						<p class="label"><?php echo $lang['can_cuser']; ?></p>
						<select id="set_can_cuser">
							<?php echo listRankStaff($data['can_cuser']); ?>
						</select>
					</div>
				</div>
				<div class="form_control">
				    <button data="staff_limitation" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-3-fill"></i> </i> <?php echo $lang['save']; ?></button>
				</div>
			</div>
			<div id="staff_other" class="hide_zone tab_zone" style="display: none;">
				<div class="form_content">
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_inv']; ?></p>
						<select id="set_can_inv">
							<?php echo listRankStaff($data['can_inv']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_content']; ?></p>
						<select id="set_can_content">
							<?php echo listRankStaff($data['can_content']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_topic']; ?></p>
						<select id="set_can_topic">
							<?php echo listRankStaff($data['can_topic']); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['can_clear']; ?></p>
						<select id="set_can_clear">
							<?php echo listRankStaff($data['can_clear']); ?>
						</select>
					</div>
				</div>
				<div class="form_control">
					<button onclick="saveAdminStaffPermission();" type="button" class="reg_button theme_btn"><i class="fa fa-floppy-o"></i> <?php echo $lang['save']; ?></button>
				</div>
			</div>
		</div>
	</div>
</div>