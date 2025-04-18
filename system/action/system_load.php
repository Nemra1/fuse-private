<?php

require __DIR__ . '/../config_session.php';
session_write_close();
if (!isset($_POST["page"])) {
    exit;
}
$d["pending"] = [];
if (isset($_POST['page'])) {
    global $mysqli;
    // Sanitize input
    $page = trim($_POST["page"]);
    boomgeo();
    updateuseraccount();
    $d["pending"] = pendingPush($d["pending"], checkregmute());
    $d['geo'] = boomGeo();
    $d['recheckVpn'] = recheckVpn();
    if (useStore()) {
        $d['premiumUserClean'] = premiumUserClean();
        $d['rankUserClean'] = Rank_UserClean();
    }
    // Secure JSON output
    header('Content-Type: application/json');
    echo json_encode($d, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit();
}

function boomGeo(){
    global $mysqli, $data;
    if (checkGeo()) {
        // Securely require required files
        require BOOM_PATH . "/system/location/country_list.php";
        require BOOM_PATH . "/system/element/timezone.php";
        // Fetch IP address securely
        $ip = getIp();
        $country = "ZZ";
        $tzone = $data["user_timezone"];
        // Fetch geolocation data with proper URL encoding
        $loc = doCurl("http://www.geoplugin.net/php.gp?ip=" . urlencode($ip));
        // Decode JSON response and validate it
        $res = json_decode($loc, true);
        if ($res !== null && isset($res["geoplugin_countryCode"]) && array_key_exists($res["geoplugin_countryCode"], $country_list)) {
            $country = escape($res["geoplugin_countryCode"]);
        }
        if (isset($res["geoplugin_timezone"]) && in_array($res["geoplugin_timezone"], $timezone)) {
            $tzone = escape($res["geoplugin_timezone"]);
        }
        // Secure SQL query to update user info
        $user_id = intval($data["user_id"]); // Ensure user ID is an integer
        $ip = escape($ip); // Secure IP address
        $country = escape($country); // Secure country code
        $tzone = escape($tzone); // Secure timezone
        // Prepare the query with parameterized statements (to prevent SQL injection)
        $query = "UPDATE boom_users SET user_ip = ?, country = ?, user_timezone = ? WHERE user_id = ?";
        $stmt = $mysqli->prepare($query);
        // Check if prepare() was successful
        if ($stmt === false) {
            // Log error and return failure
            error_log("Error preparing SQL statement: " . $mysqli->error);
            return 0;
        }
        // Bind the parameters and execute the query
        $stmt->bind_param("sssi", $ip, $country, $tzone, $user_id);
        if ($stmt->execute()) {
            //redisUpdateUser($data['user_id']);
            return 1;
        } else {
            // Log error and handle failure
            error_log("Error executing SQL statement: " . $stmt->error);
            return 0;
        }
    }

    return 0;
}

function checkRegMute(){
    global $data, $page;
    $result = "";
    // Validate page context
    if (insideChat($page)) {
        // Check if the user is a guest and muted
        if (guestMuted()) {
            $result = modalPending(boomTemplate("element/guest_talk"), "empty", 400);
        } else {
            // Ensure $data['user_id'] is valid to prevent SQL injection
            if (isset($data['user_id']) && is_numeric($data['user_id'])) {
                if (isRegMute($data)) {
                    $result = modalPending(boomTemplate("element/regmute"), "empty", 400);
                } else {
                    $result = "";
                }
            } else {
                $result = modalPending(boomTemplate("element/error"), "empty", 400); // Handle invalid data scenario
            }
        }
    }
    return $result;
}


function updateUserAccount(){
    global $mysqli, $data, $cody;
    // Validate that user_id exists and is numeric to prevent SQL injection
    if (isset($data['user_id']) && is_numeric($data['user_id'])) {
        $mob = getMobile();
        // Ensure the mobile value is properly sanitized or validated before updating
        $is_mobile = intval($mob['is_mobile']); // Ensure the value is an integer
        // Prepare the query to prevent SQL injection
        $stmt = $mysqli->prepare("UPDATE boom_users SET user_mobile = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $is_mobile, $data['user_id']); // "ii" indicates two integer parameters
        $stmt->execute();
        $stmt->close();
    } else {
        // Handle error in case user_id is invalid or not set
        error_log('Invalid user_id for updateUserAccount.');
    }
}

function Rank_UserClean(){
    global $mysqli, $data;
    $time = time();
    // Use a prepared statement for the query to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT user_id, user_rank, rank_end FROM boom_users WHERE user_rank > 1 AND rank_end < ? AND rank_end > 0");
    $stmt->bind_param("i", $time); // Bind the $time parameter as an integer
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {    
        while ($user = $result->fetch_assoc()) {
            // Disable the update if user_rank is between 70 and 100 (inclusive)
            if ($user['user_rank'] < 70 || $user['user_rank'] > 100) {
                // Prepare and bind parameters for the update query
                $update_stmt = $mysqli->prepare("UPDATE boom_users SET user_rank = ?, rank_end = ? WHERE user_id = ?");
                $user_rank = 1;
                $rank_end = 0;
                $update_stmt->bind_param("iii", $user_rank, $rank_end, $user['user_id']); // Bind parameters as integers
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}


function premiumUserClean(){
    global $mysqli, $data;
    $time = time();
    // Use a prepared statement for the query to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT user_id, user_rank, user_prim, prim_end FROM boom_users WHERE user_prim >= 0 AND prim_end < ? AND prim_end > 0");
    $stmt->bind_param("i", $time); // Bind the $time parameter as an integer
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {    
        while ($user = $result->fetch_assoc()) {
            // Disable the update if user_rank is between 70 and 100 (inclusive)
            if ($user['user_rank'] < 70 || $user['user_rank'] > 100) {
                // Prepare and bind parameters for the update query
                $update_stmt = $mysqli->prepare("UPDATE boom_users SET user_prim = ?, prim_end = ? WHERE user_id = ?");
                $user_prim = 0;
                $prim_end = 0;
                $update_stmt->bind_param("iii", $user_prim, $prim_end, $user['user_id']); // Bind parameters as integers
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
        $stmt->close();
        return 1;
    } else {
        $stmt->close();
        return 0;
    }
}


?>