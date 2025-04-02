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


function staffDeleteIp(){
    global $mysqli, $data;
    // Ensure the user has the correct permission
    if (!boomAllow(90)) {
        return 0;
    }
    // Sanitize and validate the input: Check if the input is a valid IP address
    if (!isset($_POST["delete_ip"]) || !filter_var($_POST["delete_ip"], FILTER_VALIDATE_IP)) {
        return 0; // Return 0 if the input is not a valid IP address
    }
    $ip = $_POST["delete_ip"]; // Assign the sanitized IP address
    // Use prepared statements to prevent SQL injection
    $stmt = $mysqli->prepare("DELETE FROM boom_banned WHERE ip = ?");
    if ($stmt === false) {
        return 0; // Return 0 if the prepared statement couldn't be created
    }
    // Bind the IP address parameter to the prepared statement
    $stmt->bind_param("s", $ip);
    // Execute the query
    if ($stmt->execute()) {
        return 1; // Return 1 if the query is successful
    } else {
        return 0; // Return 0 if the query failed
    }
}

function staffDeleteWord(){
    global $mysqli, $data;
    // Ensure the user has the correct permission
    if (!boomAllow(80)) {
        return 0;
    }
    // Sanitize and validate the input
    if (!isset($_POST["delete_word"]) || !is_numeric($_POST["delete_word"])) {
        return 0; // Return 0 if input is not a valid number
    }
    $wordId = (int) $_POST["delete_word"]; // Cast to integer
    // Use prepared statements to prevent SQL injection
    $stmt = $mysqli->prepare("DELETE FROM boom_filter WHERE id = ?");
    if ($stmt === false) {
        return 0; // Return 0 if the prepared statement couldn't be created
    }
    // Bind the integer parameter to the prepared statement
    $stmt->bind_param("i", $wordId);
    // Execute the query
    if ($stmt->execute()) {
        return 1; // Return 1 if the query is successful
    } else {
        return 0; // Return 0 if the query failed
    }
}


function staffAddWord() {
    global $mysqli, $data;
    // Ensure the user has permission
    if (!boomAllow(80)) {
        return "";
    }
    // Check if necessary POST variables are set
    if (!isset($_POST["add_word"]) || !isset($_POST["type"])) {
        return "";
    }
    // Sanitize and limit word input
    $word = sanitizeChatInput($_POST["add_word"]);
    $type = sanitizeChatInput($_POST["type"]);
    // Validate type to only allow certain values
    $allowed_types = ["email", "username", "other"]; // Add more valid types if needed
    if (!in_array($type, $allowed_types, true)) {
        return "";
    }
    // Higher permission needed for certain types
    if (($type == "email" || $type == "username") && !boomAllow(90)) {
        return "";
    }
    // Check if word already exists
    $stmt = $mysqli->prepare("SELECT id FROM boom_filter WHERE word = ? AND word_type = ?");
    if (!$stmt) {
        return "";
    }
    $stmt->bind_param("ss", $word, $type);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return 0;
    }
    $stmt->close();
    // If the word is not empty, insert it into the database
    if (!empty($word)) {
        $stmt = $mysqli->prepare("INSERT INTO boom_filter (word, word_type) VALUES (?, ?)");
        if (!$stmt) {
            return "";
        }
        $stmt->bind_param("ss", $word, $type);
        if ($stmt->execute()) {
            $word_added["id"] = $stmt->insert_id;
            $word_added["word"] = $word;
            $stmt->close();
            return boomTemplate("element/word", $word_added);
        }
        $stmt->close();
    }

    return 2;
}


?>