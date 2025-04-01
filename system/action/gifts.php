<?php
$gift_array = array();
$data_array = array();
    // Function to handle errors
function handleError($code, $message) {
   header("Content-type: application/json");
    echo json_encode(['code' => $code, 'message' => $message]);
     exit();
}

if ($f == 'gifts') {
   if ($s == 'gifts_access') {
         if(isset($_POST['set_gifts_access']) && boomAllow($cody['can_manage_addons'])){
        	$gifts_access = escape($_POST['set_gifts_access']);
        	$update = $mysqli->query("UPDATE boom_addons SET addons_access = '$gifts_access' WHERE addons = 'gifts'");
        	if($update){
            echo 5;
        	die();
   	    
        	}
        }       
            
        if(isset($_POST['set_use_gift']) && boomAllow($cody['can_manage_addons'])){
            $use_gift = escape($_POST['set_use_gift']);
           $update =  $mysqli->query("UPDATE boom_setting SET use_gift = '" . $use_gift . "' WHERE id = '1'");
            if($update){
                echo 5;
        	    die();
        	}           
        }
   } 
   if ($s == 'search_box') {
        if (isset($_POST['search_box'], $_POST['q'])){
            $text = escape($_POST['q']);
           $search_query = runGiftSearch($text);
            header("Content-type: application/json");
            echo json_encode($search_query);
            exit(); 
        }        
    }
if ($s === 'send_gift') {
    // Check for conditions that prevent sending a gift
    if (checkFlood()) {
        echo json_encode(['status' => 100, 'msg' => 'Flood detected']);
        exit();
    }
    
    if (muted() || isRoomMuted($data)) {
        exit(); // User is muted or room is muted
    }

    if (!canGift()) {
        exit(); // User doesn't have permission to send gifts
    }

    // Validate required POST parameters
    if (isset($_POST['type'], $_POST['target'], $_POST['gift_id'])) {
        // Sanitize inputs
        $gift_array = [
            'type' => escape($_POST['type']),
            'target_id' => escape($_POST['target']),
            'gift_id' => escape($_POST['gift_id']),
        ];

        // Fetch target user details
        $gift_array['target'] = userDetails($gift_array['target_id']);
        if (empty($gift_array['target'])) {
            exit(); // Target user not found
        }

        // Prevent sending gifts to self
        if (mySelf($gift_array['target']['user_id'])) {
            exit(); // Cannot send a gift to oneself
        }

        // Get sender and target user details
        $my_points = $data['user_gold'];
        $my_userId = $data['user_id'];
        $receiver_id = $gift_array['target']['user_id'];
        $gift_array['my_name'] = $data['user_name'];

        // Fetch gift details
        $compare_credit = gift_list_byId($gift_array['gift_id']);
        $gift_thumb = $compare_credit['gift_url'];
        $gift_price = $compare_credit['gift_cost'];

        // Check if the gift is valid and affordable
        if ($compare_credit > 0) {
            if ($gift_price <= $my_points) {
                // Update points for sender and receiver
                $sum_points = $my_points - $gift_price;
                $divide_price = ($gift_price / 2) + $gift_array['target']['user_gold'];

                $update_sender = $mysqli->query("UPDATE `boom_users` SET `user_gold` = '$sum_points' WHERE `user_id` = '$my_userId'");
                $update_receiver = $mysqli->query("UPDATE `boom_users` SET `user_gold` = '$divide_price' WHERE `user_id` = '$receiver_id'");

                // Record the gift transaction
                $insert_gift_record = [
                    "target_id" => $receiver_id,
                    "gift_id" => $gift_array['gift_id'],
                    "room_id" => $data['user_roomid'],
                    "hunter_id" => $my_userId,
                ];
                $update_record = record_gift($insert_gift_record);

                // Notify chat of the gift
                $content = giftContentSendedOk($compare_credit, $data['user_name'], $gift_array['target']['user_name']);
                systemPostChat($data['user_roomid'], $content);
                boomNotify("gift", array("hunter" => $my_userId, "target" => $receiver_id, "source" => 'gift' ,"custom" => $compare_credit['gift_title'],"icon" => 'gift'));
  		    // Check if the user is inactive
    		    $last_active = $gift_array['target']['last_active'];
    		    $current_time = time();
    		    $inactive_time = 60; // 1 minute
                if(($current_time - $last_active) > $inactive_time){
    		        // User is inactive, send a notification
    		        $notification_msg = $data['user_name'].' Sent you Gift 💖';
    		        sendNotification($gift_array['target']['push_id'], $notification_msg);
    		    }      	
               
                $data_array['status'] = 200;
                $data_array['gift_data'] = $compare_credit;
                $data_array['msg'] = 'The gift has been sent successfully';
                $data_array['cl'] = 'success';
                
            } else {
                $data_array['status'] = 300;
                $data_array['msg'] = 'You do not have enough credit.';
                $data_array['cl'] = 'warning';
            }
        }

        // Send response as JSON
        header("Content-type: application/json");
        echo json_encode($data_array);
        exit();
    }
}

     if ($s == 'public_box') {
        $res['content']= boomTemplate('gifts/public_gift_panel', $data);
         header("Content-type: application/json");
        echo json_encode($res);
        exit();
     }
      if ($s == 'my_gift') {
        $res['content']= boomTemplate('gifts/my_gift', $data);
         header("Content-type: application/json");
        echo json_encode($res);
        exit();
          
      }
      if ($s == 'getUserGift') {
        $user_id =  escape($_POST['user_id']);
        if (!empty($user_id)) {
            $res['content']= boomTemplate('gifts/my_gift', $user_id);
        } else {
            // Handle the case where user_id is empty or null
             $res['status'] = 0;
            return;
        }          
         header("Content-type: application/json");
        echo json_encode($res);
        exit();
          
      }      
      if ($s == 'gift_panel') {
        $res['content']= boomTemplate('gifts/gift_panel', $data);
         header("Content-type: application/json");
        echo json_encode($res);
        exit();
          
      } 
    if ($s == 'admin_save_gift') {
    if (isset($_POST['save_gift'], $_POST['gift_title'])) {
        // Sanitize inputs
        $gift_id = escape($_POST['save_gift']);      // Gift ID
        $gift_title = escape($_POST['gift_title']);  // Gift title
        $gift_rank = escape($_POST['gift_rank']);    // Gift rank
        $gift_gold = escape($_POST['gift_gold']);    // Gift cost (gold)

        // Initialize file variables
        $thumb_file_path = '';
        $gif_file_path = '';
        $uploadDir = 'system/gifts/files/media/gift_box/'; // Directory for thumbnail files
        $gifUploadDir = 'system/gifts/files/media/gift_box/gif/'; // Directory for GIF files

        // Fetch the current file paths from the database
        $db->where('id', $gift_id);
        $existingGift = $db->getOne('gift', ['gift_image', 'gif_file']);

        // Ensure the GIF directory exists
        if (!file_exists($gifUploadDir)) {
            mkdir($gifUploadDir, 0755, true);
        }

        // Handle thumb_file upload if submitted
        if (isset($_FILES['thumb_file']) && $_FILES['thumb_file']['error'] === UPLOAD_ERR_OK) {
            // File properties for thumb_file
            $fileTmpPath = $_FILES['thumb_file']['tmp_name'];
            $fileType = $_FILES['thumb_file']['type'];
            $fileSize = $_FILES['thumb_file']['size'];

            // Define allowed file types and size limits
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 5 * 1024 * 1024; // 5 MB

            // Validate file type
            if (!in_array($fileType, $allowedTypes)) {
                handleError(1, 'Invalid thumbnail file type.');
            }

            // Validate file size
            if ($fileSize > $maxFileSize) {
                handleError(2, 'Thumbnail file size exceeds the limit.');
            }

            // Generate a unique file name
            $fileName = 'thumb_' . uniqid() . '.' . pathinfo($_FILES['thumb_file']['name'], PATHINFO_EXTENSION);

            // Define the full path for the uploaded file
            $thumb_file_path = $uploadDir . $fileName;

            // Move the uploaded file to the designated directory
            if (move_uploaded_file($fileTmpPath, $thumb_file_path)) {
                // Delete the old thumbnail if it exists
                if (!empty($existingGift['gift_image'])) {
                    $oldThumbPath = $uploadDir . basename($existingGift['gift_image']);
                    if (file_exists($oldThumbPath)) {
                        unlink($oldThumbPath); // Delete the old file
                    }
                }
            } else {
                handleError(3, 'Failed to move the thumbnail file.');
            }

            // Prepare the file path for database storage
            $thumb_file_path = 'gift_box/' . $fileName;
        }

        // Handle gif_file upload if submitted
        if (isset($_FILES['gif_file']) && $_FILES['gif_file']['error'] === UPLOAD_ERR_OK) {
            // File properties for gif_file
            $fileTmpPath = $_FILES['gif_file']['tmp_name'];
            $fileType = $_FILES['gif_file']['type'];
            $fileSize = $_FILES['gif_file']['size'];

            // Define allowed file types and size limits for GIFs
            $allowedGifTypes = ['image/gif'];
            $maxFileSize = 10 * 1024 * 1024; // 10 MB (as GIFs might be larger)

            // Validate file type
            if (!in_array($fileType, $allowedGifTypes)) {
                handleError(1, 'Invalid GIF file type.');
            }

            // Validate file size
            if ($fileSize > $maxFileSize) {
                handleError(2, 'GIF file size exceeds the limit.');
            }

            // Generate a unique file name
            $gifFileName = 'gif_' . uniqid() . '.' . pathinfo($_FILES['gif_file']['name'], PATHINFO_EXTENSION);

            // Define the full path for the uploaded GIF file
            $gif_file_path = $gifUploadDir . $gifFileName;

            // Move the uploaded file to the designated directory
            if (move_uploaded_file($fileTmpPath, $gif_file_path)) {
                // Delete the old GIF if it exists
                if (!empty($existingGift['gif_file'])) {
                    $oldGifPath = $gifUploadDir . basename($existingGift['gif_file']);
                    if (file_exists($oldGifPath)) {
                        unlink($oldGifPath); // Delete the old file
                    }
                }
            } else {
                handleError(3, 'Failed to move the GIF file.');
            }

            // Prepare the GIF file path for database storage
            $gif_file_path = 'gift_box/gif/' . $gifFileName;
        }

        // Prepare data for updating the gift
        $updata = Array (
            'gift_title' => $gift_title,
            'gift_cost' => $gift_gold,
            'gift_rank' => $gift_rank,
            'time' => time(),
        );

        // Add file paths if new files were uploaded
        if (!empty($thumb_file_path)) {
            $updata['gift_image'] = $thumb_file_path;
        }
        if (!empty($gif_file_path)) {
            $updata['gif_file'] = $gif_file_path;
        }

        // Update the gift in the database
        $db->where('id', $gift_id);
        $update = $db->update('gift', $updata);

        // Check if the update was successful
        if ($update === true) {
            $gift_array['code'] = 200;
            $gift_array['id'] = $gift_id;
            $gift_array['message'] = 'Gift Updated successfully';
            $gift_array['data'] = boomTemplate('element/admin_gift', giftDetails($gift_id));
        }

        // Return JSON response
        header("Content-type: application/json");
        echo json_encode($gift_array);
        exit();
    }
}
  

// Check if the script should handle file uploads
if ($s == 'admin_add_gift') {
    // Define the directory to store uploaded files
    $uploadDir = 'system/gifts/files/media/gift_box/';
    // Create the upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }


    // Check if a file is uploaded
    if (isset($_FILES['thumb_file']) && $_FILES['thumb_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['thumb_file']['tmp_name'];
        $fileSize = $_FILES['thumb_file']['size'];
        $fileType = $_FILES['thumb_file']['type'];
        // Define the file name with a unique identifier and a fixed prefix
        $fileName = 'thumb_' . uniqid() . '.' . pathinfo($_FILES['thumb_file']['name'], PATHINFO_EXTENSION);
        // Define allowed file types and size limits
        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        if (!in_array($fileType, $allowedTypes)) {
            handleError(1, 'Invalid file type jpeg or png only in thumb file');
        }

        if ($fileSize > $maxFileSize) {
            handleError(2, 'File size exceeds the limit.');
        }

        // Move the file to the desired directory
        $destPath = $uploadDir . $fileName;
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Prepare the gift data
            $add_gift_array = [
                "gift_title" => "New Gift",
                "gift_image" => "gift_box/" . $fileName,
                "gift_method" => '1',
                "gift_cost" => '100',
                "gift_rank" => '1',
                "video_file" => '',
                "gif_file" => '',
                "time" => time(),
            ];

            // Insert the gift data into the database
            $add_gift_query = $db->insert('gift', $add_gift_array);

            if ($add_gift_query) {
                // Retrieve the last inserted ID
                $lastInsertId = $add_gift_query;
                header("Content-type: application/json");
                echo json_encode([
                    'code' => 5,
                    'last_id' => $lastInsertId,
                    'data' =>  boomTemplate('element/admin_gift', giftDetails($lastInsertId)),
                ]);
            } else {
                handleError(5, 'Failed to insert data into the database.');
            }
        } else {
            handleError(3, 'Failed to move the uploaded file.');
        }
    } else {
        handleError(4, 'No file uploaded or there was an upload error.');
    }
}


if ($s == 'admin_delete_gift') {
    // Ensure that the gift ID is provided
    if (isset($_POST['gift_id']) && !empty($_POST['gift_id'])) {
        $giftId = intval($_POST['gift_id']);
        
        // Fetch the gift data to get the file paths for both gift_image and gif_file
        $gift = $db->where('id', $giftId)->getOne('gift', ['gift_image', 'gif_file']);
        
        if($gift) {
            // Define the directories for uploaded files
            $uploadDir = 'system/gifts/files/media/gift_box/';
            $gifUploadDir = 'system/gifts/files/media/gift_box/gif/';
            
            // Prepare file paths for both thumbnail and gif
            $thumbFilePath = $uploadDir . $gift['gift_image'];
            $gifFilePath = $gifUploadDir . $gift['gif_file'];

            // Check if the thumbnail file exists and delete it
            if (!empty($gift['gift_image']) && file_exists($thumbFilePath)) {
                unlink($thumbFilePath);
            }

            // Check if the gif file exists and delete it
            if (!empty($gift['gif_file']) && file_exists($gifFilePath)) {
                unlink($gifFilePath);
            }

            // Delete the record from the database
            $deleteGiftResult = $db->where('id', $giftId)->delete('gift');
            $deleteUserGiftResult = $db->where('gift', $giftId)->delete('users_gift');
            if ($deleteGiftResult || $deleteUserGiftResult) {
                header("Content-type: application/json");
                echo json_encode([
                    'code' => 5,
                    'message' => 'Gift has been deleted successfully.',
                    'thumbFilePath' => $thumbFilePath,
                    'gifFilePath' => $gifFilePath
                ]);
            } else {
                handleError(5, 'Failed to delete the gift from the database.');
            }
        } else {
            handleError(6, 'Gift not found.');
        }
    } else {
        handleError(7, 'No gift ID provided.');
    }
}


   
}
?>