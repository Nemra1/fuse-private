<?php


require __DIR__ . "./../config_session.php";
// Handle VPN permission requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['target'], $_POST['set_user_vpn'])) {
    // 1. Validate inputs (strictly for uvpn field)
    $target = filter_var($_POST['target'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    $user_vpn = filter_var($_POST['set_user_vpn'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0, 'max_range' => 1] // Only 0 or 1 allowed
    ]);
    if ($target === false || $user_vpn === false) {
        http_response_code(400);
        exit(json_encode(['error' => 'Invalid input']));
    }
    // 2. Secure database update
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE boom_users SET uvpn = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $user_vpn, $target);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'uvpn' => $user_vpn]);
    } else {
        error_log("VPN update failed for user $target: " . $stmt->error);
        http_response_code(500);
        echo json_encode(['error' => 'Update failed']);
    }
    
    $stmt->close();
    exit;
}
if (isset($_POST["change_rank"]) && isset($_POST["target"])) {
    echo boomchangeuserrank();
} else {
    if (isset($_POST["user_color"]) && isset($_POST["user_font"]) && isset($_POST["user"])) {
        echo boomchangecolor();
        exit;
    }
    if (isset($_POST["account_status"]) && isset($_POST["target"])) {
        echo boomchangeuserverify();
    } else {
        if (isset($_POST["delete_user_account"])) {
            echo boomdeleteaccount();
            exit;
        }
        if (isset($_POST["set_user_email"]) && isset($_POST["set_user_id"])) {
            echo staffuseremail();
            exit;
        }
        if (isset($_POST["set_user_about"]) && isset($_POST["target_about"])) {
            echo staffuserabout();
            exit;
        }
        if (isset($_POST["target_id"]) && isset($_POST["user_new_password"])) {
            echo staffchangepassword();
            exit;
        }
        if (isset($_POST["target_id"]) && isset($_POST["user_new_mood"])) {
            echo staffchangemood();
            exit;
        }
        if (isset($_POST["target_id"]) && isset($_POST["user_new_name"])) {
            echo staffchangeusername();
            exit;
        }
        if (isset($_POST["create_user"]) && isset($_POST["create_name"]) && isset($_POST["create_password"]) && isset($_POST["create_email"]) && isset($_POST["create_age"]) && isset($_POST["create_gender"])) {
            echo staffcreateuser();
            exit;
        }
    }
}
if (isset($_POST["user_language"]) && isset($_POST["user_country"]) && isset($_POST["user_timezone"])) {
    echo setuserlocation();
    exit;
}
exit;

function setUserLocation(){
    global $mysqli;
    global $data;
    $language = boomSanitize($_POST["user_language"]);
    $country = escape($_POST["user_country"]);
    $new_timezone = escape($_POST["user_timezone"]);
    require BOOM_PATH . "/system/element/timezone.php";
    $refresh = 0;
    if (file_exists(BOOM_PATH . "/system/language/" . $language . "/language.php")) {
        $mysqli->query("UPDATE boom_users SET user_language = '" . $language . "' WHERE user_id = '" . $data["user_id"] . "'");
        setBoomLang($language);
        if ($language != $data["user_language"]) {
            $refresh++;
        }
    }
    if (in_array($new_timezone, $timezone)) {
        $mysqli->query("UPDATE boom_users SET user_timezone = '" . $new_timezone . "' WHERE user_id = '" . $data["user_id"] . "'");
        if ($new_timezone != $data["user_timezone"]) {
            $refresh++;
        }
    }
    if (validCountry($country)) {
        $mysqli->query("UPDATE boom_users SET country = '" . $country . "' WHERE user_id = '" . $data["user_id"] . "'");
    }
    if (0 < $refresh) {
        return 1;
    }
    return 0;
}

function boomChangeUserRank() {
    global $mysqli, $data;
    // 1. Input Validation
    $target = filter_input(INPUT_POST, 'target', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    $rank = filter_input(INPUT_POST, 'change_rank', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0, 'max_range' => 100] // Adjust max rank as needed
    ]);
    if ($target === false || $rank === false) {
        return 3; // Invalid input
    }
    // 2. Get User Details Securely
    $user = userDetails($target);
    if (empty($user)) {
        return 3; // User not found
    }
    // 3. Permission Check
    if (!canRankUser($user)) {
        return 0; // Permission denied
    }
    // 4. Check if rank is unchanged
    if ($user["user_rank"] == $rank) {
        return 2; // No change needed
    }
    // 5. Update User Rank
    userReset($user, $rank);
    boomNotify("rank_change", [
        "target" => $target,
        "source" => "rank_change",
        "rank" => $rank
    ]);
    // 6. Handle Staff Promotions
    if (isStaff($rank)) {
        // Use prepared statements for all queries
        $queries = [
            "UPDATE boom_users SET room_mute = 0, user_private = 1, user_mute = 0, user_regmute = 0 WHERE user_id = ?",
            "DELETE FROM boom_room_action WHERE action_user = ?",
            "DELETE FROM boom_ignore WHERE ignored = ?"
        ];
        foreach ($queries as $query) {
            $stmt = $mysqli->prepare($query);
            if ($stmt) {
                $stmt->bind_param("i", $target);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    // 7. Log the action
    boomConsole("change_rank", [
        "target" => $user["user_id"],
        "rank" => $rank
    ]);
    return 1; // Success
}

function boomChangeUserVerify() {
    global $mysqli, $data;
    // 1. Input Validation
    $target = filter_input(INPUT_POST, 'target', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'account_status', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0, 'max_range' => 1]
    ]);
    if (!$target || $status === false) {
        return 0;
    }
    // 2. Permission Check
    if (!boomAllow(80)) {
        return 0;
    }
    // 3. Get User Data
    $user = userDetails($target);
    if (empty($user) || !canEditUser($user, 80)) {
        return empty($user) ? 3 : 0;
    }
    // 4. Prepare Update
    if ($status == 0) {
        $verify = userHaveEmail($user) ? (int)$data["activation"] : 0; // Fixed missing parenthesis
        $query = "UPDATE boom_users SET verified = 0, user_verify = ?";
        $params = [$verify];
        
        if ($verify == 1) {
            $query .= ", user_action = user_action + 1";
        }
    } else {
        $query = "UPDATE boom_users SET verified = 1, user_verify = 0";
        $params = [];
    }
    $query .= " WHERE user_id = ?";
    $params[] = $user["user_id"];
    // 5. Execute with Prepared Statement
    $stmt = $mysqli->prepare($query);
    if ($stmt) {
        if (!empty($params)) {
            $types = str_repeat('i', count($params));
            $stmt->bind_param($types, ...$params);
        }
        if ($stmt->execute()) {
            boomConsole("change_verify", ["target" => $user["user_id"]]);
            $stmt->close();
            return 1;
        }
        $stmt->close();
    }

    return 0;
}

function boomChangeColor() {
    global $mysqli, $data, $cody;
    // 1. Input Validation
    $id = filter_input(INPUT_POST, 'user', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    $color = isset($_POST['user_color']) ? trim($_POST['user_color']) : '';
    $font = isset($_POST['user_font']) ? trim($_POST['user_font']) : '';
    // 2. Get User Details
    $user = userDetails($id);
    if (empty($user)) {
        return 0;
    }
    // 3. Permission Check
    if (!canModifyColor($user)) {
        return 0;
    }
    // 4. Validate Color and Font Format
    if (!validNameColor($color)) {
        return 0;
    }
    if (!validNameFont($font)) {
        return 0;
    }
    // 5. Secure Database Update
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_color = ?, user_font = ? WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ssi", $color, $font, $id);
        if ($stmt->execute()) {
            // 6. Log the action
            boomConsole("change_color", [
                "target" => $id,
                "color" => $color,
                "font" => $font,
                "changed_by" => $_SESSION['user_id'] ?? 0
            ]);
            $stmt->close();
            return 1;
        }
        error_log("Color update failed for user {$id}: " . $stmt->error);
        $stmt->close();
    } else {
        error_log("Prepare failed: " . $mysqli->error);
    }

    return 0;
}

function staffUserEmail() {
    global $mysqli, $data, $cody;
    // 1. Input Validation
    $user_id = filter_input(INPUT_POST, 'set_user_id', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    $user_email = isset($_POST['set_user_email']) ? trim($_POST['set_user_email']) : '';
    // 2. Get User Details
    $user = userDetails($user_id);
    if (empty($user)) {
        return 0;
    }
    // 3. Email Validation
    if (!isEmail($user_email)) {
        return 3; // Invalid email format
    }
    // 4. Check for Duplicate Email (if needed)
    if (!checkEmail($user_email) && !boomSame($user_email, $user["user_email"])) {
        return 2; // Email already exists
    }
    // 5. Permission Check
    if (!canModifyEmail($user)) {
        return 0; // Permission denied
    }
    // 6. Process and Secure Update
    $smail = smailProcess($user_email);
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_email = ?, user_smail = ? WHERE user_id = ?");
    if($stmt){
        $stmt->bind_param("ssi", $user_email, $smail, $user_id);
        if ($stmt->execute()) {
            // 7. Log the action
            boomConsole("edit_profile", [
                "target" => $user_id,
                "changed_by" => $_SESSION['user_id'] ?? 0,
                "old_email" => $user["user_email"],
                "new_email" => $user_email
            ]);
            $stmt->close();
            return 1; // Success
        }
        error_log("Email update failed for user {$user_id}: " . $stmt->error);
        $stmt->close();
    } else {
        error_log("Prepare failed: " . $mysqli->error);
    }

    return 0; // Error
}

function staffUserAbout() {
    global $mysqli,$data,$cody;
    // 2. Input Validation
    $user_id = filter_input(INPUT_POST, 'target_about', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    if(!$user_id) {
        return 0;
    }
    // 3. Sanitize User About
    $user_about = isset($_POST['set_user_about']) ? htmlspecialchars(trim($_POST['set_user_about']), ENT_QUOTES, 'UTF-8') : '';
    // 4. Get User Details
    $user = userDetails($user_id);
    if (empty($user)) {
        return 0;
    }
    // 5. Permission Check
    if (!canModifyAbout($user)) {
        return 0;
    }
    // 6. Content Validation
    if (mb_strlen($user_about) > 900) {  // Direct length check
        return 0;
    }    
    if (isBadText($user_about)) {  // Improved bad word check
        return 2;
    }
    // 7. Secure Database Update
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_about = ? WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $user_about, $user_id);
        
        if ($stmt->execute()) {
            // 8. Enhanced Logging
            boomConsole("edit_profile", [
                "target" => $user["user_id"],
                "changed_by" => $_SESSION['user_id'] ?? 0,
                "action" => "about_update",
                "ip" => $_SERVER['REMOTE_ADDR']
            ]);
            $stmt->close();
            return 1;
        }
        error_log("About update failed for user {$user_id}: " . $stmt->error);
        $stmt->close();
    } else {
        error_log("Prepare failed: " . $mysqli->error);
    }

    return 0;
}


function staffChangeUsername() {
    global $mysqli, $data, $cody;
    // 2. Input Validation
    $target = filter_input(INPUT_POST, 'target_id', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]); 
    $new_name = isset($_POST['user_new_name']) ? 
        trim(htmlspecialchars($_POST['user_new_name'], ENT_QUOTES, 'UTF-8')) : 
        '';

    if(!$target || empty($new_name)) {
        return 0;
    }
    // 3. Get User Details
    $user = userDetails($target);
    if (empty($user)) {
        return 0;
    }
    // 4. Permission Check
    if (!canModifyName($user)) {
        return 0;
    }
    // 5. Check if name unchanged
    if ($new_name === $user["user_name"]) {
        return 1;
    }
    // 6. Validate New Username
    if (!validName($new_name)) {
        return 2;
    }
    // 7. Check Name Availability
    if (!boomSame($new_name, $user["user_name"]) && !boomUsername($new_name)) {
        return 3;
    }
    // 8. Secure Database Updates
    try {
        $mysqli->begin_transaction();   
        // Update main user table
        $stmt = $mysqli->prepare("UPDATE boom_users SET user_name = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_name, $user["user_id"]);
        $stmt->execute();
        
        // Update bot table if applicable
        if (isBot($user)) {
            $stmt2 = $mysqli->prepare("UPDATE boom_addons SET bot_name = ? WHERE bot_id = ?");
            $stmt2->bind_param("si", $new_name, $user["user_id"]);
            $stmt2->execute();
        }
        $mysqli->commit();
		// 9. Logging and Notifications
		boomConsole("rename_user", ["target" => $user["user_id"], "custom" => $user["user_name"]]);
		clearNotifyAction($user["user_id"], "name_change");
		boomNotify("name_change", ["target" => $user["user_id"], "source" => "name_change", "custom" => $new_name]);
        changeNameLog($user, $new_name);
        return 1;
    } catch (Exception $e) {
        $mysqli->rollback();
        error_log("Username change failed: " . $e->getMessage());
        return 0;
    }
}

function staffChangeMood() {
    global $mysqli, $data, $cody;
    // 1. Input Validation
    $target = filter_input(INPUT_POST, 'target_id', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    $mood = isset($_POST['user_new_mood']) ? 
        htmlspecialchars(trim($_POST['user_new_mood']), ENT_QUOTES, 'UTF-8') : 
        '';
    // 2. Get User Details
    $user = userDetails($target);
    if (empty($user)) {
        return 0;
    }
    // 3. Permission Check
    if (!canModifyMood($user)) {
        return 0;
    }
    // 4. Check if mood unchanged
    if ($mood === $user["user_mood"]) {
        return getMood($user);
    }
    // 5. Content Validation
    if (isBadText($mood)) {
        return 2;
    }
    if (mb_strlen($mood) > 40) {
        return 0;
    }
    // 6. Secure Database Update
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_mood = ? WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $mood, $user["user_id"]);
        if ($stmt->execute()) {
            // 7. Simplified Logging
            boomConsole("mood_user", [
                "target" => $user["user_id"],
                "custom" => $user["user_name"]
            ]);
            // 8. Return updated mood
            $u = userDetails($user["user_id"]);
            return getMood($u);
        }
        error_log("Mood update failed: " . $stmt->error);
        $stmt->close();
    } else {
        error_log("Prepare failed: " . $mysqli->error);
    }
    return 0;
}
function staffChangePassword() {
    global $mysqli, $data, $cody;
    // 1. CSRF Protection
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        http_response_code(403);
        exit(json_encode(['error' => 'Invalid CSRF token']));
    }
    // 2. Input Validation
    $target = filter_input(INPUT_POST, 'target_id', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);
    $raw_password = $_POST['user_new_password'] ?? '';
    // 3. Get User Details
    $user = userDetails($target);
    if (empty($user)) {
        return 0;
    }
    // 4. Permission Check
    if (!canModifyPassword($user)) {
        return 0;
    }
    // 5. Password Strength Validation
    if (!isStrongPassword($raw_password)) {
        return 2; // Weak password
    }
    // 6. Secure Password Hashing
    $hashed_password = password_hash($raw_password, PASSWORD_BCRYPT);
    if ($hashed_password === false) {
        error_log("Password hashing failed for user: " . $user['user_id']);
        return 0;
    }
    // 7. Database Update with Prepared Statement
    $stmt = $mysqli->prepare("UPDATE boom_users SET user_password = ?, password_changed_at = NOW() WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $hashed_password, $user["user_id"]);
        if ($stmt->execute()) {
            // 8. Security Logging
            error_log("Password changed for user: " . $user['user_id'] . " by " . ($_SESSION['user_id'] ?? 'system'));
            // 9. Invalidate all existing sessions
            $stmt2 = $mysqli->prepare("DELETE FROM user_sessions WHERE user_id = ?");
            $stmt2->bind_param("i", $user["user_id"]);
            $stmt2->execute();
            $stmt2->close();
            return 1;
        }
        error_log("Password update failed for user {$user['user_id']}: " . $stmt->error);
        $stmt->close();
    } else {
        error_log("Prepare failed: " . $mysqli->error);
    }

    return 0;
}

/**
 * Password strength checker
 */
function isStrongPassword($password) {
    return strlen($password) >= 8 &&           // Minimum 8 characters
           //preg_match('/[A-Z]/', $password) && // At least one uppercase
           preg_match('/[a-z]/', $password) && // At least one lowercase
           preg_match('/[0-9]/', $password);   // At least one number
}

function boomDeleteAccount(){
 global $mysqli, $data, $cody;
	$id = escape($_POST["delete_user_account"]);
    $id = sanitizeChatInput($_POST["delete_user_account"]);
    $user = userDetails($id);
    if (empty($user)) {
        return 3;
    }
    if (!canDeleteUser($user)) {
        return 0;
    }
    clearUserData($user);
    boomConsole("delete_account", ["target" => $id, "custom" => $user["user_name"]]);
    return 1;
}

function staffCreateUser(){
	global $mysqli, $data, $cody;
    $name = escape($_POST["create_name"]);
    $name = sanitizeChatInput($_POST["create_name"]);
    $pass = escape($_POST["create_password"]);
    $email = escape($_POST["create_email"]);
    $age = sanitizeChatInput($_POST["create_age"]);
    $gender = sanitizeChatInput($_POST["create_gender"]);

    if (!boomAllow(90)) {
        return 2;
    }
    if ($name == "" || $pass == "" || $email == "") {
        return 2;
    }
    if (!validName($name)) {
        return 3;
    }
    if (!boomUsername($name)) {
        return 4;
    }
    if (!isEmail($email)) {
        return 5;
    }
    if (!checkEmail($email)) {
        return 6;
    }
    if (!checkSmail($email)) {
        return 6;
    }
    if (!validAge($age)) {
        $age = 0;
    }
    if (!validGender($gender)) {
        $gender = 1;
    }
    $enpass = encrypt($pass);
    $system_user = ["name" => $name, "password" => $enpass, "email" => $email, "language" => $data["language"], "verified" => 1, "cookie" => 0, "gender" => $gender, "avatar" => genderAvatar($gender), "age" => $age];
    $user = boomInsertUser($system_user);
    boomConsole("create_user", ["target" => $user["user_id"]]);
    return 1;
}

?>