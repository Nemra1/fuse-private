<?php


require __DIR__ . "./../config_session.php";
if (isset($_POST["run_console"])) {
    $console = escape($_POST["run_console"]);
    echo boomrunconsole($console);
} else {
    echo 0;
    exit;
}

function boomRunConsole($console)
{
    global $mysqli;
    global $data;
    global $cody;
    global $lang;

            $command = explode(" ", trim($console));
            if ($command[0] == "/removetheme" && boomAllow(100)) {
                $theme = trimCommand($console, "/removetheme");
                if ($theme == $data["default_theme"]) {
                    return 3;
                }
                $mysqli->query("UPDATE boom_users SET user_theme = 'system' WHERE user_theme = '" . $theme . "'");
                return 1;
            }
            if ($command[0] == "/removelanguage" && boomAllow(100)) {
                $language = trimCommand($console, "/removelanguage");
                if ($language == $data["language"]) {
                    return 3;
                }
                $mysqli->query("UPDATE boom_users SET user_language = '" . $data["language"] . "' WHERE user_language = '" . $language . "'");
                return 1;
            }
            if ($command[0] == "/clearwall" && boomAllow(100)) {
                $mysqli->query("TRUNCATE TABLE boom_post");
                $mysqli->query("TRUNCATE TABLE boom_post_reply");
                $mysqli->query("TRUNCATE TABLE boom_post_like");
                $mysqli->query("DELETE FROM boom_notification WHERE notify_source = 'post'");
                return 1;
            }
            if ($command[0] == "/clearprivate" && boomAllow(100)) {
                $mysqli->query("DELETE FROM boom_private WHERE id > 0");
                return 1;
            }
            if ($command[0] == "/clearnotification" && boomAllow(100)) {
                $mysqli->query("TRUNCATE TABLE boom_notification");
                return 1;
            }
            if ($command[0] == "/resetgeo" && boomAllow(100)) {
                $mysqli->query("UPDATE boom_users SET country = '' WHERE user_id > 0");
                $mysqli->query("UPDATE boom_users SET country = 'ZZ' WHERE user_bot > 0");
                return 1;
            }
            if ($command[0] == "/resetcover" && boomAllow(100)) {
                $mysqli->query("UPDATE boom_users SET user_cover = '' WHERE user_id > 0");
                return 1;
            }
            if ($command[0] == "/clearchat" && boomAllow(90)) {
                $mysqli->query("DELETE FROM boom_chat WHERE post_id > 0");
                return 1;
            }
            if ($command[0] == "/clearreport" && boomAllow(90)) {
                $mysqli->query("TRUNCATE TABLE boom_report");
                return 1;
            }
            if ($command[0] == "/clearmail" && boomAllow(100)) {
                $mysqli->query("TRUNCATE TABLE boom_mail");
                return 1;
            }
            if ($command[0] == "/resetsystembot" && boomAllow(100)) {
                if (isset($data["system_id"])) {
                    $user = userDetails($data["system_id"]);
                } else {
                    $user = userDetails(0);
                }
                clearUserData($user);
                sleep(1);
                $mysqli->query("INSERT INTO `boom_users` (user_name, user_email, user_ip, user_join, user_language, user_password, user_rank, user_tumb, verified, user_bot) VALUES('System', '', '0.0.0.0', '" . time() . "', 'English', '" . randomPass() . "', '69', 'default_system.png', '1', '69')");
                $last_id = $mysqli->insert_id;
                $mysqli->query("UPDATE boom_setting SET system_id = '" . $last_id . "'");
                return 1;
            }
            if ($command[0] == "/clearnews" && boomAllow(100)) {
                $mysqli->query("TRUNCATE TABLE boom_news");
                $mysqli->query("TRUNCATE TABLE boom_news_reply");
                $mysqli->query("TRUNCATE TABLE boom_news_like");
                updateAllNotify();
                return 1;
            }
            if ($command[0] == "/resetkeys" && boomAllow(100)) {
                $mysqli->query("UPDATE boom_setting SET dat = '' WHERE id > 0");
                $mysqli->query("UPDATE boom_addons SET addons_key = '' WHERE addons_id > 0");
                return 6;
            }
            if ($command[0] == "/clearcache" && boomAllow(100)) {
                boomCacheUpdate();
                return 1;
            }
            if ($command[0] == "/makefullowner" && boomAllow(100)) {
                $t = trimCommand($console, "/makefullowner");
                $target = nameDetails($t);
                if (empty($target)) {
                    return 4;
                }
                if (!mySelf($target["user_id"]) && !isOwner($target)) {
                    $mysqli->query("UPDATE boom_users SET user_rank = 100 WHERE user_name = '" . $target["user_name"] . "'");
                    return 1;
                }
                return 5;
            }
            if ($command[0] == "/resetpassword" && boomAllow(100)) {
                $t = trimCommand($console, "/resetpassword");
                $new_pass = encrypt($t);
                $mysqli->query("UPDATE boom_users SET user_password = '" . $new_pass . "' WHERE user_id = '" . $data["user_id"] . "'");
                setBoomCookie($data["user_id"], $new_pass);
                return 1;
            }
            if ($command[0] == "/fixmain" && boomAllow(100)) {
                $check_main = $mysqli->query("SELECT * FROM boom_rooms WHERE room_id = 1");
                if ($check_main->num_rows < 1) {
                    $mysqli->query("INSERT INTO boom_rooms ( room_id, room_name, access, room_system, room_action, room_creator ) VALUES (1, 'Main room', 0, 1, '" . time() . "', '" . $data["user_id"] . "')");
                }
                return 1;
            }
            if ($command[0] == "/resetchat" && boomAllow(100)) {
                $mysqli->query("TRUNCATE TABLE boom_chat");
                $mysqli->query("TRUNCATE TABLE boom_private");
                return 1;
            }
			if ($command[0] == "/makevisible" && boomAllow(100)) {
				// Use a prepared statement to prevent SQL injection
				$stmt = $mysqli->prepare("UPDATE boom_users SET user_status = 1 WHERE user_status = 99 AND user_id != ? AND user_rank < 100");
				$stmt->bind_param("i", $data["user_id"]);
				$stmt->execute();
				$stmt->close();
				return 1;
			}

            if ($command[0] == "/resetsystem" && boomAllow(100)) {
                $mysqli->query("TRUNCATE TABLE boom_chat");
                $mysqli->query("TRUNCATE TABLE boom_private");
                $mysqli->query("TRUNCATE TABLE boom_notification");
                $mysqli->query("TRUNCATE TABLE boom_post");
                $mysqli->query("TRUNCATE TABLE boom_post_reply");
                $mysqli->query("TRUNCATE TABLE boom_post_like");
                return 1;
            }
            if ($command[0] == "/banip" && boomAllow(90)) {
                $ip = $command[1];
                if (!filter_var($ip, FILTER_VALIDATE_IP) === false || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
                    $mysqli->query("INSERT INTO boom_banned (ip) VALUES ('" . $ip . "')");
                    return 1;
                }
                return 2;
            }
            if ($command[0] == "/resetterms" && boomAllow(100)) {
                require BOOM_PATH . "/system/template/data_template.php";
                $mysqli->query("UPDATE `boom_page` SET `page_content` = '" . $term_content . "' WHERE page_name = 'terms_of_use'");
                return 1;
            }
            if ($command[0] == "/resetprivacy" && boomAllow(100)) {
                require BOOM_PATH . "/template/data_template.php";
                $mysqli->query("UPDATE `boom_page` SET `page_content` = '" . $privacy_content . "' WHERE page_name = 'privacy_policy'");
                return 1;
            }
            if ($command[0] == "/resethelp" && boomAllow(100)) {
                require BOOM_PATH . "/template/data_template.php";
                $mysqli->query("UPDATE `boom_page` SET `page_content` = '" . $help_content . "' WHERE page_name = 'help'");
                return 1;
            }
            if ($command[0] == "/resetemailfilter" && boomAllow(100)) {
                $mysqli->query("DELETE FROM boom_filter WHERE word_type = 'email'");
                $mysqli->query("INSERT INTO boom_filter (word, word_type) VALUES\r\n\t\t('aol','email'),('att','email'),('comcast','email'),('facebook','email'),('gmail','email'),('gmx','email'),('googlemail','email'),('google','email'),('hotmail','email'),('mac','email'),('me','email'),('mail','email'),('msn','email'),('live','email'),('sbcglobal','email'),\r\n\t\t('verizon','email'),('yahoo','email'),('email','email'),('fastmail','email'),('games','email'),('hush','email'),('hushmail','email'),('icloud','email'),('iname','email'),('inbox','email'),('lavabit','email'),('love','email'),('outlook','email'),('pobox','email'),\r\n\t\t('protonmail','email'),('rocketmail','email'),('safe-mail','email'),('wow','email'),('ygm','email'),('ymail','email'),('zoho','email'),('yandex','email'),('bellsouth','email'),('charter','email'),('cox','email'),('earthlink','email'),('juno','email'),\r\n\t\t('btinternet','email'),('virginmedia','email'),('blueyonder','email'),('freeserve','email'),('ntlworld','email'),('o2','email'),('orange','email'),('sky','email'),('talktalk','email'),('tiscali','email'),('virgin','email'),('wanadoo','email'),\r\n\t\t('bt','email'),('sina','email'),('qq','email'),('naver','email'),('hanmail','email'),('daum','email'),('nate','email'),('laposte','email'),('gmx','email'),('sfr','email'),('neuf','email'),('free','email'),('online','email'),('t-online','email'),('web','email'),\r\n\t\t('libero','email'),('virgilio','email'),('alice','email'),('tin','email'),('poste','email'),('teletu','email'),('mail','email'),('rambler','email'),('ya','email'),('list','email'),('skynet','email'),('voo','email'),('tvcablenet','email'),('telenet','email'),\r\n\t\t('fibertel','email'),('speedy','email'),('arnet','email'),('prodigy.mx','email'),('uol','email'),('bol','email'),('terra','email'),('ig','email'),('itelefonica','email'),('r7','email'),('zipmail','email'),('globo','email'),('globomail','email'),('oi','email')\r\n\t\t");
                return 1;
            }
            if ($command[0] == "/stylereset" && boomAllow(100)) {
                $mysqli->query("UPDATE boom_users SET user_color = 'user' WHERE user_rank < '" . $data["allow_name_color"] . "' AND user_bot = 0");
                $mysqli->query("UPDATE boom_users SET user_color = 'user' WHERE user_rank < '" . $data["allow_name_grad"] . "' AND user_color LIKE '%bgrad%' AND user_bot = 0");
                $mysqli->query("UPDATE boom_users SET user_color = 'user' WHERE user_rank < '" . $data["allow_name_neon"] . "' AND user_color LIKE '%bneon%' AND user_bot = 0");
                $mysqli->query("UPDATE boom_users SET bccolor = '', bcbold = '' WHERE user_rank < '" . $data["allow_colors"] . "' AND user_bot = 0");
                $mysqli->query("UPDATE boom_users SET bccolor = '' WHERE user_rank < '" . $data["allow_grad"] . "' AND bccolor LIKE '%bgrad%' AND user_bot = 0");
                $mysqli->query("UPDATE boom_users SET bccolor = '' WHERE user_rank < '" . $data["allow_neon"] . "' AND bccolor LIKE '%bneon%' AND user_bot = 0");
                $mysqli->query("UPDATE boom_users SET user_font = '' WHERE user_rank < '" . $data["allow_name_font"] . "' AND user_bot = 0");
                $mysqli->query("UPDATE boom_users SET bcfont = '' WHERE user_rank < '" . $data["allow_font"] . "' AND user_bot = 0");
                return 1;
            }
            if ($command[0] == "/fontreset" && boomAllow(100)) {
                $mysqli->query("UPDATE boom_users SET user_font = '', bcfont = '' WHERE user_id > 0");
                return 1;
            }
            if ($command[0] == "/moodreset" && boomAllow(100)) {
                $mysqli->query("UPDATE boom_users SET user_mood = '' WHERE user_rank < '" . $data["allow_mood"] . "'");
                return 1;
            }
            if ($command[0] == "/themereset" && boomAllow(100)) {
                $mysqli->query("UPDATE boom_users SET user_theme = 'system' WHERE user_rank < '" . $data["allow_theme"] . "'");
                return 1;
            }
            return 0;
}

?>