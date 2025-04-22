const FUSE_SOCKET = {
    socket: null,
    isConnected: false,
    currentRoom: logged ? cur_room : 'index',
    listenersAttached: false,
    curPage:'home',
    typing:false, // Flag to track typing status
    typingTimeout:600,
    typingTimer:null,
    debounce: function (func, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    },
    sendUserUpdate: function () {
        return {
            user_id: user_id,
            username: user_name,
            avatar: avatar,
            role: user_rank,
            room: this.currentRoom,
            is_logged: logged === 1,
            is_joined: curPage === 'chat',
            user_type: logged === 1 ? 'member' : 'guest'
        };
    },
    start: function () {
            if (!this.isSocketInitialized && (!this.socket || !this.isConnected)) {
                console.log('Initializing new socket connection...');
                
                this.socket = io(s_protocol + s_server + ':' + s_port, {
                    reconnectionDelay: 3000,
                    reconnectionAttempts: 1,
                    reconnection: true,
                    transports: ['websocket', 'polling'],
                    secure: true,
                    withCredentials: true
                });
        
                // Attach event listeners only once
                if (!this.listenersAttached) {
                    this.attachEventListeners();
                    this.listenersAttached = true; // Mark that listeners have been attached
                }
        
                this.isSocketInitialized = true; // Prevent future reinitialization
                
            } else {
                console.log('Socket connection already exists or is being initialized');
            }
        },
        // Method to notify typing
    notifyTyping: function (user_id) {
        if (!this.typing && allow_typing ==="1") {
            this.typing = true; // Set typing status
            this.socket.emit('typing', user_id); // Emit typing event
        }

        // Clear any existing timeout to reset typing status
        clearTimeout(this.typingTimer);
        this.typingTimer = setTimeout(() => {
            this.typing = false; // Reset typing status
        }, this.typingTimeout);
    },    
    attachEventListeners: function () {
        // Handle socket connection
        this.socket.on('connect', () => {
            console.log('Socket connected:', this.socket.id);
            this.isConnected = true;
            this.curPage = curPage;
            if (this.curPage=='chat') {
                this.socket.emit('login', this.sendUserUpdate());
            }
        });

        // Handle reconnection logic
        this.socket.on('reconnect', () => {
            console.log('Reconnected to the server');
            this.socket.emit('login', this.sendUserUpdate());
        });
         if(allow_typing ==="1") {
            $('#content').on('input', () => {
                     this.notifyTyping(user_id);
            });             
            // Handle typing indicator from server
            this.socket.on('userTyping', (data) => {
                console.log(`${data.username} is typing...`);
                const typing_temp = `
                    <div onclick="getProfile(${data.user_id});" class="cp_typing_indicator" title="${data.username} is typing...">
                        <span class="cp_ball cp_ball1"></span>
                        <span class="cp_ball cp_ball2"></span>
                        <div class="user_item_avatar"><img class="avav acav avsex nosex " src="${data.avatar}"> </div>
                        <span class="cp_ball cp_ball3"></span>
                        <span class="cp_ball cp_ball3"></span>
                    </div>
                `;
                $('#typing-indicator').html(typing_temp);
                setTimeout(() => {
                    $('#typing-indicator').text('');
                }, 5000);
            });
        }
        // Handle message submission
        $('#submit_button').on('click', () => {
            const message = $('#content').val();
            this.socket.emit('sendMessage', { room: this.currentRoom, message });
        });

        // Prevent duplicate event listeners for room switching
        $(document).off('click', '.switch_room').on('click', '.switch_room', (event) => {
            const newRoom = $(event.currentTarget).data('room');
            if (newRoom && newRoom !== FUSE_SOCKET.currentRoom) {
                FUSE_SOCKET.switchRoom(newRoom);
            }
        });
        // Handle socket disconnection
        this.socket.on('disconnect', () => {
            console.log('Socket disconnected');
            this.isConnected = false;
            this.isSocketInitialized = false; // Allow reconnection attempts
        });

        // Handle socket disconnection
        this.socket.on('roomUsers', (data) => {
            console.log(data);
        });

        // Handle connection error
        this.socket.on('connect_error', (err) => {
            console.error('Connection failed: ', err.message);
        });

        // Disconnect socket on page unload
        window.addEventListener('beforeunload', () => {
            if (this.socket) {
                this.socket.disconnect();
            }
        });
    },

    switchRoom: function (newRoom) {
        if (this.currentRoom) {
            this.socket.emit('leaveRoom', { room: this.currentRoom });
        }
        this.socket.emit('switch_room', {
            user_id: user_id,
            room: newRoom,
            role: user_rank,
            avatar: avatar,
            is_logged: logged === 1,
            is_joined: curPage === 'chat',
            user_type: logged === 1 ? 'member' : 'guest',
            username: user_name
        });

        this.currentRoom = newRoom;
    }, logSocket: function() {
        if (!this.socket) {
            this.start();
        }
        //belong to initializeMonitor();
        this.socket.on('monitor', (data) => {
            console.log(data);
            const log_type = data.type;
            let color = '';
            let icon = '';
            switch (log_type) {
                case 'connect_to_server':
                    color = 'error';
                    icon = `<i class="ri-plug-line lmargin3 ${color}"></i>`;
                    break;
                case 'join_room':
                    color = 'success';
                    icon = `<i class="ri-chat-1-line lmargin3 ${color}"></i>`;
                    break;
                case 'switch_room':
                    color = 'warn';
                    icon = `<i class="ri-expand-width-line  lmargin3 ${color}"></i>`;
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
            $('#SocketMonitor_wrap_stream').append(logList);
        });
        
    }
};

// Start the socket connection on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    FUSE_SOCKET.start();
});
