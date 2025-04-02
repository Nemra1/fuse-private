<?php
/**
* Codychat
*
* @package Codychat
* @author www.boomcoding.com
* @copyright 2020
* @terms any use of this script without a legal license is prohibited
* all the content of Codychat is the propriety of BoomCoding and Cannot be 
* used for another project.
*/
require_once("./../config_session.php");

if(mainBlocked()){
    die(json_encode(['error' => 'System unavailable']));
}
// 1. Verify CSRF Token First
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    error_log("CSRF token mismatch from IP: " . $_SERVER['REMOTE_ADDR']);
    die(json_encode(['error' => 'security_error']));
}
// 2. Strict Input Validation
if (!isset($_POST['content'], $_POST['snum'], $_POST['csrf_token']) || 
    !verifyCsrfToken($_POST['csrf_token'])) {
    die(json_encode(['error' => 'Invalid request']));
}

// 3. Rate Limiting
if (checkFlood()) {
    die(json_encode(['error' => 'flood'])); // Consistent with your 100 code
}

// 4. User Status Checks
if (muted() || isRoomMuted($data)) {
    die(json_encode(['error' => 'muted']));
}

// 5. Content Processing Pipeline
$content = $_POST['content'];

// Length Validation
if (isTooLong($content, $data['max_main']) || empty(trim($content))) {
    die(json_encode(['error' => 'invalid_length']));
}

// 6. Secure Filter Chain
$content = secureFilterPipeline($content);

// 7. Final Validation
if (!validateFinalContent($content)) {
    isSecureContent('invalid_content', $content);
    //die(json_encode(['error' => 'invalid_content']));
}

// 8. Database Insertion
echo userPostChat($content, ['snum' => escape($_POST['snum'])]);

// Security Functions
function secureFilterPipeline($input) {
    // Step 1: Standard filtering
    $input = escape($input);
    $input = wordFilter($input, 1);
    $input = textFilter($input);

    return $input;
}

function validateFinalContent($content) {
    // Step 1: Temporarily replace emoji codes with a unique placeholder so they aren't treated as HTML
    $contentWithPlaceholders = preg_replace('/(:\w+:)/', '__EMOJI_PLACEHOLDER__', $content);
    // Step 2: Strip only actual HTML tags from the content
    if (strip_tags($contentWithPlaceholders) !== $contentWithPlaceholders) {
        return false; // Contains HTML, which is not allowed
    }
    // Step 3: Return true since emojis are allowed and HTML has been checked
    return true;
}

function isSecureContent($content) {
    // 1. Check for disallowed HTML tags
    if (strip_tags($content) !== $content) {
        return false; // Contains HTML, which is not allowed
    }
    // 2. Check for SQL Injection patterns
    $blacklist = [ 
        "/(\bunion\b|\bselect\b|\binsert\b|\bupdate\b|\bdelete\b|\bdrop\b|\bshutdown\b)/i", // SQL commands
        "/(--|#|\/\*|\*\/)/" // Comment injection
    ];
    foreach ($blacklist as $pattern) {
        if (preg_match($pattern, $content)) {
            return false; // Contains SQL injection patterns
        }
    }
    return true; // Content is safe
}

?>
