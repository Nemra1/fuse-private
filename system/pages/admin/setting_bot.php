<?php

require __DIR__ . "../../../config_admin.php";
if (!boomAllow(100)) {
    exit;
}
function bot_list_room(){
    global $data, $db,$lang;
	$res = array();
    $bots = $db->get('bot_data');
    if ($db->count > 0){
		foreach ($bots as $bot){
			$userdata =  userDetails($bot['user_id']);
			 $v['id'] =  cleanString($bot['id']);
			 $v['bot_name'] =  $userdata['user_name'];
			 $v['user_tumb'] =  $userdata['user_tumb'];
			 $v['reply'] =  cleanString($bot['reply']);
			 $v['view'] =  cleanString($bot['view']);
			 $v['fuse_bot_status'] =  cleanString($bot['fuse_bot_status']);
			 $v['fuse_bot_time'] =  cleanString($bot['fuse_bot_time']);
			 $v['fuse_bot_type'] =  cleanString($bot['fuse_bot_type']);
			 $v['user_id'] =  cleanString($bot['user_id']);
			 $v['group_id'] =  cleanString($bot['group_id']);
        	 $v['user_font'] 		= $userdata['user_font'];
        	 $v['user_color'] 		= $userdata['user_color'];
        	 $v['bccolor'] =  	    $userdata['bccolor'];
        	 $v['bcbold'] =  		$userdata['bcbold'];
        	 $v['bcfont'] =  		$userdata['bcfont'];
			 $res[] = $v;
		}
		
    }
    return $res;	
}

 $rooms['list'] =  bot_adminRoomList();
  $rooms['bots'] =  bot_list_room();
?>
<style>
.sub_list_name {
    display: flex;
}   
</style>
<div class="page_indata">
	<div id="page_wrapper">
		<div class="page_full">
			<?php  echo elementTitle('Bot Settings'); ?>
		</div>
		<div class="page_full">
			<div class="page_element">
				<button onclick="createBot();" class="theme_btn bmargin10 reg_button"><i class="ri-save-3-fill"></i> Add Bot</button>
				<div class="setting_element">
                    	<label for="allow_bot" class="label">allow Bot</label>
            			<select class="form-control" id="allow_bot" name="allow_bot">
            						<option value="0" <?php if($data['allow_bot']==0){echo "selected";} ?>>no</option>
            						<option value="1" <?php if($data['allow_bot']==1){echo "selected";} ?>>yes</option>
            				</select>
            		</div>    
            		<div class="setting_element">
            			<label for="fuse_bot_delay" class="label">Bot display speed</label>
            			<select class="form-control" id="fuse_bot_delay" name="fuse_bot_delay">
            						<option value="60" <?php if($data['bot_delay']==60){echo "selected";} ?>>1 min</option>
            						<option value="120" <?php if($data['bot_delay']==120){echo "selected";} ?>>2 min</option>
            						<option value="180"<?php if($data['bot_delay']==180){echo "selected";} ?>>3 min</option>
            						<option value="240" <?php if($data['bot_delay']==240){echo "selected";} ?>>4 min</option>
            						<option value="300"  <?php if($data['bot_delay']==300){echo "selected";} ?>>5 min</option>
            						<option value="600" <?php if($data['bot_delay']==600){echo "selected";} ?>>10 min</option>
            						<option value="900" <?php if($data['bot_delay']==900){echo "selected";} ?>>15 min</option>
            						<option value="1200" <?php if($data['bot_delay']==1200){echo "selected";} ?>>20 min</option>
            						<option value="1500" <?php if($data['bot_delay']==1500){echo "selected";} ?>>25 min</option>
            						<option value="1800"  <?php if($data['bot_delay']==1800){echo "selected";} ?>>30 min</option>
            				</select>
            		</div>
		<div class="setting_element">
					<p class="label">Advance search</p>
					<select id="bot_rooms">
						<option value="0" selected="" disabled="">Select Room</option>
						<?php
						foreach ($rooms['list'] as $key) { ?>
						<option value="<?php echo $key['room_id']; ?>"><?php echo $key['room_name']; ?></option>
						 <?php 
                            }
                        ?>
					</select>
				</div>
			</div>
			<div class="page_full" id="bots_list">
				<div class="page_element">
				    <?php
						foreach ($rooms['bots'] as $key) { ?>
					<div class="sub_list_item" id="bot_line_<?php echo $key['id']; ?>">
						<div class="sub_list_avatar">
							<img class="admin_user28" src="<?php echo myAvatar($key['user_tumb']); ?>" />
							<img class="sub_list_active" src="https://drop200.net/default_images/icons/active.svg" />
						</div>
						<div class="sub_list_name">
							<p class="username <?php echo $key['user_color']; ?>"><?php echo $key['bot_name']; ?></p>
						</div>
						<div class="sub_list_name">
							<p class="username user"><?php echo $key['reply']; ?></p>
						</div>
						<div onclick="getBot_info(<?php echo $key['id']; ?>,<?php echo $key['group_id']; ?>);" class="sub_list_option">
							<i class="ri-settings-2-line edit_btn"></i>
						</div>
						<div onclick="del_bot(<?php echo $key['id']; ?>);" class="sub_list_option">
							<i class="ri-close-circle-line edit_btn"></i>
						</div>
					</div>
					 <?php 
                            }
                        ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).on('change', '#bot_rooms', function(){
		var checkbot_room = $(this).val();
		if(checkbot_room == 0){
			return false;
		}
		else {
			$.post(FU_Ajax_Requests_File(), {
			    f:'bot_speakers',
			    s:'admin_bot_byroom',
				checkbot_room: $(this).val(),
				token: utk,
				}, function(response) {
					$('#bots_list').html(response);
			});
		}
	});   
	getBot_info = function(id,room_id){
			var bot_id = id;
			if(bot_id == 0){
				return false;
			}
			else {
				$.post(FU_Ajax_Requests_File(), {
					f:'bot_speakers',
					s:'admin_bot_info',
					bot_id: bot_id,
					group_id :room_id,
					token: utk,
					}, function(response) {
						showModal(response, 500);
				});
			}
	   
	}
	$(document).on('change', '#fuse_bot_delay', function(){
		var bot_delay = $(this).val();
		if(bot_delay == 0){
			return false;
		}
		else {
			$.post(FU_Ajax_Requests_File(), {
			    f:'bot_speakers',
			    s:'update_bot_set',
				bot_delay: $(this).val(),
				allow_bot: $('#allow_bot').val(),
				token: utk,
				}, function(response) {
				callSaved(system.saved, 1);
			});
		}
	}); 
	$(document).on('change', '#allow_bot', function(){
		var allow_bot = $(this).val();
			$.post(FU_Ajax_Requests_File(), {
			    f:'bot_speakers',
			    s:'allow_bot',			    
				allow_bot: $(this).val(),
				token: utk,
				}, function(response) {
				callSaved(system.saved, 1);
			});
	}); 	
	createBot = function(){
		$.post(FU_Ajax_Requests_File(), {
			f:'bot_speakers',
			s:'add_bot_modal',			    
			token: utk,
			}, function(response) {
				showModal(response.content, 500);
		});	
	}
del_bot = function(id) {
    var elm_id = $("#bot_line_" + id);
    var bot_id = id;
    // Early exit if bot_id is invalid
    if (bot_id === 0 || isNaN(bot_id)) {
        console.error('Invalid bot ID');
        return false;
    }
    // Send the request to the server
    $.post(FU_Ajax_Requests_File(), {
        f: 'bot_speakers',
        s: 'del_bot',
        bot_id: bot_id,
        token: utk  // CSRF token
    }, function(res) {
        if (res.status == 200) {
            callSaved(system.saved, 1);
            elm_id.remove(); // Remove the element from DOM
        } else {
            callSaved(system.error, 3); 
        }
    }).fail(function(xhr, status, error) {
        // Handle AJAX failure
        console.error("AJAX error: " + status + " - " + error);
        callSaved(system.error, 3); 
    });
};

</script>
