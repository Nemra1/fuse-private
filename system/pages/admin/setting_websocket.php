<?php

require __DIR__ . "../../../config_admin.php";

if (!boomAllow(100)) {
    exit;
}

echo elementTitle('Websocket Setting');

?>
   <style>
.red{
 color: red;
}

</style>
<div class="page_full">
	<div>
		<div class="tab_menu">
			<ul>
				<li class="tab_menu_item tab_selected" data="main_tab" data-z="websocket_zone">Main</li>
				<li class="tab_menu_item" data="main_tab" data-z="consolet_zone" >Console</li>
			</ul>
		</div>
	</div>
	<div id="main_tab">
		<div id="websocket_zone" class="tab_zone">
			<div class="page_element">
				<div class="boom_form">
					<div class="setting_element">
						<p class="label">Websocket Server Address</p>
						<input id="set_websocket_path" class="full_input" value="<?php echo $data['websocket_path']; ?>" type="text" placeholder="Domain or ip"/>
					</div>
					<div class="setting_element">
						<p class="label">Websocket Port Address</p>
						<input id="set_websocket_port" class="full_input" value="<?php echo $data['websocket_port']; ?>" type="number" />
					</div>
					<div class="setting_element">
						<p class="label">Websocket Protocol</p>
						<select id="set_websocket_protocol">
            			<option value="https://" <?php if($data['websocket_protocol']=='https://'){echo "selected";} ?>>https://</option>
            			<option value="wss://" <?php if($data['websocket_protocol']=='wss://'){echo "selected";} ?>>wss://</option>
						</select>
					</div>					
					<div class="setting_element">
						<p class="label">Enable Websocket</p>
						<select id="set_websocket_mode">
                         <?php echo yesNo($data["websocket_mode"]); ?>
						</select>
					</div>	
					<div class="setting_element">
						<p class="label">User is typing </p>
						<select id="set_istyping_mode">
                         <?php echo yesNo($data["istyping_mode"]); ?>
						</select>
					</div>					
					
				</div>
				<div id="websocket-box-alert"></div>
				<button data="websocket" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> Save</button>
				<button type="button" id="testConnectionButton" class="save_admin reg_button ok_btn"><i class="ri-shield-flash-fill"></i>Test connection</button>
			</div>
		</div>
		<div id="consolet_zone" class="tab_zone hide_zone" style="display: none;">
			<div class="page_element">
				<div class="boom_form">
                    	<div class="bpad15">
                    		<button onclick="clearConsole();" class="reg_button delete_btn"><i class="ri-delete-bin-2-fill"></i> Clear</button>
                    	</div>
                    
                    	<div id="console_logs_box">
                    		<div class="bpad15 console_logs_search">
                    			<input onkeyup="searchSystemConsole();" id="search_system_console" placeholder="☮" class="full_input" type="text" />
                    		</div>
                    		<div id="console_results" class="box_height"></div>
                    		<div id="console_spinner" class="vpad10 centered_element" style="display: none;">
                    	        <i class="ri-flashlight-line text_jumbo"></i>
                    		</div>
                    	</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://cdn.socket.io/4.6.1/socket.io.min.js"></script>
<script src="js/socket_admin.js"></script>
