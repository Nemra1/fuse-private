<?php


require __DIR__ . "/../../../../config_session.php";
if (!boomAllow(80)) {
    exit;
}
?>
<?php echo elementTitle($lang["main_settings"]); ?>
<div class="page_full">
	<div>
		<div class="tab_menu">
			<ul>
				<li class="tab_menu_item tab_selected" data="main_tab" data-z="main_zone"><?php echo $lang["main"]; ?></li>
				<?php if (boomAllow(100)) { ?>
				<li class="tab_menu_item " data="main_tab" data-z="maint_zone"><?php echo $lang["maintenance"]; ?></li>
				<?php }?>
				
			</ul>
		</div>
	</div>
	<div id="main_tab">
		<div id="main_zone" class="tab_zone" >
			<div class="page_element">
				<div class="boom_form">
					<div class="setting_element">
						<p class="label"><?php echo $lang["index_path"]; ?></p>
						<input id="set_index_path" class="full_input" value="<?php echo $data["domain"]; ?>" type="text" />
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang["site_title"]; ?></p>
						<input id="set_title" class="full_input" value="<?php echo $data["title"]; ?>" type="text" />
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang["site_description"]; ?></p>
						<input id="set_site_description" class="full_input" value="<?php echo $data["site_description"]; ?>" type="text" />
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang["site_keyword"]; ?></p>
						<input
							id="set_site_keyword"
							class="full_input"
							value="<?php echo $data["site_keyword"]; ?>"
							type="text"
						/>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang["timezone"]; ?></p>
						<select id="set_timezone">
						    <?php echo getTimezone($data["timezone"]); ?>
                        </select>
					</div>
					<div class="setting_element">
						<p class="label"><?php echo $lang["default_language"]; ?></p>
						<select id="set_default_language">
						     <?php echo listLanguage($data["language"], 1); ?>
						</select>
					</div>
					<div class="setting_element">
						<p class="label">Google Analytics</p>
						<input
							id="set_google_analytics"
							class="full_input"
							value="<?php echo $data["google_analytics"]; ?>"
							type="text"
						/>
					</div>					
				</div>
				<button data="main_settings" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> <?php echo $lang["save"]; ?></button>
			</div>
		</div>
		<?php if (boomAllow(100)) {?>
		<div id="maint_zone" class="tab_zone hide_zone" style="display: none;">
			<div class="page_element">
				<div class="boom_form">
					<div class="setting_element">
						<p class="label"><?php echo $lang["maint_mode"]; ?></p>
						<select id="set_maint_mode">
						     <?php echo onOff($data["maint_mode"]); ?>
						</select>
	
					</div>
				</div>
				<button data="maintenance" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> <?php echo $lang["save"]; ?></button>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
