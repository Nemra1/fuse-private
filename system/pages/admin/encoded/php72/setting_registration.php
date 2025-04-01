<?php

require __DIR__ . "/../../../../config_session.php";

if (!boomAllow(90)) {
    exit;
}

echo elementTitle($lang["registration_settings"]);

?>

<div class="page_full">
    <div>
        <div class="tab_menu">
            <ul>
                <li class="tab_menu_item tab_selected" data="rtab" data-z="main_registration"><?php echo $lang["main"]; ?></li>
                <li class="tab_menu_item" data="rtab" data-z="guest_registration"><?php echo $lang["guest"]; ?></li>
                <li class="tab_menu_item" data="rtab" data-z="social_registration"><?php echo $lang["social"]; ?></li>
                <li class="tab_menu_item" data="rtab" data-z="bridge_registration">Bridge</li>
                <li class="tab_menu_item " data="rtab" data-z="security_registration" style=" display: none; ">Security</li>
            </ul>
        </div>
    </div>

    <div class="page_element">
        <!-- Main Registration Tab -->
        <div id="rtab">
            <div id="main_registration" class="tab_zone">
                <div class="boom_form">
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["allow_registration"]; ?></p>
                        <select id="set_registration">
                            <?php echo yesNo($data["registration"]); ?>
                        </select>
                    </div>
                     <div class="setting_element ">
						<p class="label"><?php echo $lang['max_reg']; ?></p>
						<select id="set_max_reg">
							<?php echo optionCount($data['max_reg'], 1, 50, 1); ?>
						</select>
					</div>                   
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["regmute"]; ?></p>
                        <select id="set_regmute">
                            <option value="0"><?php echo $lang["off"]; ?></option>
                            <?php echo optionMinutes($data["regmute"], [2, 5, 10, 15, 20, 25, 30, 45, 60, 120]); ?>
                        </select>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["validate"]; ?></p>
                        <select id="set_activation">
                            <?php echo yesNo($data["activation"]); ?>
                        </select>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["max_name"]; ?></p>
                        <select id="set_max_username">
                            <?php echo optionCount($data["max_username"], 4, 20, 1, ""); ?>
                        </select>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["min_age"]; ?></p>
                        <select id="set_min_age">
                            <?php echo optionCount($data["min_age"], 9, 99, 1, ""); ?>
                        </select>
                    </div>
                </div>
                <button data="registration" type="button" class="save_admin reg_button theme_btn">
                    <i class="ri-save-line"></i> <?php echo $lang["save"]; ?>
                </button>
            </div>

            <!-- Guest Registration Tab -->
            <div id="guest_registration" class="hide_zone tab_zone">
                <div class="boom_form">
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["allow_guest"]; ?></p>
                        <select id="set_allow_guest">
                            <?php echo yesNo($data["allow_guest"]); ?>
                        </select>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["max_greg"]; ?></p>
                        <select id="set_max_greg">
                            <?php echo optionCount($data['guest_per_day'], 1, 20, 1); ?>
                        </select>
                    </div>                    
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["guest_form"]; ?></p>
                        <select id="set_guest_form">
                            <?php echo yesNo($data["guest_form"]); ?>
                        </select>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["guest_talk"]; ?></p>
                        <select id="set_guest_talk">
                            <?php echo yesNo($data["guest_talk"]); ?>
                        </select>
                    </div>
                </div>
                <button data="guest" type="button" class="save_admin reg_button theme_btn">
                    <i class="ri-save-line"></i> <?php echo $lang["save"]; ?>
                </button>
            </div>

            <!-- Bridge Registration Tab -->
            <div id="bridge_registration" class="hide_zone tab_zone">
                <div class="boom_form">
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["bridge"]; ?></p>
                        <select id="set_use_bridge">
                            <?php echo yesNo($data["use_bridge"]); ?>
                        </select>
                    </div>
                </div>
                <button data="bridge_registration" type="button" class="save_admin reg_button theme_btn">
                    <i class="ri-save-line"></i> <?php echo $lang["save"]; ?>
                </button>
            </div>

            <!-- Social Registration Tab -->
            <div id="social_registration" class="hide_zone tab_zone">
                <div class="boom_form">
                    <!-- Facebook -->
                    <div class="setting_element">
                        <p class="label"><i class="fa fbook ri-facebook-circle-line"></i> <?php echo processSocial("Facebook", 1); ?></p>
                        <select id="set_facebook_login">
                            <?php echo yesNo($data["facebook_login"]); ?>
                        </select>
                        <p class="sub_text sub_label"><i class="ri-earth-line theme_color"></i> <?php echo $data["domain"]; ?>/login/facebook_login.php</p>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo processSocial("Facebook", 2); ?></p>
                        <input id="set_facebook_id" class="full_input" value="<?php echo $data["facebook_id"]; ?>" type="text"/>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo processSocial("Facebook", 3); ?></p>
                        <input id="set_facebook_secret" class="full_input" value="<?php echo $data["facebook_secret"]; ?>" type="text"/>
                    </div>
                    
                    <!-- Google -->
                    <div class="clear15"></div>
                    <div class="setting_element">
                        <p class="label"><i class="fa gplus ri-google-fill"></i> <?php echo processSocial("Google", 1); ?></p>
                        <select id="set_google_login">
                            <?php echo yesNo($data["google_login"]); ?>
                        </select>
                        <p class="sub_text sub_label"><i class="ri-earth-line theme_color"></i> <?php echo $data["domain"]; ?>/login/google_login.php</p>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo processSocial("Google", 2); ?></p>
                        <input id="set_google_id" class="full_input" value="<?php echo $data["google_id"]; ?>" type="text"/>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo processSocial("Google", 3); ?></p>
                        <input id="set_google_secret" class="full_input" value="<?php echo $data["google_secret"]; ?>" type="text"/>
                    </div>
                    
                    <!-- Twitter -->
                    <div class="clear15"></div>
                    <div class="setting_element">
                        <p class="label"><i class="fa twit ri-twitter-fill"></i> <?php echo processSocial("Twitter", 1); ?></p>
                        <select id="set_twitter_login">
                            <?php echo yesNo($data["twitter_login"]); ?>
                        </select>
                        <p class="sub_text sub_label"><i class="ri-earth-line theme_color"></i> <?php echo $data["domain"]; ?>/login/twitter_login.php</p>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo processSocial("Twitter", 2); ?></p>
                        <input id="set_twitter_id" class="full_input" value="<?php echo $data["twitter_id"]; ?>" type="text"/>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo processSocial("Twitter", 3); ?></p>
                        <input id="set_twitter_secret" class="full_input" value="<?php echo $data["twitter_secret"]; ?>" type="text"/>
                    </div>
                </div>
                <button data="social_registration" type="button" class="save_admin reg_button theme_btn">
                    <i class="ri-save-line"></i> <?php echo $lang["save"]; ?>
                </button>
            </div>

            <!-- Security Registration Tab -->
            <div id="security_registration" class="hide_zone tab_zone" style=" display: none; ">
                <div class="boom_form">
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["use_recapt"]; ?></p>
                        <select id="set_use_recapt">
                            <?php echo yesNo($data["use_recapt"]); ?>
                        </select>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["recapt_site"]; ?></p>
                        <input id="set_recapt_key" class="full_input" value="<?php echo $data["recapt_key"]; ?>" type="text"/>
                    </div>
                    <div class="setting_element">
                        <p class="label"><?php echo $lang["recapt_secret"]; ?></p>
                        <input id="set_recapt_secret" class="full_input" value="<?php echo $data["recapt_secret"]; ?>" type="text"/>
                    </div>
                </div>
                <button data="security_registration" type="button" class="save_admin reg_button theme_btn">
                    <i class="ri-save-line"></i> <?php echo $lang["save"]; ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php

function processSocial($type, $mode) {
    global $lang;
    switch ($mode) {
        case 1:
            return str_replace("%type%", $type, $lang["social_login"]);
        case 2:
            return str_replace("%type%", $type, $lang["social_id"]);
        case 3:
            return str_replace("%type%", $type, $lang["social_secret"]);
    }
}

?>
