<?php


require __DIR__ . "../../../config_session.php";
if (!boomAllow(100)) {
    exit;
}
function users_withPush($push_id){
    global $data, $cody, $db;
     // Ensure $push_id is safely included in the query
    $push_id = '%' . $db->escape($push_id) . '%'; // Add wildcard characters and escape the input
    // Use LIKE operator for pattern matching
    $db->where('push_id', $push_id, 'LIKE');
    $u_data = $db->getOne('users');
    // Check if no data is returned or if the data is null
    if (empty($u_data)) {
        return [
            'data' => null,
            'message' => 'No user data found for push_id: ' . htmlspecialchars($push_id)
        ];
    }
    
    return [
        'data' => $u_data,
        'message' => ''
    ];
}

$appId =$data['onesignal_web_push_id']; // Replace with your OneSignal App ID
$restApiKey = $data['onesignal_web_reset_key']; // Replace with your OneSignal REST API Key
$subscribers = getAllSubscribers($appId, $restApiKey);

?>
   <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .pagination {
            margin: 20px 0;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }
    </style>
<div class="page_indata">
	<div id="page_wrapper">
		<div class="page_full">
				<?php  echo elementTitle($lang["notification"]); ?>
		</div>
		<div class="page_full">
			<div>
				<div class="tab_menu">
					<ul>
						<li class="tab_menu_item tab_selected" data="main_tab" data-z="main_zone">Main</li>
						<li class="tab_menu_item" data="main_tab" data-z="maint_zone">Mass Notifications</li>
					</ul>
				</div>
			</div>
			<div id="main_tab">
				<div id="main_zone" class="tab_zone" style="display: block;">
					<div class="page_element">
						<div class="boom_form">
							<div class="setting_element">
								<p class="label">OneSignal APP ID</p>
								<input id="onesignal_web_push_id" class="full_input" value="<?php echo $data["onesignal_web_push_id"]; ?>" type="text" />
								
							</div>
							<div class="setting_element">
								<p class="label">REST API Key</p>
								<input id="onesignal_web_reset_key" class="full_input" value="<?php echo $data["onesignal_web_reset_key"]; ?>" type="text" />
								
							</div>						
						
							<div class="setting_element">
								<p class="label">Enable Push Notifications</p>
								<select id="allow_onesignal">
									<option value="1">yes</option>
									<option value="0">no</option>
								</select>

							</div>
						</div>
						<button data="setting_notifications" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> Save</button>
					</div>
				</div>
				<div id="maint_zone" class="tab_zone hide_zone" style="display: none;">
					<div class="page_element">
					    <form id="mass_notifications">
						<div class="boom_form">
							<div class="setting_element">
								<p class="label">Send Mass Notifications</p>
                                <input type="hidden" name="token" value="<?php echo setToken(); ?>" />
                                     <textarea class="full_textarea medium_textarea" name="mass_message" id="mass_message" rows="3" cols="80" maxlength="1000"></textarea>
							</div>
						</div>
						</form>
						<button id="send_notifications" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> Send</button>
						<div class="setting_element">
                        <table>
                            <thead>
                                <tr>
                                    <th>Player Name</th>
                                    <th>Player ID</th>
                                    <th>Last Active</th>
                                    <th>Device Type</th>
                                </tr>
                            </thead>
                            <tbody>
                               <?php 
                                foreach ($subscribers as $subscriber) { 
                                $user_in =  users_withPush($subscriber['id']);
                                ?>
                                    <tr>
                                        <td>
                                          <?php  
                                          if (isset($user_in['data']) && !is_null($user_in['data']) && isset($user_in['data']['user_name'])) {
                                                echo htmlspecialchars($user_in['data']['user_name']);
                                            } else {
                                                echo 'User is not exist Database';
                                            }?>
                                            
                                        </td>
                                        <td><?php echo htmlspecialchars($subscriber['id']); ?></td>
                                        <td><?php echo htmlspecialchars($subscriber['last_active']); ?></td>
                                        <td><?php echo htmlspecialchars($subscriber['device_type']); ?></td>
                                    </tr>
                               <?php 
                            }
                        ?>
                            </tbody>
                        </table>
                                                 
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).on('click', '#send_notifications', function(e) {
    e.preventDefault();
    var $this = $(this);
    // Check if the button is already disabled
    if ($this.attr('disabled')) {
        return; // Prevent further clicks if already disabled
    }
    $this.attr('disabled', true); // Disable the button
    var data = new FormData($('#mass_notifications')[0]);
    var url = 'requests.php?f=one_signal&s=mass_notifications';
    $.ajax({
        url: url,
        data: data,
        type: "POST",
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.status == 200) {
                callSaved(system.saved, 1);
            } else {
                callSaved(system.error, 3);
            }
        },
        complete: function() {
            // Re-enable the button after the request is complete
            $this.attr('disabled', false);
        }
    });
    
    return false;
});
</script>
