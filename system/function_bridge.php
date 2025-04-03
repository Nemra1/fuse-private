<?php
function createBridgeUser($provider, $info) {
    global $bmysqli, $bdata;
    $bridge_user = array();
    
    // Validate input
    if (empty($info) || empty($provider)) {
        return false;
    }
    
    // Default values for the new user
    $bridge_default = array(
        'id' => '',
        'name' => '',
        'age' => 0,
        'gender' => 3, // Default: unspecified
        'password' => bridgeRandomPass(),
        'language' => bridgeLanguage(),
        'avatar' => '',
        'ip' => bridgeGetIp(),
    );
    
    // Merge incoming data with default values
    $bridge = array_merge($bridge_default, $info);
    
    // Secure and sanitize input data
    $provider = bridgeEscape($provider);
    $bridge['id'] = bridgeEscape($bridge['id']);
    $bridge['name'] = bridgeEscape($bridge['name']);
    $bridge['age'] = (int)$bridge['age']; // Ensure age is an integer
    $bridge['gender'] = bridgeEscape($bridge['gender']);
    $bridge['password'] = bridgeEscape($bridge['password']);
    $bridge['language'] = bridgeEscape($bridge['language']);
    $bridge['avatar'] = bridgeEscape($bridge['avatar']);
    
    // Check if critical fields are provided
    if (empty($bridge['id']) || empty($bridge['name'])) {
        return false;
    }
    
    // Define unique bridge identity
    $bridge['identity'] = $provider . '_' . $bridge['id'];
    
    // Ensure valid gender
    switch (strtolower($bridge['gender'])) {
        case 'female':
            $bridge['gender'] = 2;
            break;
        case 'male':
            $bridge['gender'] = 1;
            break;
        default:
            $bridge['gender'] = 3; // Default: unspecified
            break;
    }
    
    // Validate avatar URL (ensure it’s a full URL starting with http)
    if (stripos($bridge['avatar'], 'http') === false) {
        $bridge['avatar'] = '';
    }
    
    // Check if the bridge user already exists
    $bridge_exist = $bmysqli->prepare("SELECT * FROM `boom_users` WHERE `sub_id` = ? AND `sub_id` != '' LIMIT 1");
    $bridge_exist->bind_param('s', $bridge['identity']);
    $bridge_exist->execute();
    $result = $bridge_exist->get_result();
    
    if ($result->num_rows > 0) {
        // Update the user IP if the user already exists
        $bridge_user = $result->fetch_assoc();
        $update_stmt = $bmysqli->prepare("UPDATE boom_users SET user_ip = ? WHERE user_id = ?");
        $update_stmt->bind_param('si', $bridge['ip'], $bridge_user['user_id']);
        $update_stmt->execute();
    } else {
        // User doesn't exist, create a new one
        $bridge['name'] = getBridgeName($bridge['name'], $bmysqli);
        $insert_stmt = $bmysqli->prepare("INSERT INTO boom_users 
            (user_name, sub_id, user_password, user_ip, user_join, last_action,
            user_theme, user_sex, user_age, user_language, user_timezone, user_roomid, verified, user_rank)
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)");
        
        $user_join = time();
        $insert_stmt->bind_param(
            'sssssiiiisss',
            $bridge['name'],
            $bridge['identity'],
            $bridge['password'],
            $bridge['ip'],
            $user_join,
            $user_join,
            $bdata['default_theme'],
            $bridge['gender'],
            $bridge['age'],
            $bridge['language'],
            $bdata['timezone'],
            0 // user_roomid
        );
        $insert_stmt->execute();
        $bridge_user = bridgeUserDetails($bmysqli->insert_id);
    }
    
    // Download avatar if provided
    if ($bridge['avatar'] != '') {
        $add_bridge_avatar = downloadBridgeAvatar($bridge_user, $bridge['avatar'], $provider);
    }
    
    // Create session for the new or existing user
    setBoomCookie($bridge_user['user_id'], $bridge_user['user_password']);
    
    return $bridge_user;
}

// bridge functions
function bridgeMinutesUp($min){
	return time() + ($min * 60);
}
function bridgeVersion(){
	$fversion = 70;
	$pversion = PHP_MAJOR_VERSION . PHP_MINOR_VERSION;
	if($pversion >= 71){
		$fversion = 71;
	}
	if($pversion >= 72){
		$fversion = 72;
	}
	return 'php' . $fversion;
}
function bridgeRandomPass(){
	$text = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890++--';
	$text = substr(str_shuffle($text), 0, 10);
	return bridgeEncrypt($text);
}
function bridgeGetIp(){
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $cloud =   @$_SERVER["HTTP_CF_CONNECTING_IP"];
    $remote  = $_SERVER['REMOTE_ADDR'];
    if(filter_var($cloud, FILTER_VALIDATE_IP)) {
        $ip = $cloud;
    }
    else if(filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP)){
        $ip = $forward;
    }
    else{
        $ip = $remote;
    }
    return bridgeEscape($ip);	
}
function cleanBridgeName($name){
	return str_replace(
		array(' ', "'", '"', '<', '>', ",",")","("),
		array('_', '', '', '', '', '', '', ''),
		$name
	);
}
function bridgeEncrypt($d){
	return sha1(str_rot13($d . BOOM_CRYPT));
}
function bridgeEscape($t){
	global $bmysqli;
	return $bmysqli->real_escape_string(trim(htmlspecialchars($t, ENT_QUOTES)));
}
function bridgeLanguage(){
	global $bdata, $cody;
	$l = $bdata['language'];
	if(isset($_COOKIE[BOOM_PREFIX . 'lang'])){
		$test_lang = bridgeEscape($_COOKIE[BOOM_PREFIX . 'bc_lang']);
		if(file_exists(BOOM_PATH . '/system/language/' . $test_lang . '/language.php')){
			$l = $test_lang;
		}
	}
	return $l;
}
function bridgeUserDetails($id) {
    global $bmysqli;
    $user = array();
    // Ensure the ID is sanitized and valid
    $id = (int) $id; // Cast to integer for safety
    // Use prepared statement to fetch user details
    $stmt = $bmysqli->prepare("SELECT * FROM boom_users WHERE user_id = ? LIMIT 1");
    $stmt->bind_param('i', $id); // Bind the integer user ID
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
    $stmt->close(); // Always close the statement
    return $user;
}

function downloadBridgeAvatar($user, $url, $prefix){
    global $bmysqli;
    // Escape URL for security
    $url = bridgeEscape($url);
    // Ensure the user has a default avatar or the avatar needs to be updated
    if ($user['user_tumb'] == 'default_avatar.png' || stripos($user['user_tumb'], $prefix) !== false) {
        // Generate a unique avatar filename
        $img = $prefix . '_' . md5(time() . $user['user_id']) . '.jpg';
        $path = BOOM_PATH . '/avatar/' . $img;
        // Initialize file handler and cURL for downloading the image
        $fh = fopen($path, 'wb');
        if (!$fh) {
            // If file handler fails, return an error
            return false;
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_FILE, $fh);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);  // Enable SSL verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);    // Verify host against certificate
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 1);
        // Execute cURL request
        $result = curl_exec($curl);
        if ($result === false) {
            // Handle cURL error
            fclose($fh);
            curl_close($curl);
            return false;
        }
        curl_close($curl);
        fclose($fh);
        // Check if the file was successfully downloaded
        if (file_exists($path)) {
            // Get image info to verify it is a valid image
            $info = getimagesize($path);
            if ($info !== false) {
                // If it's a valid image, update the user avatar in the database
                $unlink = unlinkBridgeAvatar($user['user_tumb']); // Delete old avatar
                // Update the database with the new avatar
                $stmt = $bmysqli->prepare("UPDATE boom_users SET user_tumb = ? WHERE user_id = ?");
                $stmt->bind_param('si', $img, $user['user_id']);
                $stmt->execute();
                $stmt->close();
            } else {
                // If the file is not a valid image, delete it
                $unlink_fail = unlinkBridgeAvatar($img);
                return false;
            }
        } else {
            return false; // File was not downloaded
        }
    }
    return true;
}


function unlinkBridgeAvatar($file){
	if(stripos($file, 'default') === false){
		$delete =  BOOM_PATH . '/avatar/' . $file;
		if(file_exists($delete)){
			unlink($delete);
		}
	}
	return true;
}
function getBridgeName($name, $connection){
    $tcount = 0;
    $try = cleanBridgeName($name);
    while (true) {
        // Use prepared statement to avoid SQL injection
        $stmt = $connection->prepare("SELECT user_name FROM boom_users WHERE user_name = ?");
        $stmt->bind_param('s', $try);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $tcount++;
            $try = $name . $tcount;
        } else {
            break; // Exit loop when a unique name is found
        }
        $stmt->close();
    }
    return $try;
}

?>