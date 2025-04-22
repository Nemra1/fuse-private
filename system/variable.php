<?php
$cody['system_id'] = '2';
$cody['max_reg'] = 5; 				// max registration per day per ip // done in admin panel
$cody['max_room_name'] = 30; 		// max lenght of room name
$cody['max_description'] = 150; 	// max lenght of room description
$cody['act_time'] = 1;				// turn on off the innactivity balancer (0)off (1)on
$cody['max_room'] = 1;				// maximum room that a single user can create
$cody['reg_filter'] = 1;			// turn on off the ip registration filter (0)off (1)on
$cody['strict_guest'] = 1;			// strict guest registration mode follow system settings
$cody['max_verify'] = 3;			// maximum verification email allowed per 24 hours per user
$cody['max_report'] = 3;			// maximum active report allowed per users.
$cody['guest_per_day'] = 20;		// maximum guest account per day with same ip // done in admin panel
$cody['guest_delay'] = 30;			// delay for wich a guest account cannot be overwrited in minutes
$cody['flood_delay'] = 15;			// minutes of mute applyed when a flood is detected
$cody['flood_limit'] = 6;			// post required within 10 sec to trigger flood protection
$cody['strip_direct'] = 0;			// set to 1 to activate direct display hard mode
$cody['default_mute'] = 5;			// default mute delay in mute box
$cody['ignore_clean'] = 30;			// ignore expire automaticly after x days 0 for never
$cody['use_geo'] = 1;				// set to 0 to disable the auto geolocalisation // add in admin panel (done)
$cody['default_kick'] = 5;			// default kick delay in kick box
$cody['rbreak'] = 900;				// right chat panel mobile breakpoint in pixel
$cody['lbreak'] = 1260;				// left chat panel mobile breakpoint in pixel
$cody['right_size'] = 335;			// default right panel size in pixel
$cody['left_size'] = 290;			// default left panel size in pixel
$cody['report_history'] = 100;		// max log history private report will show
$cody['card_cover'] = 1;			// display card cover set 0 to disable or 1 to enable

/* permission settings */

$cody['can_flood'] = 70;				// rank that is not affected by the mute protection.
$cody['can_word_filter'] = 90;		// rank required to not be affected by word filter
$cody['word_action'] = 2;		// when system dedcted spam word
$cody['can_post_news'] = 100;		// rank required to post news
$cody['can_delete_news'] = 100;		// rank required to delete news post
$cody['can_reply_news'] = 1;		// rank required to reply to news
$cody['can_delete_wall'] = 70;		// rank required to delete wall post
$cody['can_delete_logs'] = 70;		// rank required to delete chat post
$cody['can_delete_slogs'] = 1;		// rank required to delete self posted chat log
$cody['can_invisible'] = 80;			// rank required to have invisibility option
$cody['can_inv_view'] = 100;			// rank required to view invisible in admin panel
$cody['can_modavat'] = 70;		// rank required to modify users avatar
$cody['can_modcover'] = 70;		// rank required to modify users cover
$cody['can_modname'] = 80;		// rank required to modify users username
$cody['can_modmood'] = 70;		// rank required to modify users mood
$cody['can_modabout'] = 70;		// rank required to modify users about me
$cody['can_modemail'] = 90;		// rank required to modify users email
$cody['can_modcolor'] = 90;		// rank required to modify users color
$cody['can_modpass'] = 90;	// rank required to modify users password
$cody['can_view_history'] = 70;		// rank required to view users action history
$cody['can_view_console'] = 90;		// rank required to access console in admin panel
$cody['can_clear_console'] = 100;	// rank required to clear the admin console log
$cody['can_view_email'] = 90;		// rank required to view users email
$cody['can_view_timezone'] = 10;	// rank required to view users timezone
$cody['can_view_id'] = 90;			// rank required to view users id
$cody['can_view_ip'] = 90;			// rank required to view users ip
$cody['can_room_pass'] = 70;			// rank required to enter room without pass
$cody['can_rank'] = 90;				// rank required to change rank of members do not go bellow 100, 10 or 9
$cody['can_ban'] = 80;				// rank required to have ban power
$cody['can_kick'] = 70;				// rank required to have kick power
$cody['can_delete'] = 90;			// rank required to have delete power
$cody['can_report'] = 1;			// rank required to have report ability
$cody['can_maintenance'] = 70;		// rank required to enter chat while in maintenance mode
$cody['can_manage_addons'] = 100;	// rank required to install, config and uninstall addons
$cody['can_edit_info'] = 0;			// rank required to edit general profile information
$cody['can_edit_about'] = 0;		// rank required to edit profile about
$cody['can_manage_report'] = 70;		// rank required to view and manage report
$cody['can_self_report'] = 90;		// rank required to remove a self involved report
$cody['can_manage_history'] = 90;	// rank required to manage profile history
$cody['can_delete_private'] = 1;	// rank required to delete private chat
$cody['can_clear_room'] = 100;		// rank required to have /clear room ability
$cody['can_ghost'] = 100; 			// rank required to make user ghost room ability
$cody['can_vghost'] = 100;           // rank required to view ghost
$cody['use_like'] = 1;              // system profile likes // add in admin panel (done)
$cody['dev_mode'] = 0;              // in case you would to enable development mode and file changes
$cody['secure_header'] = 0;         // enable hard secure mode for site header
$cody['can_raction'] = 100;
/* system log messages */

$cody['join_room'] = 1;				// show log when entering room 0 disabled 1 enabled
$cody['leave_room'] = 1;			// show log when leaving room 0 disabled 1 enabled
$cody['name_change'] = 1;			// show log when change username 0 disabled 1 enabled
$cody['action_log'] = 1;			// show log when an action is taken 0 disabled 1 enabled

/* color count in the system */

$cody['color_count'] = 32;			// number of color used and defined in css
$cody['gradient_count'] = 40;		// number of gradient used and defined in css
$cody['neon_count'] = 32;			// number of gradient used and defined in css
			
/* misc */

$cody['audio_download'] = 0;        // show download button for uploaded audio
$cody['clean_delay'] = 5;			// delay for system cleaning in minutes
$cody['enable_daymode'] = 1;	    	// theme switcher for light and dark mode in reverse mode
/* system gold  */
$cody['can_vgold'] = 1;
$cody['bagold'] = 5000;
$cody['can_gold'] = 100;
$cody['can_rgold'] = 100;
$cody['gold_delay'] =1;
$cody['gold_base'] = 1;
$cody['use_gold'] = 1;
$cody['can_sgold'] = 100;
$cody['allow_private'] = 0;       //started rank allowed to private chat from low to high (0 to 100)
$cody['allow_main'] = 0;          //started rank allowed to Main chat from low to high (0 to 100)  
$_SESSION['csrf_token'] =         generateCsrfToken();
// cookie and session settings

define('BOOM_PREFIX', 'bc_');

// do not edit function below they are very important for the system to work properly

define('BOOM', 1);
define('BOOM_PATH', dirname(__DIR__));

define('BOOM_DHOST', $DB_HOST);
define('BOOM_DNAME', $DB_NAME);
define('BOOM_DUSER', $DB_USER);
define('BOOM_DPASS', $DB_PASS);
define('BOOM_CRYPT', $encryption);
define('CSRF_TOKEN', $_SESSION['csrf_token']);
function unsetBoomCookie(){
	setcookie(BOOM_PREFIX . "userid","",time() - 1000, '/');
	setcookie(BOOM_PREFIX . "utk","",time() - 1000, '/');    
    // Destroy session
    $_SESSION = [];
    session_destroy();
}
function setBoomLang($val){
	setcookie(BOOM_PREFIX . "lang","$val",time()+ 31556926, '/');
}
function setBoomCookieLaw(){
	setcookie(BOOM_PREFIX . "claw","1",time()+ 31556926, '/');
}
/**
 * Generate and store CSRF token
 * Call this once per session or when generating forms
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
// Verify submitted token
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}
function setBoomCookie($user_id, $password_hash) {
    $prefix = defined('BOOM_PREFIX') ? BOOM_PREFIX : 'bc_';
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    // Set User ID Cookie
    setcookie($prefix . "userid", $user_id, [
        'expires' => time() + 31556926, // 1 year expiration
        'path' => '/',                  // Accessible across the entire domain
        'secure' => $is_https,          // Secure cookie (only transmitted over HTTPS)
        'httponly' => true,             // Prevents JavaScript access (prevents XSS)
        'samesite' => 'Strict'             // Prevents CSRF by restricting cross-site requests
    ]);
    // Set Auth Token Cookie (could store a session token or something less sensitive than the actual password)
    setcookie($prefix . "utk", $password_hash, [
        'expires' => time() + 31556926, // 1 year expiration
        'path' => '/',                  // Accessible across the entire domain
        'secure' => $is_https,          // Secure cookie (only transmitted over HTTPS)
        'httponly' => true,             // Prevents JavaScript access (prevents XSS)
        'samesite' => 'Strict'             // Prevents CSRF by restricting cross-site requests
    ]);
}
function validateAuth() {
    // First check session
    if (!empty($_SESSION['user_id'])) {
        return true;
    }
    // Fallback to cookie validation
    $prefix = defined('BOOM_PREFIX') ? BOOM_PREFIX : 'bc_';
    if (empty($_COOKIE[$prefix.'userid']) || empty($_COOKIE[$prefix.'utk'])) {
        return false;
    }
    $user = userDetails($_COOKIE[$prefix.'userid']);
    if (!$user || $user['user_password'] !== $_COOKIE[$prefix.'utk']) {
        return false;
    }
    // Re-establish session
    $_SESSION['user_id'] = $user['user_id'];
    return true;
}

?>