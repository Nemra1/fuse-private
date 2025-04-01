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
				<button  type="button" onclick="FUSE_Admin_SOCKET.start();"class="save_admin reg_button ok_btn"><i class="ri-shield-flash-fill"></i>Test connection</button>
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
<script>
var FUSE_Admin_SOCKET = {
    socket: FUSE_SOCKET.socket,
    start: function() {
        if (!this.socket) {
            this.attachEventListeners();
        } else {
            console.log('Socket connection already exists');
             this.attachEventListeners();
        }
    },
    attachEventListeners: function() {
        const alertContainer = $("#websocket-box-alert");
        FUSE_SOCKET.socket.on('connect', () => {
            const successAlert = $("<div>", {
                class: "alert alert-success",
                text: "Successfully connected to the server!"
            }).prepend('<i class="ri-checkbox-circle-fill"></i>');
            alertContainer.html(successAlert);
            console.log('Connected to the server. Socket ID:', FUSE_SOCKET.socket.id);
           
        });
        FUSE_SOCKET.socket.on('disconnect', () => {
            const errorAlert = $("<div>", {
                class: "alert alert-danger",
                text: "Disconnected from the server..."
            }).prepend('<i class="ri-xrp-line"></i>');
            alertContainer.html(errorAlert);
            console.log('Disconnected from the server.');
        });
        FUSE_SOCKET.socket.on('connect_error', (error) => {
            const errorAlert = $("<div>", {
                class: "alert alert-danger",
                text: "Failed to connect to the server: " + error.message
            }).prepend('<i class="ri-xrp-line"></i>');
            alertContainer.html(errorAlert);
            console.error('Connection failed:', error);
        });
        FUSE_SOCKET.socket.on('reconnect_attempt', () => {
            const reconnectAlert = $("<div>", {
                class: "alert alert-danger",
                text: "Reconnecting..."
            }).prepend('<i class="ri-xrp-line"></i>');
            alertContainer.html(reconnectAlert);
            console.log('Reconnecting...');
        });
        this.socket.emit('login', FUSE_SOCKET.sendUserUpdate());
    },
    addLogEntry: function(message, type) {
        const $log = $('#console_results');
        const timestamp = new Date().toLocaleTimeString();
        const $entry = `
                    <div class="sub_list_item console_data_logs" value="1">
                        <div class="sub_list_cell_top hpad3">
                            <div class="text_small console_log">
                                <span class="bold ${type}">${type}</span> ${message}
                            </div>
                        </div>
                        <div class="console_date sub_text centered_element">${timestamp}</div>
                    </div>`;
        $log.append($entry);
        $log.scrollTop($log[0].scrollHeight);
    },
    logSocket: function() {
        if (!this.socket) {
            this.start();
        }

        this.socket.on('monitor', (data) => {
            console.log(data);
            const log_type = data.type;
            let color = '';
            let icon = '';
            switch (log_type) {
                case 'connect_to_server':
                    color = 'red';
                    icon = `<i class="ri-plug-line lmargin3 ${color}"></i>`;
                    break;
                case 'join_room':
                    color = 'success';
                    icon = `<i class="ri-chat-1-line lmargin3 ${color}"></i>`;
                    break;
                case 'switch_room':
                    color = 'blue';
                    icon = `<i class="ri-shut-down-line lmargin3 ${color}"></i>`;
                    break;
                case 'left_room':
                    color = 'black';
                    icon = `<i class="ri-plug-fill lmargin3 ${color}"></i>`;
                    break;
                case 'logged_in':
                    color = 'purple';
                    icon = `<i class="ri-gradienter-line lmargin3 ${color}"></i>`;
                    break;
                case 'left_server':
                    color = 'dark_gray';
                    icon = `<i class="ri-reset-left-line lmargin3 ${color}"></i>`;
                    break;
                case 'room_list':
                    color = 'dark_gray';
                    icon = `<i class="ri-chat-voice-fill lmargin3 ${color}"></i>`;
                    break;
            }

            const timestamp = new Date().toLocaleTimeString();
            const logList = `
                        <div class="sub_list_item console_data_logs ${color}" value="1">
                            <div class="text_small console_log">${icon}
                                <span class="bold console_user  ${color}">${data.ip}: ${data.text}</span>
                            </div>
                            <div class="console_date sub_text centered_element">${timestamp}</div>
                        </div>`;

            $('#console_results').append(logList);
        });
    }
};

setTimeout(() => {
    FUSE_Admin_SOCKET.start();
    FUSE_Admin_SOCKET.logSocket();;
}, 1000);
</script>