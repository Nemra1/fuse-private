class PeerCallManager {
  constructor({
    userId,
    roomId,
	username = 'Anonymous',
    callType = 'audio',
    domain = '',
    utk = '',
    curPage = ''
  } = {}) {
    this.userId = userId;
    this.roomId = roomId;
	this.username = username;
    this.callType = callType;
    this.domain = domain;
    this.utk = utk;
    this.curPage = curPage;
    this.peer = null;
    this.localStream = null;
    this.calls = {};
    this.dataConnections = {};
    this.isMicEnabled = true;
    this.silentTrack = null;
    this.silentContext = null;
    this.isLeaving = false;

    this.init();
  }

	init() {
	  // Initialize PeerJS
	  this.peer = new Peer(this.userId);
	  // ✅ Listen for incoming DATA connections
	  this.peer.on('connection', (conn) => {
		console.log("🔌 Incoming data connection from:", conn.peer);
		this.setupDataConnection(conn, conn.peer);
	  });

	  this.peer.on('open', (id) => {
		console.log("Peer ID:", id);
		this.joinRoom();
	  });

	  this.peer.on('error', (err) => {
		console.error("PeerJS Error:", err.message);
		alert("فشل في الاتصال: " + err.message);
	  });

	  this.peer.on('call', (call) => {
		this.handleIncomingCall(call);
	  });

	  this.peer.on('disconnected', () => {
		console.log("Peer disconnected, reconnecting...");
		this.peer.reconnect();
	  });

	  this.setupUI();
	  this.getMicrophoneAccess();
	}

  async getMicrophoneAccess() {
    try {
      this.localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
      const localAudio = document.createElement('audio');
      localAudio.id = 'local-audio';
      localAudio.muted = true;
      localAudio.autoplay = true;
      localAudio.srcObject = this.localStream;
      document.body.appendChild(localAudio);
      this.isMicEnabled = true;
    } catch (err) {
      alert("يجب تمكين المايكروفون للانضمام إلى المكالمة");
      console.error("Microphone access error:", err);
    }
  }
	sendMicStatus(muted) {
	  const status = {
		type: 'mic_status',
		senderId: this.peer.id,
		 username: this.username, // Add this line
		muted: !muted
	  };
	  Object.values(this.dataConnections).forEach(conn => {
		if (conn.open) {
		  conn.send(status);
		  console.log("📤 Sent mic status:", status);
		}
	  });
	}

  async createSilentAudioTrack() {
    const ctx = new AudioContext();
    const dest = ctx.createMediaStreamDestination();
    const { sampleRate } = ctx;
    const buffer = ctx.createBuffer(1, sampleRate, sampleRate);
    const source = ctx.createBufferSource();
    source.buffer = buffer;
    source.connect(dest);
    source.start();
    this.silentContext = ctx;
    return dest.stream.getAudioTracks()[0];
  }

  setupUI() {
    // Mic toggle
    const micBtn = document.getElementById('vcall_mic');
    if (micBtn) {
      micBtn.addEventListener('click', async () => {
        if (!this.localStream) return;
        this.isMicEnabled = !this.isMicEnabled;
        const audioTrack = this.localStream.getAudioTracks()[0];
        if (audioTrack) {
          audioTrack.enabled = this.isMicEnabled;
        }
        this.sendMicStatus(this.isMicEnabled);
        // Update local UI mic icon
        const iconEl = document.getElementById('mic-state-icon');
        if (iconEl) {
          iconEl.innerHTML = this.isMicEnabled
            ? '<i class="ri-mic-line" style="color: green;"></i>'
            : '<i class="ri-mic-off-line" style="color: red;"></i>';
        }
        // Update stream using replaceTrack (modern way)
        if (this.currentCall && this.currentCall.peerConnection) {
          const sender = this.currentCall.peerConnection.getSenders()
            .find(s => s.track && s.track.kind === 'audio');
          if (sender) {
            if (this.isMicEnabled && audioTrack) {
              await sender.replaceTrack(audioTrack);
            } else {
              if (!this.silentTrack) {
                this.silentTrack = await this.createSilentAudioTrack();
              }
              await sender.replaceTrack(this.silentTrack);
            }
          }
        }
        micBtn.classList.toggle('vcall_off', !this.isMicEnabled);
      });
    }

    // Leave call
    const leaveBtn = document.getElementById('vcall_leave');
    if (leaveBtn) {
      leaveBtn.addEventListener('click', () => {
		  this.leaveCall();
      });
    }

  }

  joinRoom() {
    console.log("Joined room:", this.roomId);
    // TODO: Implement actual room logic with socket.io/firebase here
  }

call(otherUserId) {
    if (!this.localStream) {
      alert("الميكروفون غير متوفر");
      return;
    }
    const call = this.peer.call(otherUserId, this.localStream);
    this.calls[otherUserId] = call;
    this.currentCall = call;
    this.setupCallEvents(call);
    const dataConn = this.peer.connect(otherUserId);
    this.setupDataConnection(dataConn, otherUserId);
}
setupDataConnection(connection, peerId) {
  const remoteId = peerId || connection.peer;
  this.dataConnections[remoteId] = connection;
  console.log("🔌 Setting up data connection with:", remoteId);
  connection.on('data', (data) => {
    console.log("📨 FULL DATA RECEIVED:", data);
    if (!data || typeof data !== 'object') {
      console.warn("⚠️ Invalid data received:", data);
      return;
    }
    if (data.type === 'mic_status') {
      const userId = data.senderId;
      const username = data.username || 'User';
      const isMuted = data.muted;
      let micIcon = document.getElementById(`mic-status-${userId}`);
      if (!micIcon) {
        micIcon = document.createElement('div');
        micIcon.id = `mic-status-${userId}`;
        micIcon.innerHTML = this.getMicIconHTML(userId, username, isMuted);
        document.getElementById('remote-user-list')?.appendChild(micIcon);
      } else {
        micIcon.innerHTML = this.getMicIconHTML(userId, username, isMuted);
      }
    }
    if (data.type === 'user_left') {
      console.log(`⚠️ User ${data.userId} left the call`);
      alert(`${data.username || 'The other user'} has left the call.`);
      window.location.href = 'call_index.php?end=2';
    }
  });

  connection.on('open', async () => {
    console.log("✅ Data connection OPEN with:", remoteId);
    connection.send({
      type: 'mic_status',
      senderId: this.peer.id,
      username: this.username,
      muted: !this.isMicEnabled
    });
  });

  connection.on('close', () => {
    console.log("🔌 Data connection CLOSED with:", remoteId);
	this.leaveCall();
    delete this.dataConnections[remoteId];
  });

  connection.on('error', (err) => {
    console.error("💥 Data connection ERROR with:", remoteId, err);
  });
}
  handleIncomingCall(call) {
    if (call.peer === this.peer.id) return;

    call.answer(this.localStream);
    this.calls[call.peer] = call;
    this.currentCall = call;

    const dataConn = this.peer.connect(call.peer);
    this.setupDataConnection(dataConn, call.peer);

    this.setupCallEvents(call);
  }

  setupCallEvents(call) {
    call.on('stream', (remoteStream) => {
      console.log("Received remote stream:", remoteStream);
      const remoteAudio = document.createElement('audio');
      remoteAudio.autoplay = true;
      remoteAudio.playsInline = true;
      remoteAudio.srcObject = remoteStream;
      remoteAudio.id = 'remote-audio';

      const container = document.getElementById('vcall_streams');
      if (container) {
        container.innerHTML = '';
        container.appendChild(remoteAudio);
      }
    });

    call.on('close', () => {
      this.cleanupRemoteAudio();
    });

    call.on('error', (err) => {
      console.error("Call error:", err);
    });
  }

  cleanupRemoteAudio() {
    const remoteAudio = document.getElementById('remote-audio');
    if (remoteAudio) {
      remoteAudio.pause();
      remoteAudio.srcObject = null;
      remoteAudio.remove();
    }
  }

cleanup() {
    // Stop local stream
    if (this.localStream) {
      this.localStream.getTracks().forEach(track => track.stop());
      this.localStream = null;
    }
    // Close data connections
    Object.values(this.dataConnections).forEach(conn => conn.close());
    this.dataConnections = {};

    // Close current call
    if (this.currentCall) {
      this.currentCall.close();
      this.currentCall = null;
    }
    // Close silent context
    if (this.silentContext) {
      this.silentContext.close();
      this.silentContext = null;
    }
    this.cleanupRemoteAudio();
  }
	getMicIconHTML(userId, username, muted) {
	  return `
		<span style="min-width:200px;display: inline;">${username || `User ${userId}`}:</span>
		<i class="ri-${muted ? 'volume-mute' : 'volume-up'}-line" style="color:${muted ? 'red' : 'green'};"></i>
	  `;
	}
	leaveCall() {
	  if (this.isLeaving) return;
	  this.isLeaving = true;
	  console.log("Leaving call...");
	  // Step 1: Notify other user(s)
	  const leaveMessage = {
		type: 'user_left',
		userId: this.peer.id,
		username: this.username || 'User'
	  };
	  Object.values(this.dataConnections).forEach(conn => {
		if (conn.open) {
		  conn.send(leaveMessage);
		  console.log("📤 Sent user_left to:", conn.peer);
		}
	  });
	  // Step 2: Wait a moment before cleanup to allow message to go through
	  setTimeout(() => {
		this.cleanup();
		if (this.peer && !this.peer.destroyed) {
		  this.peer.disconnect();
		}
		window.location.href = 'call_index.php?end=2';
	  }, 50); // Small delay to ensure message is sent
	}

}