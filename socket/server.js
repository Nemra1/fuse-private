const fs = require('fs');
const path = require('path');
const express = require('express');
const https = require('https');
const socketio = require('socket.io');
const initConfigModule = require('./configModule'); // Import the config module
const config = require('./config_setting'); // Import configuration from the config_setting.js file
const formatMessage = require('./messages');
const compression = require('compression');
const UserManager = require('./users'); // Import the UserManager class
const EventEmitter = require('events');
EventEmitter.defaultMaxListeners = 1000; // Increase the global limit
// Extract the first object from the array
const settings = config[0] || {}; // Default to an empty object if the array is empty
// Read SSL/TLS certificate and key
const privateKey = fs.readFileSync('privkey.pem', 'utf8');
const certificate = fs.readFileSync('fullchain.pem', 'utf8');
const credentials = { key: privateKey, cert: certificate };
// Create an HTTPS server
const app = express();
const PORT = process.env.PORT || 8543;
const server = https.createServer(credentials, app);
const io = socketio(server, {
    cors: {
        origin: ["https://127.0.0.1", "https://localhost", "http://127.0.0.1", "http://localhost"], // Allow multiple origins
        methods: ["GET", "POST"],
        credentials: true
    }
});
// Make 'io' globally available (if needed)
global.io = io;
app.use(compression());
// Set Static Folder
app.use(express.static(path.join(__dirname, 'public')));
const botName = 'ChatCord Bot';
// Optional: Define your authentication middleware
function authMiddleware(req, res, next) {
    const token = req.headers['x-auth-token'];
    if (token && token === 'lucifer666') {
        next();
    } else {
        res.status(403).send('Forbidden');
    }
}
// Initialize the configuration module with authentication
initConfigModule({
    app,
    filePath: 'config_setting.js', // You can specify a different path if needed
    authMiddleware, // Pass the authentication middleware
});
// Create an instance of the UserManager class
const userManager = new UserManager(io);
// Track active users and their socket IDs
const private_chat_Sockets = {};
// Track private chat sessions (userA -> userB)
const privateChatSessions = {};
const TYPING_DELAY = 500; // Delay in milliseconds
let lastTypingEmitTime = 0; // Track last emit time

// Run when client connects
io.on('connection', socket => {
    // Handle user login
		socket.on('login', ({ user_id, username, room_id, avatar, role }) => {
		user_id = normalizeId(user_id);
		room_id = normalizeId(room_id);
		role = normalizeId(role);
		const user = userManager.userJoin(socket, user_id, username, room_id, avatar, role);
		console.log(`User ${user_id} joined with socket ID: ${socket.id}`);
		console.log('Updated userToSocketMap:', userManager.userToSocketMap);
        socket.join(user.room_id); // Join the public room_id
        // Notify the owner
			userManager.sendToRoleOnce(socket, 'monitor', () => {
				const numericRole = userManager.resolveRoleByName('owner'); // Convert "owner" to 100
				if (numericRole === null) {
					console.warn(`Invalid role detected: owner`);
					return;
				}
				userManager.sendToRole(numericRole, 'monitor', formatMessage(botName, `${user.username} has joined the room`, socket, 'join_room'));
			});
        // Send users and room_id info
        io.to(user.room_id).emit('room_Users', {
            room_id: user.room_id,
            users: userManager.getUsers(user.room_id)
        });
    });
	// Handle room_id switch
	socket.on('switch_room', ({ user_id, username, room_id, avatar, role }) => {
		// Join the user to the new room_id
		const user = userManager.userJoin(socket, user_id, username, room_id, avatar, role);
		if (!user) {
			console.error(`Failed to join user ${user_id} to room ${room_id}`);
			return;
		}
		socket.join(user.room_id);
		// Optionally, leave the old room_id
		const previousroom = userManager.getCurrentUser(user_id)?.room_id;
		if (previousroom && previousroom !== user.room_id) {
			socket.leave(previousroom);
			// Notify users in the previous room_id about the departure
			io.to(previousroom).emit('room_Users', {
				room_id: previousroom,
				users: userManager.getUsers(previousroom)
			});
		}
		// Notify the owner about the room_id switch
			userManager.sendToRoleOnce(socket, 'monitor', () => {
				const numericRole = userManager.resolveRoleByName('owner'); // Convert "owner" to 100
				if (numericRole === null) {
					console.warn(`Invalid role detected: owner`);
					return;
				}
				userManager.sendToRole(numericRole, 'monitor', formatMessage(botName, `${user.username} has switched to room ${user.room_id}`, socket, 'switch_room'));
			});
		// Notify users in the new room_id about the arrival
		io.to(user.room_id).emit('room_Users', {
			room_id: user.room_id,
			users: userManager.getUsers(user.room_id)
		});
	});
	// Handle private chat requests
	socket.on('startPrivateChat', ({ user_id, target_id }) => {
			 target_id = normalizeId(target_id); // Convert to a number
			console.log('Starting private chat between:', user_id, 'and', target_id);
			// Validate target user
			if (!userManager.userToSocketMap.has(target_id)) {
				console.warn(`Invalid or inactive target user ID: ${target_id}`);
				return;
			}
			// Get the socket ID of the target user
			const targetSocketId = userManager.userToSocketMap.get(target_id);
			if (!targetSocketId) {
				console.warn(`Target user ${target_id} is not connected.`);
				return;
			}
			// Establish a private chat session
			privateChatSessions[user_id] = target_id;
			privateChatSessions[target_id] = user_id;
			// Notify both users about the private chat
			io.to(socket.id).emit('privateChatStarted', { target_id });
			io.to(targetSocketId).emit('privateChatStarted', { target_id: user_id });
			console.log(`Private chat started between ${user_id} and ${target_id}`);
		});
	// Handle "user is typing" for private chats
	socket.on('privateTyping', ({ user_id, target_id, stopped }) => {
			 target_id = normalizeId(target_id);
			 const userInfo = userManager.getUserInfo(user_id);
				console.log(`User ${user_id} is typing in private chat with ${target_id}  = typing section`);
				// Validate target user
				if (!userManager.userToSocketMap.has(target_id)) {
					console.warn(`Invalid or inactive target user ID: ${target_id} = typing section`);
					return;
				}
				// Get the socket ID of the target user
				const targetSocketId = userManager.userToSocketMap.get(target_id);
				if (!targetSocketId) {
					console.warn(`Target user ${target_id} is not connected.  = typing section`);
					return;
				}
				// Emit the "user is typing" or "user stopped typing" event to the target user
				io.to(targetSocketId).emit('privateUserTyping', {
					user_id: user_id,
					username: userInfo['username'] || 'Unknown User',
					avatar: userInfo['avatar'] || 'Unknown avatar',
					role: userInfo['role'] || 'Unknown avatar',
					stopped: !!stopped // Convert to boolean
				});
			});
	// Handle message deletion requests
	socket.on('deleteMessage', ({ msg_id, target_id }, callback) => {
			console.log('Attempting to delete message:', { msg_id, target_id });
			// Validate input
			const numericMsgId = Number(msg_id);
			const numericTargetId = Number(target_id);
			if (!numericMsgId || !numericTargetId) {
				console.warn('Invalid message ID or target ID:', { msg_id, target_id });
				callback({ success: false, error: 'Invalid message or target ID.' });
				return;
			}
			// Validate target user
			if (!userManager.userToSocketMap.has(numericTargetId)) {
				console.warn(`Invalid or inactive target user ID: ${numericTargetId}`);
				callback({ success: false, error: 'Target user is not connected.' });
				return;
			}
			// Get the socket ID of the target user
			const targetSocketId = userManager.userToSocketMap.get(numericTargetId);
			if (!targetSocketId) {
				console.warn(`Target user ${numericTargetId} is not connected.`);
				callback({ success: false, error: 'Target user is not connected.' });
				return;
			}
			console.log(`Found socket ID for target user ${numericTargetId}:`, targetSocketId);
			// Emit the deletion event only to the target user
			io.to(targetSocketId).emit('messageDeleted', { msg_id: numericMsgId });
			console.log(`Message deletion event sent to user ${numericTargetId}`);
			callback({ success: true });
		});
    // PRIVATE CHAT HANDLER SECTION END
	socket.on('typing', (user_id) => {
			const currentTime = Date.now();
			// Check if enough time has passed since the last typing event
			if (!lastTypingEmitTime || currentTime - lastTypingEmitTime > TYPING_DELAY) {
				lastTypingEmitTime = currentTime;
				// Retrieve the current user based on user ID
				const user = userManager.getCurrentUser(user_id);
				if (!user) {
					console.warn(`User with ID ${user_id} not found.`);
					return;
				}
				console.log(`User ${user.username} is typing in room ${user.room_id}`);
				// Broadcast the typing notification to other users in the same room_id
				socket.broadcast.to(user.room_id).emit('userTyping', {
					user_id: user.user_id,
					username: user.username,
					avatar: user.avatar
				});
			}
		});
	//Handle Public_announcement
	  /**
	 * Broadcasts a message to all clients in a specific room_id.
	 *
	 * @param {string} room_id The room_id to broadcast to.
	 * @param {string} event The event name (e.g., "newMessage").
	 * @param {object} data The message payload.
	 */
	function broadcastToroom_id(room_id, event, data) {
		io.to(room_id).emit(event, data);
		console.log(`Broadcasted "${event}" to room_id "${room_id}":`, data);
	}
	/**
	 * Broadcasts a message to all connected clients.
	 *
	 * @param {string} event The event name (e.g., "newMessage").
	 * @param {object} data The message payload.
	 */
	function broadcastToAll(event, data) {
		io.emit(event, data);
		console.log(`Broadcasted to all "${event}" to all clients:`, data);
	}
		// Listen for custom events
	socket.on("newMessage", (data) => {
				console.log("Incoming message data:", data);
				console.log("Current users array:", userManager.users);
				// Normalize user_id to a string
				data.user_id = String(data.user_id);
				const userInfo = userManager.getUserInfo(data.user_id);
				if (!userInfo) {
					console.warn(`User with ID ${data.user_id} not found. Cannot broadcast message.`);
					return;
				}
				const enrichedData = {
					...data,
					senderInfo: {
						username: userInfo.username,
						avatar: userInfo.avatar,
						role: userManager.UsRrank(userInfo.role),
						user_type: userInfo.user_type,
						user_id: userInfo.user_id
					}
				};
				console.log("Received message:", enrichedData);
				if (data.room_id) {
					broadcastToroom_id(data.room_id, "newMessage", enrichedData);
				} else {
					broadcastToAll("newMessage", enrichedData);
				}
			});


// Handle incoming chat messages
socket.on('chatMessage', ({ user_id, msg, room_id }) => {
    // Validate input: Ensure user_id and msg are provided
    if (!user_id || !msg) {
        console.warn('Invalid or missing user_id or message:', { user_id, msg });
        return;
    }
    // Retrieve the current user based on user_id
    const user = userManager.getCurrentUser(user_id);
    if (!user) {
        console.warn(`User with ID ${user_id} not found.`);
        return;
    }
    // Use the user's current room_id from the UserManager
    const currentRoomId = user.room_id;
    // Broadcast the message to all users in the same room (excluding the sender)
    socket.broadcast.to(currentRoomId).emit('message', {
        user_id: user.user_id,
        username: user.username,
        avatar: user.avatar,
        msg: msg
    });
    // Optionally, log the message details for debugging
    console.log(`Broadcasted message to room ${currentRoomId}:`, {
        user_id: user.user_id,
        username: user.username,
        msg: msg
    });
});



		socket.on('disconnect', () => {
			// Use userManager.userLeave to handle user disconnection
			const user = userManager.getCurrentUserBySocketId(socket.id);
			if (user) {
				// Clean up private chat sessions
				delete private_chat_Sockets[user.user_id]; // Remove user from private_chat_Sockets
				delete privateChatSessions[user.user_id]; // Clean up private chat sessions
				// Notify the room_id about the updated user list
				io.to(user.room_id).emit('room_Users', {
					room_id: user.room_id,
					users: userManager.getUsers(user.room_id)
				});
			// Notify owners/monitors about the user leaving
			userManager.sendToRoleOnce(socket, 'monitor', () => {
				userManager.sendToRole(
					'owner',
					'monitor',
					formatMessage(botName, `${user.username} has left the chat!`, socket, 'left_server')
				);
			});
			userManager.userLeave(socket.id);
		} else {
			console.log(`User with socket ID ${socket.id} not found on disconnect.`);
		}
	});
        // Handle chat messages

});
function normalizeId(id) {
    const normalizedId = Number(id);
    if (!Number.isInteger(normalizedId)) {
        throw new Error(`Invalid ID: ${id}`);
    }
    return normalizedId;
}
/**
 * Helper function to send events once per connection.
 */
function sendToRoleOnce(socket, event, callback) {
    if (!socket.listeners(event).length) {
        callback();
    }
}

// Start the server
server.listen(PORT, () => {
    console.log(`Server running on https://localhost:${PORT}`);
});