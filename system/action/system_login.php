<?php
if ($f == 'system_login') {
	$res =[];
		if ($s == 'member_login') {
			$res['code'] = 2;
			$input_password = escape($_POST["password"]);
			$username = escape($_POST["username"]);
			$user_ip = getIp();

			if (empty($input_password) || empty($username)) {
				$res['msg'] = 'Bad login';
				$res['code'] = 1;
				echo fu_json_results($res);
				exit;
			}

			// Fetch user data based on username or email
			if (isEmail($username)) {
				$query = "SELECT * FROM boom_users WHERE user_email = ?";
			} else {
				$query = "SELECT * FROM boom_users WHERE user_name = ?";
			}

			$stmt = $mysqli->prepare($query);
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$validate = $stmt->get_result();

			if ($validate->num_rows > 0) {
				$valid = $validate->fetch_assoc();
				$db_password = $valid["user_password"];
				$id = $valid["user_id"];

				// Check if password is using old encryption (SHA-1 or similar)
				if (strlen($db_password) === 40 || strpos($db_password, '$2y$') !== 0) {
					// Likely SHA-1 or an old hash, verify and upgrade to bcrypt
					if (encrypt($input_password) === $db_password) {
						// ✅ Password is correct, upgrade to bcrypt
						$new_hash = password_hash($input_password, PASSWORD_BCRYPT);
						$mysqli->query("UPDATE boom_users SET user_password = '" . $new_hash . "' WHERE user_id = '" . $id . "'");
						$db_password = $new_hash;
					}
				}

				// Validate bcrypt password
				if (password_verify($input_password, $db_password)) {
					$post_time = date("H:i", time());
					$ssesid = $valid["session_id"] + 1;

					$mysqli->query("UPDATE boom_users SET user_ip = '" . $user_ip . "', session_id = '" . $ssesid . "', join_msg = '0', user_roomid = '0' WHERE user_id = '" . $id . "'");

					// Set the session token cookie
					setBoomCookie($id, $db_password);

					// Set a secure login session
					$_SESSION['user_id'] = $id;
					session_regenerate_id(true); // Regenerate session ID

					$res['code'] = 3;
					$res['msg'] = "You have been logged in successfully";
					$res['reload_delay'] = 2; // Delay before reload
				} else {
					$res['code'] = 1;
					$res['msg'] = "Invalid password";
				}
			} else {
				$res['code'] = 1;
				$res['msg'] = "User not found";
			}

			echo fu_json_results($res);
		}
			if ($s == 'guest_login') {
				$res = [];
				$res['code'] = 1;
				$res['guest_lang'] = getLanguage();
				$res['guest_ip'] = getIp();
				$create = 0;

				if (!allowGuest()) {
					$res['code'] = 0;
				}
				if (!boomCheckRecaptcha()) {
					$res['code'] = 6;
				}
				if (!okGuest($res['guest_ip'])) {
					$res['code'] = 16; // Prevent new guest login if already exists
				}

				$res['guest_name'] = trim(escape($_POST["guest_name"]));
				$res['guest_gender'] = trim(escape($_POST["guest_gender"]));
				$res['guest_age'] = trim(escape($_POST["guest_age"]));

				if (!validName($res['guest_name'])) {
					$res['code'] = 4;
				}
				if (!boomUsername($res['guest_name'])) {
					$res['code'] = 5;
				}
				if (guestForm()) {
					if (!validAge($res['guest_age'])) {
						$res['code'] = 13;
					}
					if (!validGender($res['guest_gender'])) {
						$res['code'] = 14;
					}
				}

				// Prevent new guest creation if already exists
				if ($res['code'] == 1) {
					$guest_user = [
						"name" => $res['guest_name'],
						"password" => randomPass(),
						"language" => $res['guest_lang'],
						"ip" => $res['guest_ip'],
						"rank" => 0,
						"avatar" => "default_guest.png",
						"email" => ""
					];

					if (guestForm()) {
						$guest_user["age"] = $res['guest_age'];
						$guest_user["gender"] = $res['guest_gender'];
					}

					$user = boomInsertUser($guest_user);
					if (empty($user)) {
						$res['code'] = 2;
					}
				}

				header("Content-type: application/json");
				echo json_encode($res);
				exit();
			}
		if ($s == 'system_register') {
			$res = [];
			$user_ip = getIp();
			$user_name = escape($_POST["username"]);
			$user_password = escape($_POST["password"]);
			$dlang = getLanguage();
			$user_email = escape($_POST["email"]);
			$user_gender = escape($_POST["gender"]);
			$user_age = escape($_POST["age"]);
			$recaptcha = isset($_POST['recaptcha']) ? $_POST['recaptcha'] : '';
			$referrer_id = isset($_POST['referrer_id']) ? intval(escape($_POST['referrer_id'])) : NULL;

			// Check for empty fields
			if (empty($user_password) || empty($user_name) || empty($user_email)) {
				$res['code'] = 3;  // Empty field validation
				$res['msg'] = 'All fields are required';
				echo fu_json_results($res);
				exit;
			}

			// Validate username, password, and email not being only whitespace
			if (preg_match('/^\s+$/', $user_name) || preg_match('/^\s+$/', $user_password) || preg_match('/^\s+$/', $user_email)) {
				$res['code'] = 3;
				$res['msg'] = 'Fields cannot contain only whitespace';
				echo fu_json_results($res);
				exit;
			}

			// Validate reCAPTCHA
			if ($recaptcha > 0 && empty($recaptcha)) {
				$res['code'] = 7;  // Missing reCAPTCHA
				$res['msg'] = 'Please complete the reCAPTCHA';
				echo fu_json_results($res);
				exit;
			}

			// Further validation checks (username, email, password, etc.)
			if (!validName($user_name)) {
				$res['code'] = 4; // Invalid username
				$res['msg'] = 'Invalid username';
				echo fu_json_results($res);
				exit;
			}

			if (!validEmail($user_email)) {
				$res['code'] = 6; // Invalid email
				$res['msg'] = 'Invalid email format';
				echo fu_json_results($res);
				exit;
			}

			if (!checkEmail($user_email)) {
				$res['code'] = 10; // Email already exists
				$res['msg'] = 'Email already exists';
				echo fu_json_results($res);
				exit;
			}

			if (!boomValidPassword($user_password)) {
				$res['code'] = 17; // Short password
				$res['msg'] = 'Password is too short';
				echo fu_json_results($res);
				exit;
			}

			if (!validAge($user_age)) {
				$res['code'] = 13; // Invalid age
				$res['msg'] = 'Please select a valid age';
				echo fu_json_results($res);
				exit;
			}

			if (!validGender($user_gender)) {
				$res['code'] = 14; // Invalid gender
				$res['msg'] = 'Please select a gender';
				echo fu_json_results($res);
				exit;
			}

			if (!boomOkRegister($user_ip)) {
				$res['code'] = 16; // Max registration attempts or system issue
				$res['msg'] = 'Registration is temporarily unavailable';
				echo fu_json_results($res);
				exit;
			}

			if (!boomUsername($user_name)) {
				$res['code'] = 5; // Username already exists
				$res['msg'] = 'Username already exists';
				echo fu_json_results($res);
				exit;
			}

			// Encrypt password
			$user_password = encrypt($user_password);
			
			// Insert new user into the database
			$system_user = [
				"name" => $user_name,
				"password" => $user_password,
				"email" => $user_email,
				"language" => $dlang,
				"gender" => $user_gender,
				"avatar" => genderAvatar($user_gender),
				"age" => $user_age,
				"verify" => $data["activation"],
				"ip" => $user_ip
			];

			$user = boomInsertUser($system_user);

			if (empty($user)) {
				$res['code'] = 0;  // Registration is closed or failed
				$res['msg'] = 'Registration failed';
				echo fu_json_results($res);
				exit;
			}

			$res['code'] = 1;  // Registration successful
			$res['msg'] = 'You have been successfully registered';
			echo fu_json_results($res);
			exit;
		}
	
}


?>