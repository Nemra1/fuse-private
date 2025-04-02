<?php
require __DIR__ . "./../config_session.php";


if (isset($_POST['update_status'])) {
    // Escape and validate the status
    $status = escape($_POST['update_status']);
    // Validate the status to ensure it's a valid value (1 as default if invalid)
    if (!validStatus($status)) {
        $status = 1;
    }
    // Prepare the query with a parameterized statement to prevent SQL injection
    $update_stmt = $mysqli->prepare("UPDATE boom_users SET user_status = ? WHERE user_id = ?");
    // Bind parameters to the prepared statement
    $update_stmt->bind_param("ii", $status, $data['user_id']);
    // Execute the query and check for success
    if ($update_stmt->execute()) {
        // Return success with status title and icon
        echo boomCode(1, array(
            'text' => statusTitle($status),
            'icon' => newStatusIcon($status)
        ));
    } else {
        // Return failure if the update query failed
        echo boomCode(0, array('error' => 'Failed to update status'));
    }
    die();
}

if (isset($_POST['edit_username'], $_POST['new_name'])) {
    $new_name = escape($_POST['new_name']);
    // Check if the user has permission to change the username
    if (!canName()) {
        echo 0; // No permission to change username
        die();
    }
    // Check if the new name is the same as the current username
    if ($new_name == $data['user_name']) {
        echo 1; // No change needed
        die();
    }
    // Validate the new name
    if (!validName($new_name)) {
        echo 2; // Invalid name
        die();
    }
    // Check if the new name is available and doesn't match the current name
    if (!boomSame($new_name, $data['user_name'])) {
        if (!boomUsername($new_name)) {
            echo 3; // Username already taken
            die();
        }
    }
    // Prepare the SQL query with a prepared statement to prevent SQL injection
    $update_stmt = $mysqli->prepare("UPDATE boom_users SET user_name = ? WHERE user_id = ?");
    $update_stmt->bind_param("si", $new_name, $data['user_id']);
    // Execute the query and check for success
    if ($update_stmt->execute()) {
        // Log the name change and perform other related actions
        boomConsole('change_name', array('custom' => $data['user_name']));
        changeNameLog($data, $new_name);      
        echo 1; // Success
    } else {
        echo 0; // Failed to update username
    }  
    die();
}

if (isset($_POST['save_color'], $_POST['save_bold'], $_POST['save_font'])) {
    $c = escape($_POST['save_color']);
    $b = escape($_POST['save_bold']);
    $f = escape($_POST['save_font']);
    // Validate input values
    if (!validTextColor($c) || !validTextWeight($b) || !validTextFont($f)) {
        echo 0; // Invalid input
        die();
    }
    // Prepare the SQL query with a prepared statement to prevent SQL injection
    $update_stmt = $mysqli->prepare("
        UPDATE boom_users 
        SET bccolor = ?, bcbold = ?, bcfont = ? 
        WHERE user_id = ?
    ");
    $update_stmt->bind_param("sssi", $c, $b, $f, $data['user_id']);
    // Execute the query and check if successful
    if ($update_stmt->execute()) {
        echo 1; // Success
    } else {
        echo 0; // Failed to update
    }
    die();
}

if (isset($_POST['save_mood'])) {
    $mood = escape($_POST['save_mood']);
    // Check if the user has permission to change their mood
    if (!canMood()) {
        echo 0; // No permission to change mood
        die();
    }
    // Validate the mood input
    if (isBadText($mood)) {
        echo 2; // Invalid text (e.g., offensive content)
        die();
    }
    if (isTooLong($mood, 40)) {
        echo 0; // Mood is too long
        die();
    }
    // If the mood hasn't changed, return the current mood
    if ($mood == $data['user_mood']) {
        echo getMood($data); // Return the existing mood
        die();
    }
    // Prepare the SQL query with a prepared statement to prevent SQL injection
    $update_stmt = $mysqli->prepare("
        UPDATE boom_users 
        SET user_mood = ? 
        WHERE user_id = ?
    ");
    $update_stmt->bind_param("si", $mood, $data['user_id']);
    // Execute the query and check if successful
    if ($update_stmt->execute()) {
        // Fetch the updated user details
        $u = userDetails($data['user_id']);
        echo getMood($u); // Return the updated mood
    } else {
        echo 0; // Failed to update mood
    }
    die();
}

if (isset($_POST['save_info'], $_POST['age'], $_POST['gender'])) {
    $age = escape($_POST['age']);
    $gender = escape($_POST['gender']);
    // Validate gender and age inputs
    if (!validGender($gender) || !validAge($age)) {
        echo boomCode(0); // Invalid input
        die();
    }
    // Update user sex in the data array
    $data['user_sex'] = $gender;
    // Determine the avatar
    if (defaultAvatar($data['user_tumb'])) {
        $avatar = myAvatar(resetAvatar($data)); // Default avatar
    } else {
        $avatar = myAvatar($data['user_tumb']); // Custom avatar
    }
    // Prepare SQL query for updating user age and sex with prepared statements
    $update_stmt = $mysqli->prepare("
        UPDATE boom_users 
        SET user_age = ?, user_sex = ? 
        WHERE user_id = ?
    ");
    $update_stmt->bind_param("ssi", $age, $gender, $data['user_id']);
    // Execute the query and check if the update was successful
    if ($update_stmt->execute()) {
        echo boomCode(1, ['av' => $avatar]); // Success, return the avatar
    } else {
        echo boomCode(0); // Failed to update user info
    }
    die();
}
if (isset($_POST['save_about'], $_POST['about'])) {
    $about = clearBreak($_POST['about']);
    $about = escape($about);
    // Check if the 'about' text is too long
    if (isTooLong($about, 900)) {
        echo 0; // Text too long
        die();
    }
    // Check for bad text in the 'about' field
    if (isBadText($about)) {
        echo 2; // Bad text
        die();
    }
    // Prepare SQL query to update 'about' field for the user
    $update_stmt = $mysqli->prepare("
        UPDATE boom_users 
        SET user_about = ? 
        WHERE user_id = ?
    ");
    $update_stmt->bind_param("si", $about, $data['user_id']);
    // Execute the query and check if it was successful
    if ($update_stmt->execute()) {
        echo 1; // Success
    } else {
        echo 0; // Failed to update
    }
    die();
}
	if (isset($_POST['my_username_color'], $_POST['my_username_font'])) {
		// Escape input values to prevent SQL injection
		$color = escape($_POST['my_username_color']);
		$font = escape($_POST['my_username_font']);
		// Validate the color and font
		if (!validNameColor($color)) {
			echo 0; // Invalid color
			die();
		}
		if (!validNameFont($font)) {
			echo 0; // Invalid font
			die();
		}
		// Prepare SQL query for updating user color and font
		$update_stmt = $mysqli->prepare("
			UPDATE boom_users 
			SET user_color = ?, user_font = ? 
			WHERE user_id = ?
		");
		$update_stmt->bind_param("ssi", $color, $font, $data['user_id']);
		// Execute the query and check if it was successful
		if ($update_stmt->execute()) {
			echo 1; // Success
		} else {
			echo 0; // Failed to update
		}
		die();
	}
	if (isset($_POST['set_private_mode'])) {
		// Escape input value to prevent SQL injection
		$pmode = escape($_POST['set_private_mode']);
		// Check if the user is a guest and validate the private mode
		if (isGuest($data)) {
			// Ensure the private mode is either 0 or 1 for guests
			if ($pmode != 0 && $pmode != 1) {
				echo 0; // Invalid private mode for guest
				die();
			}
		}
		// Check if the private mode is valid (0, 1, 2, or 3)
		if (in_array($pmode, [0, 1, 2, 3])) {
			// Prepare the SQL query to update the private mode
			$update_stmt = $mysqli->prepare("
				UPDATE boom_users 
				SET user_private = ? 
				WHERE user_id = ?
			");
			$update_stmt->bind_param("ii", $pmode, $data['user_id']);
			// Execute the query and check if it was successful
			if ($update_stmt->execute()) {
				echo 1; // Success
			} else {
				echo 0; // Failed to update
			}
			die();
		} else {
			echo 0; // Invalid private mode
			die();
		}
	}
if (isset($_POST['change_sound'], $_POST['chat_sound'], $_POST['private_sound'], $_POST['notify_sound'], $_POST['name_sound'])) {
    // Escape input values to prevent SQL injection
    $chat_sound = escape($_POST['chat_sound']);
    $private_sound = escape($_POST['private_sound']);
    $notify_sound = escape($_POST['notify_sound']);
    $name_sound = escape($_POST['name_sound']);
    // Concatenate sound codes for different categories
    $sound = soundCode('chat', $chat_sound) . soundCode('private', $private_sound) . soundCode('notify', $notify_sound) . soundCode('name', $name_sound);
    // If no sounds are selected, set sound to 0
    if (empty($sound)) {
        $sound = 0;
    }
    // Use prepared statement to prevent SQL injection and update user sound
    $update_stmt = $mysqli->prepare("
        UPDATE boom_users 
        SET user_sound = ? 
        WHERE user_id = ?
    ");
    $update_stmt->bind_param("ii", $sound, $data['user_id']);
    // Execute the query and check if it was successful
    if ($update_stmt->execute()) {
        echo boomCode(1, array('data' => $sound)); // Success
    } else {
        echo boomCode(0); // Error
    }
    die();
}

//UPDATE profile privace
if (isset($_POST['save_shared'])) {
    if (boomLogged()) {
        // Escape input values to prevent SQL injection
        $ashare = escape($_POST['ashare']);
        $sshare = escape($_POST['sshare']);
        $fshare = escape($_POST['fshare']);
        $gshare = escape($_POST['gshare']);
        $lshare = escape($_POST['lshare']);
        // Create an associative array for the update
        $update_privacy = array(
            "ashare" => $ashare,
            "sshare" => $sshare,
            "fshare" => $fshare,
            "gshare" => $gshare,
            "lshare" => $lshare,
        );
        // Call the function to update user data
        $update_query = cl_update_user_data($data['user_id'], $update_privacy);
        // Optionally, handle the result of $update_query, if necessary
        if ($update_query) {
            echo boomCode(1, array('status' => 'Privacy settings updated.'));
        } else {
            echo boomCode(0, array('status' => 'Error updating privacy settings.'));
        }
    }
}

//UPDATE PUSHE NOTIFICATION user_id
if (isset($_POST['update_pushId'])) {
    if (boomLogged()) {
        // Escape the user input to prevent SQL injection
        $push_id = escape($_POST['userId']);
        // Use a prepared statement for the update query
        $update_stmt = $mysqli->prepare("UPDATE boom_users SET push_id = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $push_id, $data['user_id']);
        // Execute the statement
        if ($update_stmt->execute()) {
            echo boomCode(1, array('status' => 'Push ID updated successfully.'));
        } else {
            echo boomCode(0, array('status' => 'Error updating Push ID.'));
        }
    }
}
?>