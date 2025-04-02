<?php
require __DIR__ . "./../function_admin.php";

$time = ceil(time());
$res = array(); 
if ($f == "bot_speakers") {
    $res = array();
	if ($s == "del_bot") {
		$bot_id = cleanString(escape($_POST['bot_id']));
		$res['status'] = 100;
		$db->where('id', $bot_id);
		$del_query = $db->delete('bot_data');
		if ($del_query === true) {
			$res['status'] = 200;
		}
		header("Content-type: application/json");
		echo json_encode($res);
		exit();
	}

    if($s == "admin_bot_byroom"){
    $post_data = cleanString($_POST);
    $group_id = cleanString($post_data['checkbot_room']);
    $bots = bot_list_by_room($group_id);
        if (!empty($bots)) {
          $array_data = $bots; 
         }else{
          $array_data = emptyZone($lang['empty']);
       }    
    header("Content-type: application/json");
	echo json_encode($array_data);
	exit();	    
    }
     if($s == "admin_bot_info"){
         $res = array();
         $post_data = cleanString($_POST);
         $bot_id = cleanString($post_data['bot_id']);
         $group_id = $post_data['group_id'];
         $bot_info = bot_informatin($bot_id,$group_id);
        if (!empty($bot_info)) {
              $array_data = $bot_info; 
             }else{
              $array_data = emptyZone($lang['empty']);
           }          
        header("Content-type: application/json");
    	echo json_encode($array_data);
    	exit();	  

     }
      if($s == "update_bot"){
	    $post_data = cleanString($_POST);
        $time = ceil(time());
        $res['bot_id'] = $post_data['bot_id'];
        $res['bot_user_id'] = $post_data['bot_user_id'];
        $data_bot_query = array(
			"fuse_bot_status" => $post_data['fuse_bot_status'],
			"fuse_bot_type" => $post_data['fuse_bot_type'],
			"reply" 		=> $post_data['fuse_bot_line'],
			"fuse_bot_time" => $time,
			);
		$res['bot_query'] = cl_update_bot_data($res['bot_id'],$data_bot_query);	
        if($res['bot_query'] == true){
				$res['status'] = 200;
				$res['message'] = 'Updated successfully';
			}else{
				$res['status'] = 150;
				$res['message'] = 'Something Wrong';
			}		
        header("Content-type: application/json");
		echo json_encode($res);
		exit();          
      }
     if($s == "add_bot_modal") {
       $res['content'] = boomTemplate('element/bots/add_bot', $res);
       header("Content-type: application/json");
		echo json_encode($res['content']);
		exit();  
     }
 	if ($s == "add_bot") {
		$post_data = cleanString($_POST);
 		$res['user_id']= cleanString(cl_rn_strip(cl_text_secure($post_data['fuse_bot_id'])));
 		$res['group_id']= cleanString(cl_rn_strip(cl_text_secure($post_data['group_id'])));
 		//$res['fuse_bot_delay']= cleanString(cl_rn_strip(cl_text_secure($post_data['fuse_bot_delay'])));
 		$res['fuse_bot_status']= cleanString(cl_rn_strip(cl_text_secure($post_data['fuse_bot_status'])));
 		$res['fuse_bot_type']= cleanString(cl_rn_strip(cl_text_secure($post_data['fuse_bot_type'])));
 		$res['fuse_bot_line']= cleanString($post_data['fuse_bot_line']);
 		$time = ceil(time());
		$data_query = array(
				"reply" => $res['fuse_bot_line'],
				//"fuse_bot_delay" => $res['fuse_bot_delay'],
				"fuse_bot_status" => $res['fuse_bot_status'],
				"fuse_bot_type" => $res['fuse_bot_type'],
				"group_id" => $res['group_id'],
				"user_id" => $res['user_id'],
				"fuse_bot_time" => $time,
				);
		$res['query'] = $db->insert ('bot_data', $data_query);
		if($res['query'] == true){
		$res['status'] = 200;
		$res['message'] = 'Bot has been add successfully';
		}else{
		    $res['status'] = 150;
		    $res['message'] = 'Something Wrong';
		} 
		header("Content-type: application/json");
		echo json_encode($res);
		exit();
	}
	if($s == "update_bot_set"){
		// Ensure input data is sanitized
		$post_data = cleanString($_POST);
		$bot_delay = intval($post_data['bot_delay']); // Ensure it's an integer
		$allow_bot = intval($post_data['allow_bot']); // Ensure it's an integer
		
		// Prepare and execute the update query securely using a prepared statement
		$stmt = $mysqli->prepare("UPDATE boom_setting SET bot_delay = ?, allow_bot = ? WHERE id = 1");
		$stmt->bind_param("ii", $bot_delay, $allow_bot); // Bind parameters as integers
		$stmt->execute();
		
		// Check if the update was successful
		if($stmt->affected_rows > 0){
			return 1; // Successfully updated
		} else {
			return 0; // No changes made
		}
	}
	if($s=="allow_bot"){
	    $post_data = cleanString($_POST);
	    $allow_bot =  $post_data['allow_bot'];
	    $mysqli->query("UPDATE boom_setting SET  allow_bot = '" . $allow_bot . "' WHERE id = '1'");
         return 1;
	}
if ($s == "speak" && $data['allow_bot'] == 1) {
    // Get the current time formatted
    $post_time = date("H:i", $time);
    $bot_time = $data['bot_time'] + $data['bot_delay'];
    //$group_id = $data['user_roomid'];
    if (isset($data['user_roomid'])) {
        $group_id = $data['user_roomid'];
    } else {
        die(json_encode(['status' => 'error', 'message' => 'User room ID is not set']));
    }
    // Check user access and bot status
    if (boomLogged() && $data['allow_bot'] == 1) {
        // Check if the current time is greater than bot time
        if ($time > $bot_time) {
            // Fetch bot data for the given group
            $ckbdata = $mysqli->query("SELECT * FROM `boom_bot_data` WHERE group_id = '$group_id' AND `id` > 0");

            if ($ckbdata && $ckbdata->num_rows > 0) {
                $bot_row = $ckbdata->fetch_array(MYSQLI_ASSOC);

                if ($bot_row['fuse_bot_type'] == 1) {
                    // Fetch the next bot data
                    $findbotdata = $mysqli->query("SELECT * FROM `boom_bot_data` WHERE `view` != 1 AND group_id = '$group_id' ORDER BY `id` ASC LIMIT 1");

                    if ($findbotdata && $findbotdata->num_rows > 0) {
                        $prepare = $findbotdata->fetch_array(MYSQLI_BOTH);
                        $this_ads_bot = $prepare['id'];
                        $bot_info2 = fuse_user_data($prepare['user_id']);

                        // Update the bot data to mark it as viewed
                        $mysqli->query("UPDATE boom_bot_data SET view = 1 WHERE id = '$this_ads_bot' AND group_id = '$group_id'");

                        // Prepare the bot's response
                        $botsay = addslashes($prepare['reply']);
                        $mysqli->query("UPDATE boom_setting SET bot_time = '$time' WHERE id = 1");
                        $botsay = escape($botsay);
                        $botsay = wordFilter($botsay, 1);
                        $botsay = textFilter($botsay);
                        // Create content and post chat
                        $content = '<div class="' . $bot_info2['bccolor'] . ' ' . $bot_info2['bcbold'] . ' ' . $bot_info2['bcfont'] . '">' . $botsay . '</div>';
                        botPostChat($prepare['user_id'], $prepare['group_id'], $content);
                    } else {
                        // Reset the view status if no data found
                        $mysqli->query("UPDATE boom_bot_data SET view = 0 WHERE group_id = '$group_id' AND `id` > 0");
                        $res['status'] = 2; // Indicate that no data was found
                    }
                } else {
                    // Random bot response logic
                    $randomResponseQuery = $mysqli->query("SELECT reply, user_id FROM boom_bot_data WHERE group_id = '$group_id' AND `id` > 0 ORDER BY RAND() LIMIT 1");

                    if ($randomResponseQuery && $randomResponseQuery->num_rows > 0) {
                        $prepare_result = $randomResponseQuery->fetch_array(MYSQLI_BOTH);
                        $botsay = addslashes($prepare_result['reply']);
                        $mysqli->query("UPDATE boom_setting SET bot_time = '$time'");
                        $botsay = escape($botsay);
                        $botsay = wordFilter($botsay, 1);
                        $botsay = textFilter($botsay);

                        $bot_info3 = fuse_user_data($prepare_result['user_id']);
                        $content = '<div class="' . $bot_info3['bccolor'] . ' ' . $bot_info3['bcbold'] . ' ' . $bot_info3['bcfont'] . '">' . $botsay . '</div>';

                        // Insert the bot's message into chat
                        botPostChat($prepare_result['user_id'], $prepare_result['group_id'], $content);
                    } else {
                        // Handle case where no random response is found
                        die(json_encode(['status' => 'error', 'message' => 'No data found']));
                    }
                }
            } else {
                die(json_encode(['status' => 'error', 'message' => 'No data found']));
            }
        } else {
            die(json_encode(['status' => 'error', 'message' => 'Current time is not greater than bot time']));
        }
    } else {
        die(json_encode(['status' => 'error', 'message' => 'User access is not valid or bot status is off']));
    }

    // Send response as JSON
    header("Content-type: application/json");
    echo json_encode($res);
    exit();
}

}	
?>    