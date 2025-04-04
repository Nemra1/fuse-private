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
/* Table styles */
table {
    width: 100%;
    border-collapse: collapse;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

table, th, td {
    border: 1px solid #ddd;
}

/* Header styles */
th {
    background-color: #4CAF50;
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
    font-size: 16px;
    text-transform: uppercase;
}

/* Cell styles */
td {
    padding: 12px;
    text-align: left;
    font-size: 14px;
    background-color: #f9f9f9;
    color: black;          /* Text color set to black */
    font-weight: bold;     /* Make text bold */
}

/* Hover effect for table rows */
tr:hover {
    background-color: #f1f1f1;
}

/* Pagination styles */
.pagination {
    display: flex;
    justify-content: center;
    margin: 20px 0;
}

.pagination a {
    padding: 10px 16px;
    margin: 0 5px;
    text-decoration: none;
    background-color: #f1f1f1;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    font-weight: bold;
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Active page styles */
.pagination a.active {
    background-color: #4CAF50;
    color: white;
    border: 1px solid #4CAF50;
}

/* Hover effect for pagination links */
.pagination a:hover {
    background-color: #ddd;
    color: #333;
}

/* Disabled page styles */
.pagination a.disabled {
    background-color: #f9f9f9;
    color: #aaa;
    cursor: not-allowed;
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
								 <?php echo yesNo($data["allow_onesignal"]); ?>
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
                                                echo 'User Deleted';
                                            }?>
                                            
                                        </td>
                                        <td><?php echo htmlspecialchars($subscriber['id']); ?></td>
                                        <td><?php echo htmlspecialchars(chatDate($subscriber['last_active'])); ?></td>
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
