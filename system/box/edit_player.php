<?php
require_once('../config_session.php');
if (!isset($_POST['edit_player'])) {
    // Return an error response instead of die()
    echo json_encode(['status' => 'error', 'message' => 'Player ID is missing.']);
    exit();
}
// Sanitize the input using escape() function
$id = escape($_POST['edit_player']);
// Use a prepared statement to prevent SQL injection
$stmt = $mysqli->prepare("SELECT * FROM boom_radio_stream WHERE id = ?");
$stmt->bind_param("i", $id); // Bind the ID as an integer
$stmt->execute();
$result = $stmt->get_result();
// Check if the player exists
if ($result->num_rows < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Player not found.']);
    exit();
} else {
    // Fetch the player data securely
    $player = $result->fetch_assoc();
    // Optionally, you can return player data as a JSON response if it's an API
    echo json_encode(['status' => 'success', 'player' => $player]);
    exit();
}

// Close the prepared statement
$stmt->close();
?>

<div class="pad_box">
	<div class="setting_element ">
		<p class="label"><?php echo $lang['stream_alias']; ?></p>
		<input id="new_player_alias" class="full_input" value="<?php echo $player['stream_alias']; ?>"/>
	</div>
	<div class="setting_element ">
		<p class="label"><?php echo $lang['stream_url']; ?></p>
		<input id="new_player_url" class="full_input" value="<?php echo $player['stream_url']; ?>"/>
	</div>
	<button onclick="savePlayer(<?php echo $player['id']; ?>);" type="button" class="reg_button theme_btn tmargin10"><i class="ri-save-line"></i> <?php echo $lang['save']; ?></button>
	<button type="button" class="reg_button cancel_modal default_btn"><?php echo $lang['cancel']; ?></button>
</div>