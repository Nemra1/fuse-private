<?php


require __DIR__ . "./../config_session.php";

if (isset($_POST["word_action"]) && isset($_POST["word_delay"])) {
    echo setwordaction();
    exit;
}
if (isset($_POST["spam_action"]) && isset($_POST["spam_delay"])) {
    echo setspamaction();
    exit;
}
if (isset($_POST["email_filter"])) {
    echo setemailfilter();
    exit;
}
if (isset($_POST["delete_ip"])) {
    echo staffdeleteip();
    exit;
}
if (isset($_POST["delete_word"])) {
    echo staffdeleteword();
    exit;
}
if (isset($_POST["add_word"]) && isset($_POST["type"])) {
    echo staffaddword();
    exit;
}

function setWordAction(){
    global $mysqli,$data;
    // Check if the user has permission
    if (!boomAllow(90)) {
        return 0;
    }
    // Sanitize the inputs using sanitizeChatInput function
    $action = isset($_POST['word_action']) ? sanitizeChatInput($_POST['word_action']) : '';
    $delay = isset($_POST['word_delay']) ? sanitizeChatInput($_POST['word_delay']) : '';
    // Validate sanitized inputs
    if (empty($action) || empty($delay)) {
        return 0; // Ensure action and delay are not empty
    }
    // Make sure the delay is a valid number and falls within a reasonable range
    if (!is_numeric($delay) || $delay < 0 || $delay > 3600) {
        return 0; // Invalid delay value
    }
    // Prepare and execute the query safely
    $stmt = $mysqli->prepare("UPDATE boom_setting SET word_action = ?, word_delay = ? WHERE id = 1");
    $stmt->bind_param('si', $action, $delay); // 'si' means string and integer
    $stmt->execute();
    // Check if there were any errors with the execution
    if ($stmt->error) {
        return 0; // Return 0 if there was an error
    }
    $stmt->close();
    return 1; // Return 1 if everything went successfully
}

function setSpamAction(){
    global $mysqli,$data;
    // Check if the user has permission
    if (!boomAllow(90)) {
        return 0;
    }
    // Sanitize the inputs using sanitizeChatInput function
    $action = isset($_POST['spam_action']) ? sanitizeChatInput($_POST['spam_action']) : '';
    $delay = isset($_POST['spam_delay']) ? sanitizeChatInput($_POST['spam_delay']) : '';
    // Validate sanitized inputs
    if (empty($action) || empty($delay)) {
        return 0; // Ensure action and delay are not empty
    }
    // Make sure the delay is a valid number and falls within a reasonable range (e.g., 0 to 3600 seconds)
    if (!is_numeric($delay) || $delay < 0 || $delay > 3600) {
        return 0; // Invalid delay value
    }
    // Prepare and execute the query safely
    $stmt = $mysqli->prepare("UPDATE boom_setting SET spam_action = ?, spam_delay = ? WHERE id = 1");
    $stmt->bind_param('si', $action, $delay); // 'si' means string and integer
    $stmt->execute();
    // Check if there were any errors with the execution
    if ($stmt->error) {
        return 0; // Return 0 if there was an error
    }
    $stmt->close();
    return 1; // Return 1 if everything went successfully
}


function setEmailFilter(){
 global $mysqli,$data;
    // Check if the user has permission to perform this action
    if (!boomAllow(90)) {
        return 0;
    }
    // Sanitize the input using sanitizeChatInput function
    $action = isset($_POST['email_filter']) ? sanitizeChatInput($_POST['email_filter']) : '';
    // Validate the sanitized input: Ensure that the action is not empty and is a valid string.
    if (empty($action)) {
        return 0; // Ensure the filter action is not empty
    }
    // Prepare and execute the query safely using prepared statements
    $stmt = $mysqli->prepare("UPDATE boom_setting SET email_filter = ? WHERE id = 1");
    $stmt->bind_param('s', $action); // 's' means string
    $stmt->execute();
    // Check for any errors in the execution
    if ($stmt->error) {
        return 0; // Return 0 if there was an error
    }
    $stmt->close();
    return 1; // Return 1 if everything was successful
}


function staffDeleteIp()
{
    global $mysqli;
    global $data;
    $ip = escape($_POST["delete_ip"]);
    if (!boomAllow(90)) {
        return 0;
    }
    $mysqli->query("DELETE FROM boom_banned WHERE id = '" . $ip . "'");
    return 1;
}

function staffDeleteWord()
{
    global $mysqli;
    global $data;
    $word = escape($_POST["delete_word"]);
    if (!boomAllow(80)) {
        return 0;
    }
    $mysqli->query("DELETE FROM boom_filter WHERE id = '" . $word . "'");
    return 1;
}

function staffAddWord()
{
    global $mysqli;
    global $data;
    $word = escape($_POST["add_word"]);
    $type = escape($_POST["type"]);
    if (!boomAllow(80)) {
        return "";
    }
    $check_word = $mysqli->query("SELECT * FROM boom_filter WHERE word = '" . $word . "' AND word_type = '" . $type . "'");
    if (0 < $check_word->num_rows) {
        return 0;
    }
    if (($type == "email" || $type == "username") && !boomAllow(90)) {
        return "";
    }
    if ($word != "") {
        $mysqli->query("INSERT INTO boom_filter (word, word_type) VALUE ('" . $word . "', '" . $type . "')");
        $word_added["id"] = $mysqli->insert_id;
        $word_added["word"] = $word;
        return boomTemplate("element/word", $word_added);
    }
    return 2;
}

?>