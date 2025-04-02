<?php
$res = [];
if ($f == 'login_as') {
	if ($s == 'login_as_username') {
		// Check if necessary data is provided
		if (isset($_POST['owner_switch']) && isset($_POST['user_id'])) {
			$user_id = escape($_POST['user_id']);  // Escape user_id to prevent SQL injection
			
			// Fetch the user details by user_id
			$validate = $mysqli->query("SELECT * FROM boom_users WHERE user_id = '" . $user_id . "'");
			
			if ($validate->num_rows > 0) {
				$user = $validate->fetch_assoc();
				
				// Set cookies to log in as the selected user
				setBoomCookie($user['user_id'], $user['user_password']);
				
				// Store the owner session before switching
				if (!isset($_SESSION['original_owner_id'])) {
					$_SESSION['original_owner_id'] = $data['user_id'];           // Store original owner user_id
					$_SESSION['original_owner_password'] = $data['user_password']; // Store original owner password
					$_SESSION['original_owner_name'] = $data['user_name'];       // Store original owner user_name
				}
				
				// Set the switched user session
				$_SESSION['switched_user_id'] = $user['user_id'];
				$_SESSION['switched_user_name'] = $user['user_name'];

				// Return success and message to switch back to the owner
				$res = [
					'status' => "success",
					'redirect_url' => "index.php", // URL to redirect to
					'message' => "You are now logged in as " . htmlspecialchars($user['user_name']) . 
								 ". You will be redirected to the <a href='index.php'>home page</a>."
				];
			} else {
				// Return failure status if user is not found
				$res['status'] = "failure";
				$res['message'] = "User not found.";
			}
		} else {
			// Return failure status if data is missing
			$res['status'] = "missing_data";
		}

		// Return JSON response
		header("Content-type: application/json");
		echo json_encode($res);
		exit();
	}


    // Handle restoring the owner session
    if ($s == 'restore_owner' && isset($_SESSION['original_owner_id'])) {
        // Check if original owner password is set before using it
        if (isset($_SESSION['original_owner_password'])) {
            // Restore cookies to log back in as the original owner
            setBoomCookie($_SESSION['original_owner_id'], $_SESSION['original_owner_password']);
            
            // Clear the switched user session data
            unset($_SESSION['switched_user_id']);
            unset($_SESSION['switched_user_name']);
            
            // Optionally, remove the original owner session once switched back
            unset($_SESSION['original_owner_id']);
            unset($_SESSION['original_owner_password']);

            // Return success response with redirect URL
            $res = [
                'status' => "success",
                'redirect_url' => "admin.php" // URL to redirect to
            ];
			header('Location: '.$res['redirect_url']); // Redirect back to login page
			exit();			
        } else {
            // Handle the case where the original owner password is not set
            $res['status'] = "error";
            $res['message'] = "Original owner password is not set. Please log in again.";
        }

        // Return JSON response
        header("Content-type: application/json");
        echo json_encode($res);
        exit();
    }
}
?>
