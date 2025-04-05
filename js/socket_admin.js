// Attach an event listener to the 'Test Connection' button
$('#testConnectionButton').on('click', function() {
    // Emit a 'testConnection' event to the server
    FUSE_Admin_SOCKET.socket.emit('testConnection', { message: 'Testing connection' });

    // Optionally, display an alert that the test is in progress
    displayAlert("Testing connection...", "info", "<i class='ri-refresh-line'></i>");
})
var FUSE_Admin_SOCKET = {
    socket: FUSE_SOCKET.socket,
    start: function() {
        if (!this.socket) {
            this.socket = io(); // Initialize socket connection
        }
        // Listen for the 'connect' event
        this.socket.on('connect', () => {
            console.log('Socket connected with SID:', this.socket.id); // Log socket ID
            // Check if transport is WebSocket
            if (this.socket.io.engine.transport.name === 'websocket') {
				displayAlert("Successfully connected to the server!", "success", "<i class='ri-checkbox-circle-fill'></i>");
                console.log('Transport is WebSocket');
            } else {
                console.log('Transport is not WebSocket:', this.socket.io.engine.transport.name);
            }
        });		
        // Listen for connection error
        this.socket.on('connect_error', () => {
            console.log('Connection failed!');
            displayAlert("Disconnected from the server...", "danger", "<i class='ri-xrp-line'></i>");
        });
        // Listen for disconnect event
        this.socket.on('disconnect', () => {
            console.log('Disconnected from the server');
        });
        // Listen for the test connection response from the server
        this.socket.on('testConnectionResponse', (data) => {
            console.log('Test Connection Response:', data);
            displayAlert(data.message, "success", "<i class='ri-checkbox-circle-fill'></i>");
        });
		
    }
};
function displayAlert(message, type, icon) {
    const alertContainer = $("#websocket-box-alert");
    const alert = $("<div>", {
        class: `alert alert-${type}`,
        text: message
    }).prepend(icon); // Add the icon before the message

    alertContainer.html(alert); // Update the alert container's content with the new alert
    alertContainer.show(); // Ensure the alert container is visible
}

// Call the start method to initiate the connection and handle events
FUSE_Admin_SOCKET.start();


/*
var FUSE_Admin_SOCKET = {
    socket: FUSE_SOCKET.socket,
    start: function() {
        if (!this.socket) {
            this.attachEventListeners();
        } else {
            console.log('Socket connection already exists');
        }
    },
    attachEventListeners: function() {
        const alertContainer = $("#websocket-box-alert");

        // Establish socket event listeners
        FUSE_SOCKET.socket.on('connect', () => {
            this.displayAlert("Successfully connected to the server!", "success", "<i class='ri-checkbox-circle-fill'></i>");
            console.log('Connected to the server. Socket ID:', FUSE_SOCKET.socket.id);
        });

        FUSE_SOCKET.socket.on('disconnect', () => {
            this.displayAlert("Disconnected from the server...", "danger", "<i class='ri-xrp-line'></i>");
            console.log('Disconnected from the server.');
        });

        FUSE_SOCKET.socket.on('connect_error', (error) => {
            this.displayAlert(`Failed to connect to the server: ${error.message}`, "danger", "<i class='ri-xrp-line'></i>");
            console.error('Connection failed:', error);
        });

        FUSE_SOCKET.socket.on('reconnect_attempt', () => {
            this.displayAlert("Reconnecting...", "warning", "<i class='ri-xrp-line'></i>");
            console.log('Reconnecting...');
        });

        // Emit login event with user data
        this.socket.emit('login', FUSE_SOCKET.sendUserUpdate());
    },

    displayAlert: function(message, type, icon) {
        const alertContainer = $("#websocket-box-alert");
        const alert = $("<div>", {
            class: `alert alert-${type}`,
            text: message
        }).prepend(icon);
        alertContainer.html(alert);
    },

    addLogEntry: function(message, type) {
        const $log = $('#console_results');
        const timestamp = new Date().toLocaleTimeString();
        const $entry = `
            <div class="sub_list_item console_data_logs">
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
                default:
                    color = 'default';
                    icon = `<i class="ri-question-line lmargin3 ${color}"></i>`;
            }

            const timestamp = new Date().toLocaleTimeString();
            const logList = `
                <div class="sub_list_item console_data_logs ${color}" value="1">
                    <div class="text_small console_log">${icon}
                        <span class="bold console_user ${color}">${data.ip}: ${data.text}</span>
                    </div>
                    <div class="console_date sub_text centered_element">${timestamp}</div>
                </div>`;

            $('#console_results').append(logList);
        });
    }
};

// Initialize and start the socket connection after a delay
setTimeout(() => {
    FUSE_Admin_SOCKET.start();
    FUSE_Admin_SOCKET.logSocket();
}, 1000);
*/