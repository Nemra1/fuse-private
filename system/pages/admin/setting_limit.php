<?php
require_once('../../config_admin.php');
if (!boomAllow(90)) {
    exit;
}
?>
<div id="page_wrapper">
	<div class="page_full">
	    	<?php echo elementTitle($lang["limit_management"]);?>

	</div>
	<div class="page_full">
		<div>
			<div class="tab_menu">
				<ul>
					<li class="tab_menu_item tab_selected" data="limtab" data-z="limit_profile"><?php echo $lang["account"]; ?></li>
					<li class="tab_menu_item" data="limtab" data-z="limit_upload"><?php echo $lang["upload"]; ?></li>
					<li class="tab_menu_item" data="limtab" data-z="limit_chat"><?php echo $lang["chat"]; ?></li>
					<li class="tab_menu_item" data="limtab" data-z="limit_display"><?php echo $lang["display"]; ?></li>
					<li class="tab_menu_item " data="limtab" data-z="limit_other"><?php echo $lang["other"]; ?></li>
				</ul>
			</div>
		</div>
		<div class="page_element">
			<div id="limtab">
				<div id="limit_profile" class="tab_zone" style="display: block;">
					<div class="boom_form">
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_avatar"]; ?></p>
							<select id="set_allow_avatar">
							<?php echo listRank($data["allow_avatar"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_name"]; ?></p>
							<select id="set_allow_name" >
							    	<?php echo listRank($data["allow_name"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_cover"]; ?></p>
							<select id="set_allow_cover">
							    	<?php echo listRank($data["allow_cover"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_gcover"]; ?></p>
							<select id="set_allow_gcover">
							    <?php echo listRank($data["allow_gcover"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_mood"]; ?></p>
							<select id="set_allow_mood">
							      <?php echo listRank($data["allow_mood"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_verify"]; ?></p>
							<select id="set_allow_verify">
							    <?php echo listRank($data["allow_verify"], 0);?>
							</select>
						</div>
					</div>
					<button data="limitation" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> Save</button>
				</div>
				<div id="limit_upload" class="hide_zone tab_zone" style="display: none;">
					<div class="boom_form">
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_cupload"]; ?></p>
							<select id="set_allow_cupload">
							    <?php echo listRank($data["allow_cupload"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_pupload"]; ?></p>
							<select id="set_allow_pupload">
							    <?php echo listRank($data["allow_pupload"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_wupload"]; ?></p>
							<select id="set_allow_wupload">
							    <?php echo listRank($data["allow_wupload"], 0);?>
							</select>
						</div>
					</div>
					<button data="limitation" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> <?php echo $lang["save"]; ?></button>
				</div>
				<div id="limit_display" class="hide_zone tab_zone" style="display: none;">
					<div class="boom_form">
						<div class="setting_element">
							<p class="label"><?php echo $lang["name_color"]; ?></p>
							<select id="set_allow_name_color">
							    <?php echo listRank($data["allow_name_color"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_colors"]; ?></p>
							<select id="set_allow_colors">
							    <?php echo listRank($data["allow_colors"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["name_grad"]; ?></p>
							<select id="set_allow_name_grad">
							    <?php echo listRank($data["allow_name_grad"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_grad"]; ?></p>
							<select id="set_allow_grad">
							     <?php echo listRank($data["allow_grad"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["name_neon"]; ?></p>
							<select id="set_allow_name_neon">
							      <?php echo listRank($data["allow_name_neon"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_neon"]; ?></p>
							<select id="set_allow_neon">
							    <?php echo listRank($data["allow_neon"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["name_font"]; ?></p>
							<select id="set_allow_name_font" >
							     <?php echo listRank($data["allow_name_font"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_font"]; ?></p>
							<select id="set_allow_font">
							     <?php echo listRank($data["allow_font"], 0);?>
							</select>
						</div>
					</div>
					<button data="limitation" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> <?php echo $lang["save"]; ?></button>
				</div>
				<div id="limit_chat" class="hide_zone tab_zone">
					<div class="boom_form">
					<div class="setting_element">
						<p class="label"><?php echo $lang['allow_main']; ?></p>
						<select id="set_allow_main">
							<?php echo listRank($data['allow_main'], 1); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang['allow_private']; ?></p>
						<select id="set_allow_private">
							<?php echo listRank($data['allow_private'], 1); ?>
						</select>
					</div>
					<div class="setting_element" style="display:none">
						<p class="label"><?php echo $lang['allow_quote']; ?>(not active yet)</p>
						<select id="set_allow_quote">
							<?php echo listRank($data['allow_quote'], 0); ?>
						</select>
					</div>
					<div class="setting_element"style="display:none">
						<p class="label"><?php echo $lang['allow_pquote']; ?>(not active yet)</p>
						<select id="set_allow_pquote">
							<?php echo listRank($data['allow_pquote'], 0); ?>
						</select>
					</div>					
						<div class="setting_element">
							<p class="label"><?php echo $lang["emo_plus"]; ?></p>
							<select id="set_emo_plus">
                                <?php echo listRank($data["emo_plus"], 1);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_direct"]; ?></p>
							<select id="set_allow_direct">
                                <?php echo listRank($data["allow_direct"], 1);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_history"]; ?></p>
							<select id="set_allow_history">
                                <?php echo listRank($data["allow_history"], 1);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label">Allow Gift</p>
							<select id="set_allow_gift">
                                <?php echo listRank($data["can_gift"], 0);?>
							</select>
						</div>						
					</div>
					<button data="limitation" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> <?php echo $lang["save"]; ?></button>
				</div>
				<div id="limit_other" class="hide_zone tab_zone" style="display: none;">
					<div class="boom_form">
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_room"]; ?></p>
							<select id="set_allow_room">
                            <?php echo listRank($data["allow_room"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"><?php echo $lang["allow_user_theme"]; ?></p>
							<select id="set_allow_theme">
                            <?php echo listRank($data["allow_theme"], 0);?>
							</select>
						</div>
						<div class="setting_element">
							<p class="label"> Allow photo Frame</p>
							<select id="set_allow_frame">
                            <?php echo listRank($data["can_frame"], 0);?>
							</select>
						</div>
					</div>
					<button data="limitation" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> <?php echo $lang["save"]; ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
