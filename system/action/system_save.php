<?php


require "../config_session.php";

if (isset($_POST["store_name"]) && isset($_POST["store_pass"])) {
    if (!checkToken($_POST['utk'])) {
        exit('Invalid CSRF token');
    }   
    echo boomsystemvalidate();
    exit;
}
if (isset($_POST["save_admin_section"])) {
    $section = escape($_POST["save_admin_section"]);
    echo saveadminpanel($section);
    echo sendDataToSocket();
    exit;
}
if (isset($_POST["test_mail"]) && isset($_POST["test_email"])) {
    $data["user_email"] = escape($_POST["test_email"]);
    if (!boomAllow(90)) {
        exit;
    }
    echo sendEmail("test", $data);
    exit;
}
if (isset($_POST["save_page"]) && isset($_POST["page_content"]) && isset($_POST["page_target"])) {
    $content = softEscape($_POST["page_content"]);
    $target = escape($_POST["page_target"]);
    echo boompagecontent($content, $target);
    exit;
}
function saveAdminPanel($section){

    global $mysqli;
    global $data;
    global $cody;
    global $lang;
    if (!boomAllow(90)) {
        return 99;
    }


    if ($section == "main_settings" && boomAllow(90)) {
        if (isset($_POST["set_index_path"]) && isset($_POST["set_title"]) && isset($_POST["set_timezone"]) && isset($_POST["set_default_language"]) && isset($_POST["set_site_description"]) && isset($_POST["set_site_keyword"])) {
            $index = escape($_POST["set_index_path"]);
            $title = escape($_POST["set_title"]);
            $timezone = escape($_POST["set_timezone"]);
            $language = escape($_POST["set_default_language"]);
            $description = escape($_POST["set_site_description"]);
            $keyword = escape($_POST["set_site_keyword"]);
            $google_analytics = escape($_POST["set_google_analytics"]);
            if ($language != $data["language"]) {
                $mysqli->query("UPDATE boom_users SET user_language = '" . $language . "' WHERE user_id > 0");
            }
           $data_query = array(
    			"domain" => $index,
    			"title" => $title,
    			"site_description" => $description,
    			"site_keyword" 		=> $keyword,
    			"timezone" => $timezone,
    			"language" => $language,
    			"google_analytics" => $google_analytics,
			);
			$update = fu_update_dashboard($data_query);
			
            if ($language != $data["language"]) {
                return 2;
            }
           if($update ===true){
				return 1;
			}
        }
        return 99;
    }
    if ($section == "maintenance" && boomAllow(100)) {
        if (isset($_POST["set_maint_mode"])) {
            $maint_mode = escape($_POST["set_maint_mode"]);
            if ($maint_mode == 1 && $maint_mode != $data["maint_mode"]) {
                $mysqli->query("UPDATE boom_users SET user_action = user_action + 1 WHERE user_rank < 70");
            }
            $mysqli->query("UPDATE boom_setting SET maint_mode = '" . $maint_mode . "' WHERE id = '1'");
            return 1;
        }
        return 99;
    }
    if ($section == "display" && boomAllow(100)) {
        if (isset($_POST["set_login_page"]) && isset($_POST["set_main_theme"])) {
            $login_page = escape($_POST["set_login_page"]);
            $theme = escape($_POST["set_main_theme"]);
    
            // Check if the login page file exists
            if (file_exists(BOOM_PATH . "/control/login/" . $login_page . "/login.php")) {
                // Prepare data for update
                $data_query = array(
                    "login_page" => $login_page,
                    "default_theme" => $theme,
                );
    
                // Update settings using the update function
                $update = fu_update_dashboard($data_query);
    
                // Check the result of the update
                if ($update === true) {
                    // Return 2 if the theme has changed
                    if ($theme != $data["default_theme"]) {
                        return 1;
                    }
                    return 1; // Successful update
                }
                return 99; // Update failure
            }
            return 99; // File does not exist
        }
        return 99; // Missing parameters
    }

    if ($section == "data_setting" && boomAllow(90)) {
    if (isset($_POST["set_max_avatar"]) && isset($_POST["set_max_cover"]) && isset($_POST["set_max_file"])) {
        $max_avatar = escape($_POST["set_max_avatar"]);
        $max_cover = escape($_POST["set_max_cover"]);
        $max_file = escape($_POST["set_max_file"]);

        // Prepare data for update
        $data_query = array(
            "max_avatar" => $max_avatar,
            "max_cover" => $max_cover,
            "file_weight" => $max_file,
        );

        // Update settings using the update function
        $update = fu_update_dashboard($data_query);

        // Check the result of the update
        if ($update === true) {
            return 1; // Successful update
        }
    }
    return 99; // Missing parameter or update failure
}

    if ($section == "player" && boomAllow(90)) {
        if (isset($_POST["set_default_player"])) {
            $default_player = escape($_POST["set_default_player"]);
            
            // Prepare data for update
            $data_query = array(
                "player_id" => $default_player,
            );
    
            // Update settings using the update function
            $update = fu_update_dashboard($data_query);
    
            // Check the result of the update and compare values
            if ($update === true) {
                if ($default_player == 0 || $default_player != $data["player_id"]) {
                    return 2; // Specific condition for player ID mismatch
                }
                return 1; // Successful update
            }
        }
        return 99; // Missing parameter or update failure
    }

    if ($section == "registration" && boomAllow(90)) {
        if (isset($_POST["set_activation"]) && isset($_POST["set_registration"]) && isset($_POST["set_regmute"]) && isset($_POST["set_max_username"]) && isset($_POST["set_min_age"])) {
            $registration = escape($_POST["set_registration"]);
            $regmute = escape($_POST["set_regmute"]);
            $activation = escape($_POST["set_activation"]);
            $max_name = escape($_POST["set_max_username"]);
            $min_age = escape($_POST["set_min_age"]);
            $max_reg = escape($_POST["set_max_reg"]);
            if ($activation == 0) {
                $mysqli->query("UPDATE boom_users SET user_verify = 0 WHERE user_id > 0");
            }
         $data_query = array(
    			"registration" => $registration,
    			"regmute" => $regmute,
    			"activation" => $activation,
    			"max_username" 		=> $max_name,
    			"min_age" => $min_age,
    		    "max_reg" => $max_reg,
			);
			$update = fu_update_dashboard($data_query);
            if($update ===true){
				return 1;
			}
        }
        return 99;
    }
    if ($section == "guest" && boomAllow(90)) {
        if (isset($_POST["set_allow_guest"]) && isset($_POST["set_guest_form"]) && isset($_POST["set_guest_talk"])) {
            $allow_guest = escape($_POST["set_allow_guest"]);
            $guest_form = escape($_POST["set_guest_form"]);
            $guest_talk = escape($_POST["set_guest_talk"]);
             $guest_per_day = escape($_POST["set_max_greg"]);
            if ($allow_guest == 0 && $allow_guest != $data["allow_guest"]) {
                cleanList("guest");
            }
            //$mysqli->query("UPDATE boom_setting SET allow_guest = '" . $allow_guest . "', guest_form = '" . $guest_form . "', guest_talk = '" . $guest_talk . "' WHERE id = '1'");
          $data_query = array(
    			"allow_guest" => $allow_guest,
    			"guest_form" => $guest_form,
    			"guest_talk" => $guest_talk,
    			"guest_per_day" => $guest_per_day,
			);
			$update = fu_update_dashboard($data_query);
            if($update ===true){
				return 1;
			}
           
           // return 1;
        }
        return 99;
    }
    if ($section == "bridge_registration" && boomAllow(90)) {
        if (isset($_POST["set_use_bridge"])) {
            $use_bridge = escape($_POST["set_use_bridge"]);
            
            // Check if the bridge is enabled and the file does not exist
            if (0 < $use_bridge && !file_exists(BOOM_PATH . "/../boom_bridge.php")) {
                return 404;
            }
    
            // Prepare data for update
            $data_query = array(
                "use_bridge" => $use_bridge,
            );
    
            // Update settings using the update function
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
        }
        return 99;
    }

    if ($section == "social_registration" && boomAllow(90)) {
        if (isset($_POST["set_facebook_login"]) && isset($_POST["set_facebook_id"]) && isset($_POST["set_facebook_secret"]) && isset($_POST["set_twitter_login"]) && isset($_POST["set_twitter_id"]) && isset($_POST["set_twitter_secret"]) && isset($_POST["set_google_login"]) && isset($_POST["set_google_id"]) && isset($_POST["set_google_secret"])) {
            // Escape and prepare data for update
            $data_query = array(
                "facebook_login" => escape($_POST["set_facebook_login"]),
                "facebook_id" => escape($_POST["set_facebook_id"]),
                "facebook_secret" => escape($_POST["set_facebook_secret"]),
                "google_login" => escape($_POST["set_google_login"]),
                "google_id" => escape($_POST["set_google_id"]),
                "google_secret" => escape($_POST["set_google_secret"]),
                "twitter_login" => escape($_POST["set_twitter_login"]),
                "twitter_id" => escape($_POST["set_twitter_id"]),
                "twitter_secret" => escape($_POST["set_twitter_secret"]),
            );
    
            // Update settings using the update function
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
        }
        return 99;
    }

    if ($section == "limitation" && boomAllow(90)) {
        if (isset($_POST["set_allow_cupload"]) && isset($_POST["set_allow_pupload"]) && isset($_POST["set_allow_wupload"]) && isset($_POST["set_allow_cover"]) && isset($_POST["set_allow_gcover"]) && isset($_POST["set_emo_plus"]) && isset($_POST["set_allow_direct"]) && isset($_POST["set_allow_room"]) && isset($_POST["set_allow_theme"]) && isset($_POST["set_allow_history"]) && isset($_POST["set_allow_colors"]) && isset($_POST["set_allow_name_color"]) && isset($_POST["set_allow_name_neon"]) && isset($_POST["set_allow_name_font"]) && isset($_POST["set_allow_verify"]) && isset($_POST["set_allow_name"]) && isset($_POST["set_allow_avatar"]) && isset($_POST["set_allow_mood"]) && isset($_POST["set_allow_grad"]) && isset($_POST["set_allow_neon"]) && isset($_POST["set_allow_font"]) && isset($_POST["set_allow_name_grad"])) {
            // Escape and prepare data for update
            $data_query = array(
                "allow_main" => escape($_POST["set_allow_main"]),
                "allow_private" => escape($_POST["set_allow_private"]),
                "allow_pquote" => escape($_POST["set_allow_pquote"]),
                "allow_quote" => escape($_POST["set_allow_quote"]),
                "allow_avatar" => escape($_POST["set_allow_avatar"]),
                "allow_cover" => escape($_POST["set_allow_cover"]),
                "allow_gcover" => escape($_POST["set_allow_gcover"]),
                "allow_cupload" => escape($_POST["set_allow_cupload"]),
                "allow_pupload" => escape($_POST["set_allow_pupload"]),
                "allow_wupload" => escape($_POST["set_allow_wupload"]),
                "emo_plus" => escape($_POST["set_emo_plus"]),
                "allow_direct" => escape($_POST["set_allow_direct"]),
                "allow_room" => escape($_POST["set_allow_room"]),
                "allow_theme" => escape($_POST["set_allow_theme"]),
                "allow_history" => escape($_POST["set_allow_history"]),
                "allow_verify" => escape($_POST["set_allow_verify"]),
                "allow_name" => escape($_POST["set_allow_name"]),
                "allow_mood" => escape($_POST["set_allow_mood"]),
                "allow_colors" => escape($_POST["set_allow_colors"]),
                "allow_grad" => escape($_POST["set_allow_grad"]),
                "allow_neon" => escape($_POST["set_allow_neon"]),
                "allow_font" => escape($_POST["set_allow_font"]),
                "allow_name_color" => escape($_POST["set_allow_name_color"]),
                "allow_name_grad" => escape($_POST["set_allow_name_grad"]),
                "allow_name_neon" => escape($_POST["set_allow_name_neon"]),
                "allow_name_font" => escape($_POST["set_allow_name_font"]),
                "can_gift" => escape($_POST["set_allow_gift"]),
                "can_frame" => escape($_POST["set_allow_frame"]),
            );
    
            // Update settings using the update function
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
        }
        return 99;
    }
    if ($section == "staff_limitation" && boomAllow(100)) {
         if (isset($_POST["set_can_ghost"])){
             // Escape and prepare data for update
            $data_query = array(
                "can_mute" => escape($_POST["set_can_mute"]),
                "can_ghost" => escape($_POST["set_can_ghost"]),
                "can_vghost" => escape($_POST["set_can_vghost"]),
                "can_kick" => escape($_POST["set_can_kick"]),
                "can_ban" => escape($_POST["set_can_ban"]),
                "can_delete" => escape($_POST["set_can_delete"]),
                "can_rank" => escape($_POST["set_can_rank"]),
                "can_raction" => escape($_POST["set_can_raction"]),
                "can_modavat" => escape($_POST["set_can_modavat"]),
                "can_modcover" => escape($_POST["set_can_modcover"]),
                "can_modmood" => escape($_POST["set_can_modmood"]),
                "can_modabout" => escape($_POST["set_can_modabout"]),
                "can_modcolor" => escape($_POST["set_can_modcolor"]),
                "can_modname" => escape($_POST["set_can_modname"]),
                "can_modemail" => escape($_POST["set_can_modemail"]),
                "can_modpass" => escape($_POST["set_can_modpass"]),
                "can_modvpn" => escape($_POST["set_can_modvpn"]),
                "can_flood" => escape($_POST["set_can_flood"]),
                "can_warn" => escape($_POST["set_can_warn"]),
            );
                
            // Update settings using the update function
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
             
         }
          return 99;
    }
    if ($section == "email_settings" && boomAllow(100)) {
        if (isset($_POST["set_mail_type"]) && isset($_POST["set_site_email"]) && isset($_POST["set_email_from"]) && isset($_POST["set_smtp_host"]) && isset($_POST["set_smtp_username"]) && isset($_POST["set_smtp_password"]) && isset($_POST["set_smtp_port"]) && isset($_POST["set_smtp_type"])) {
            $mail_type = escape($_POST["set_mail_type"]);
            $site_email = escape($_POST["set_site_email"]);
            $email_from = escape($_POST["set_email_from"]);
            $smtp_host = escape($_POST["set_smtp_host"]);
            $smtp_username = escape($_POST["set_smtp_username"]);
            $smtp_password = escape($_POST["set_smtp_password"]);
            $smtp_port = escape($_POST["set_smtp_port"]);
            $smtp_type = escape($_POST["set_smtp_type"]);
    
            // Prepare data for update
            $data_query = array(
                "mail_type" => $mail_type,
                "site_email" => $site_email,
                "email_from" => $email_from,
                "smtp_host" => $smtp_host,
                "smtp_username" => $smtp_username,
                "smtp_password" => $smtp_password,
                "smtp_port" => $smtp_port,
                "smtp_type" => $smtp_type,
            );
    
            // Update settings using the update function
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
        }
        return 99;
    }

    if ($section == "chat" && boomAllow(90)) {
        if (isset($_POST["set_gender_ico"]) && isset($_POST["set_flag_ico"]) && isset($_POST["set_max_main"]) && isset($_POST["set_max_private"]) && isset($_POST["set_speed"]) && isset($_POST["set_max_offcount"]) && isset($_POST["set_allow_logs"])) {
            $gender_ico = escape($_POST["set_gender_ico"]);
            $flag_ico = escape($_POST["set_flag_ico"]);
            $max_main = escape($_POST["set_max_main"]);
            $max_private = escape($_POST["set_max_private"]);
            $max_offcount = escape($_POST["set_max_offcount"]);
            $speed = escape($_POST["set_speed"]);
            $allow_logs = escape($_POST["set_allow_logs"]);
    
            // Prepare data for update
            $data_query = array(
                "gender_ico" => $gender_ico,
                "flag_ico" => $flag_ico,
                "max_main" => $max_main,
                "max_private" => $max_private,
                "speed" => $speed,
                "max_offcount" => $max_offcount,
                "allow_logs" => $allow_logs,
            );
    
            // Update settings using the update function
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
        }
        return 99;
    }

    if ($section == "delays" && boomAllow(90)) {
        if (isset($_POST["set_chat_delete"]) && isset($_POST["set_private_delete"]) && isset($_POST["set_wall_delete"]) && isset($_POST["set_member_delete"]) && isset($_POST["set_room_delete"]) && isset($_POST["set_act_delay"])) {
            $act_delay = escape($_POST["set_act_delay"]);
            $chat = escape($_POST["set_chat_delete"]);
            $private = escape($_POST["set_private_delete"]);
            $wall = escape($_POST["set_wall_delete"]);
            $member = escape($_POST["set_member_delete"]);
            $room = escape($_POST["set_room_delete"]);
    
            // Prepare data for update
            $data_query = array(
                "act_delay" => $act_delay,
                "chat_delete" => $chat,
                "private_delete" => $private,
                "wall_delete" => $wall,
                "last_clean" => '0', // Set to 0 as per the original logic
                "member_delete" => $member,
                "room_delete" => $room,
            );
    
            // Update settings using the update function
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
        }
        return 99;
    }

    if ($section == "modules" && boomAllow(90)) {
        if (isset($_POST["set_use_wall"]) && isset($_POST["set_use_lobby"]) && isset($_POST["set_cookie_law"])) {
            $use_like = escape($_POST["set_use_like"]);
            $use_lobby = escape($_POST["set_use_lobby"]);
            $use_wall = escape($_POST["set_use_wall"]);
            $cookie_law = escape($_POST["set_cookie_law"]);
            $use_geo = escape($_POST["set_use_geo"]);
            // Perform deletions if needed
            if ($use_wall == 0) {
                $mysqli->query("DELETE FROM boom_notification WHERE notify_source = 'post'");
                $mysqli->query("DELETE FROM boom_report WHERE report_type = '2'");
            }
    
            // Prepare data for update
            $data_query = array(
                "use_geo" => $use_geo,
                "use_like" => $use_like,
                "use_lobby" => $use_lobby,
                "use_wall" => $use_wall,
                "cookie_law" => $cookie_law,
            );
    
            // Update settings using the update function
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
        }
        return 99;
    }


    // onesignal settings

     if ($section == "setting_notifications" && boomAllow(100)) {
        if (isset($_POST["onesignal_web_push_id"]) && isset($_POST["onesignal_web_reset_key"]) && isset($_POST["allow_onesignal"])) {
            $onesignal_web_push_id = escape($_POST["onesignal_web_push_id"]);
            $onesignal_web_reset_key = escape($_POST["onesignal_web_reset_key"]);
            $allow_onesignal = escape($_POST["allow_onesignal"]);
    
            $data_query = array(
                "onesignal_web_push_id" => $onesignal_web_push_id,
                "onesignal_web_reset_key" => $onesignal_web_reset_key,
                "allow_onesignal" => $allow_onesignal,
            );
    
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
        }
        return 99;
    }
      // gold settings

     if ($section == "admin_gold" && boomAllow(100)) {
        if (isset($_POST["set_use_gold"]) && isset($_POST["set_can_sgold"]) && isset($_POST["set_can_rgold"])) {
            $use_gold = escape($_POST["set_use_gold"]);
            $can_sgold = escape($_POST["set_can_sgold"]);
            $can_rgold = escape($_POST["set_can_rgold"]);
            $allow_gold = escape($_POST["set_allow_gold"]);
            $gold_delay = escape($_POST["set_gold_delay"]);
            $gold_base = escape($_POST["set_gold_base"]);
            $can_vgold = escape($_POST["set_can_vgold"]);
            $data_query = array(
                "use_gold" => $use_gold,
                "can_sgold" => $can_sgold,
                "can_rgold" => $can_rgold,
                "use_gold" => $can_rgold,
                "gold_delay" => $gold_delay,
                "gold_base" => $gold_base,
                "can_vgold" => $can_vgold,
                "allow_gold" => $allow_gold,
            );
    
            $update = fu_update_dashboard($data_query);
    
            if ($update === true) {
                return 1;
            }
        }
        return 99;
    }
       // gold settings

        if ($section === "security" && boomAllow(100)) {
        // Check if required POST variables are set
        if (
            isset($_POST["set_use_recapt"], $_POST["set_recapt_key"], $_POST["set_recapt_secret"],
                  $_POST["set_flood_action"], $_POST["set_max_flood"], $_POST["set_flood_delay"],
                  $_POST["set_vpn_key"], $_POST["set_use_vpn"], $_POST["set_vpn_delay"])
        ) {
            // Sanitize input data
            $data_query = array(
                "use_recapt"    => escape($_POST["set_use_recapt"]),
                "recapt_key"    => escape($_POST["set_recapt_key"]),
                "recapt_secret" => escape($_POST["set_recapt_secret"]),
                "flood_action"  => escape($_POST["set_flood_action"]),
                "max_flood"     => escape($_POST["set_max_flood"]),
                "flood_delay"   => escape($_POST["set_flood_delay"]),
                "vpn_key"       => escape($_POST["set_vpn_key"]),
                "use_vpn"       => escape($_POST["set_use_vpn"]),
                "vpn_delay"     => escape($_POST["set_vpn_delay"]),
            );
    
            // Update settings in the dashboard
            $update = fu_update_dashboard($data_query);
    
            // Return success or error code
            return $update === true ? 1 : 99;
        } else {
            // Missing required POST parameters
            return 99;
        }
    }
   if ($section == "gateway_mods" && boomAllow(100)) {
         if (isset($_POST["gateway_mods"])) {
            // Process PayPal settings if provided
            if (isset($_POST["allow_paypal"]) && 
                isset($_POST["paypal_mode"]) && 
                isset($_POST["paypalTestingClientKey"]) &&
                isset($_POST["paypalTestingSecretKey"]) &&
                isset($_POST["paypalLiveClientKey"]) &&
                isset($_POST["paypalLiveSecretKey"])) {
        
                $data_query = array(
                    "allow_paypal" => escape($_POST["allow_paypal"]),
                    "paypal_mode" => escape($_POST["paypal_mode"]),
                    "paypalTestingClientKey" => escape($_POST["paypalTestingClientKey"]),
                    "paypalTestingSecretKey" => escape($_POST["paypalTestingSecretKey"]),
                    "paypalLiveClientKey" => escape($_POST["paypalLiveClientKey"]),
                    "paypalLiveSecretKey" => escape($_POST["paypalLiveSecretKey"]),
                    "use_wallet" => escape($_POST["use_wallet"]),
                    "point_cost" => escape($_POST["dollar_to_point_cost"]),
                    "currency" => escape($_POST["currency"]),
                );
        
                $update = fu_update_dashboard($data_query);
        
                if ($update === true) {
                    return 1;
                } else {
                    return 0; // Handle update failure
                }
            }
        } 
    }

  if ($section === "websocket" && boomAllow(100)) {
       if (isset($_POST["set_websocket_path"]) && isset($_POST["set_websocket_port"]) && isset($_POST["set_websocket_mode"])) {
            $data_query = array(
                "websocket_path"    => escape($_POST["set_websocket_path"]),
                "websocket_port"    => escape($_POST["set_websocket_port"]),
                "websocket_mode" => escape($_POST["set_websocket_mode"]),
                 "websocket_protocol" => escape($_POST["set_websocket_protocol"]),
                  "istyping_mode" => escape($_POST["set_istyping_mode"]),
            );
             // Update settings in the dashboard
            $update = fu_update_dashboard($data_query);
                 // Return success or error code
            return $update === true ? 1 : 99;
        } else {
            // Missing required POST parameters
            return 99;
        }
  }
  if ($section === "store_control" && boomAllow(100)) {
       if (isset($_POST["set_use_store"]) && isset($_POST["set_use_frame"])) {
            $data_query = array(
				"use_store"  => escape($_POST["set_use_store"]),
                "use_frame"  => escape($_POST["set_use_frame"]),
                "use_wings"  => escape($_POST["set_use_wings"]),
            );
             // Update settings in the dashboard
            $update = fu_update_dashboard($data_query);
                 // Return success or error code
            return $update === true ? 1 : 99;
        } else {
            // Missing required POST parameters
            return 99;
        }
  }
    if ($section === "xp_system" && boomAllow(100)) {
       if (isset($_POST["set_use_level"]) && isset($_POST["set_exp_gift"])) {
            $data_query = array(
				"use_level"  => escape($_POST["set_use_level"]),
				"exp_gift"  => escape($_POST["set_exp_gift"]),
				"exp_post"  => escape($_POST["set_exp_post"]),
				"exp_priv"  => escape($_POST["set_exp_priv"]),
				"exp_chat"  => escape($_POST["set_exp_chat"]),
            );
             // Update settings in the dashboard
            $update = fu_update_dashboard($data_query);
                 // Return success or error code
            return $update === true ? 1 : 99;
        } else {
            // Missing required POST parameters
            return 99;
        }
  }
  if ($section === "gold_reward" && boomAllow(100)) {
       if (isset($_POST["set_allow_sendcoins"]) && isset($_POST["set_allow_takecoins"])) {
            $data_query = array(
				"allow_takecoins"  => escape($_POST["set_allow_takecoins"]),
				"allow_sendcoins"  => escape($_POST["set_allow_sendcoins"]),
            );
             // Update settings in the dashboard
            $update = fu_update_dashboard($data_query);
                 // Return success or error code
            return $update === true ? 1 : 99;
        } else {
            // Missing required POST parameters
            return 99;
        }
  }
}
function get_settings() {
    global $db;
    // Fetch settings where 'setting' equals 1
    $db->where('id', 1);
    $settings = $db->get('setting');
    // Check if any results were returned
    if ($db->count > 0) {
        return $settings;
    }
    // Return an empty array or null if no settings are found
    //return [];
}

function sendDataToSocket() {
    global $data;
    // URL of the Socket.IO server endpoint
   $s_protocol = $data['websocket_protocol'];
   $s_server = $data['websocket_path'];
   $s_port = $data['websocket_port'];	    
    $url = "https://{$s_server}:$s_port/update-config"; // Replace with your actual Socket.IO server URL
    $data_query = get_settings();
    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-auth-token: lucifer666' // Replace with your actual token
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_query));

    // Execute the request and get the response
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close cURL session
    curl_close($ch);

    // Optionally, handle different HTTP response codes
    if ($httpCode === 200) {
        //return $response;
    } elseif ($httpCode === 403) {
       // return 'Forbidden';
    } else {
       // return 'Error: ' . $response;
    }
}
function boomPageContent($content, $target){
    global $mysqli;
    // Check user permission
    if (!boomAllow(90)) {
        return "";
    }
    // Ensure content is not empty
    if (empty($content)) {
        $content = "";
    }
    // Sanitize the target
    $target = escape($_POST["page_target"]); // Assuming you have escape function to sanitize input
    // Prepare and execute the SELECT query securely using a prepared statement
    $stmt = $mysqli->prepare("SELECT * FROM boom_page WHERE page_name = ?");
    $stmt->bind_param("s", $target); // Bind parameter as a string
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Page exists, update the content
        $stmt_update = $mysqli->prepare("UPDATE boom_page SET page_content = ? WHERE page_name = ?");
        $stmt_update->bind_param("ss", $content, $target); // Bind both parameters as strings
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Page doesn't exist, insert new page
        $stmt_insert = $mysqli->prepare("INSERT INTO boom_page (page_name, page_content) VALUES (?, ?)");
        $stmt_insert->bind_param("ss", $target, $content); // Bind both parameters as strings
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt->close(); // Close the select statement
    return 1;
}


?>