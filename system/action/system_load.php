<?php

require __DIR__ . '/../config_session.php';
session_write_close();
if (!isset($_POST["page"])) {
    exit;
}
$d["pending"] = [];
if(isset($_POST['page'])){
    $page = escape($_POST["page"]);
    boomgeo();
    updateuseraccount();
    $d["pending"] = pendingPush($d["pending"], checkregmute());
    $d['geo'] = boomGeo();
    $d['recheckVpn'] = recheckVpn();
	if(useStore()){
		$d['premiumUserClean'] = premiumUserClean();
		$d['rankUserClean'] = Rank_UserClean();		
	}
    echo json_encode($d, JSON_UNESCAPED_UNICODE);
}
function boomGeo(){
    global $mysqli,$data;
    if (checkGeo()) {
        require BOOM_PATH . "/system/location/country_list.php";
        require BOOM_PATH . "/system/element/timezone.php";
        $ip = getIp();
        $country = "ZZ";
        $tzone = $data["user_timezone"];
        $loc = doCurl("http://www.geoplugin.net/php.gp?ip=" . $ip);
        $res = unserialize($loc);
        if (isset($res["geoplugin_countryCode"]) && array_key_exists($res["geoplugin_countryCode"], $country_list)) {
            $country = escape($res["geoplugin_countryCode"]);
        }
        if (isset($res["geoplugin_timezone"]) && in_array($res["geoplugin_timezone"], $timezone)) {
            $tzone = escape($res["geoplugin_timezone"]);
        }
        $mysqli->query("UPDATE boom_users SET user_ip = '" . $ip . "', country = '" . $country . "', user_timezone = '" . $tzone . "' WHERE user_id = '" . $data["user_id"] . "'");
        //redisUpdateUser($data['user_id']);
        return 1;
    }
    return 0;
}

function checkRegMute(){
    global $data,$page;
    $result = "";
    if (insideChat($page)) {
        if (guestMuted()) {
            $result = modalPending(boomTemplate("element/guest_talk"), "empty", 400);
        } else {
            if (isRegMute($data)) {
                $result = modalPending(boomTemplate("element/regmute"), "empty", 400);
            } else {
                $result = "";
            }
        }
    }
    return $result;
}

function updateUserAccount(){
    global $mysqli,$data,$cody;
    $mob = getMobile();
    $mysqli->query("UPDATE boom_users SET user_mobile = " . $mob['is_mobile'] . " WHERE user_id = '" . $data["user_id"] . "'");
}
function Rank_UserClean(){
    global $mysqli, $data;
    $time = time();
    $get_premium = $mysqli->query("SELECT * FROM boom_users WHERE user_rank > 1 AND rank_end < $time AND rank_end > 0");
    if($get_premium->num_rows > 0){    
        while($user = $get_premium->fetch_assoc()){
            // Disable the update if user_rank is between 70 and 100 (inclusive)
            if ($user['user_rank'] < 70 || $user['user_rank'] > 100) {
                $mysqli->query("UPDATE boom_users SET user_rank = '1', rank_end = 0 WHERE user_id = '{$user['user_id']}'");
            }
        }
        return 1;
    } else {
        return 0;
    }
}

function premiumUserClean(){
    global $mysqli, $data;
    $time = time();
    $get_premium = $mysqli->query("SELECT * FROM boom_users WHERE user_prim >= 0 AND prim_end < $time AND prim_end > 0");
    
    if($get_premium->num_rows > 0){    
        while($user = $get_premium->fetch_assoc()){
            // Disable the update if user_rank is between 70 and 100 (inclusive)
            if ($user['user_rank'] < 70 || $user['user_rank'] > 100) {
                $mysqli->query("UPDATE boom_users SET user_prim = '0', prim_end = 0 WHERE user_id = '{$user['user_id']}'");
                //resetUserPrim($user);
            }
        }
        return 1;
    } else {
        return 0;
    }
}

?>