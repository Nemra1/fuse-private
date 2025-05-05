class PeerCallManager {
  constructor({
    userId,
    roomId,
    callType = 'audio',
    domain = '',
    utk = '',
    curPage = ''
  } = {}) {
    this.userId = userId;
    this.roomId = roomId;
    this.callType = callType;
    this.domain = domain;
    this.utk = utk;
    this.curPage = curPage;
    this.peer = null;
    this.localStream = null;
    this.calls = {}; // Support multiple calls
    this.dataConnections = {};
    this.isMicEnabled = true;
    this.silentTrack = null; // Store silent track
    this.silentContext = null;

    this.init();
  }

  init() {
    // Initialize PeerJS
    this.peer = new Peer(this.userId);

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
      muted: !muted
    };
    Object.values(this.dataConnections).forEach(conn => {
      if (conn.open) conn.send(status);
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
    // Keep context running
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
        this.cleanup();
        this.peer.disconnect();
        window.location.href = 'index.php'; // Redirect or close page
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
    connection.on('open', async () => {
      connection.send({
        type: 'mic_status',
        senderId: this.peer.id,
        muted: !this.isMicEnabled
      });
    });
    connection.on('data', (data) => {
      if (data.type === 'mic_status') {
        let micIcon = document.getElementById(`mic-status-${data.senderId}`);
        if (!micIcon) {
          micIcon = document.createElement('span');
          micIcon.id = `mic-status-${data.senderId}`;
          micIcon.style.marginLeft = '10px';
          const container = document.getElementById('remote-user-list') || document.body;
          container.appendChild(micIcon);
        }
        micIcon.innerHTML = data.muted
          ? '<i class="ri-mic-off-line" style="color: red;"></i>'
          : '<i class="ri-mic-line" style="color: green;"></i>';
      }
    });
    connection.on('close', () => {
      delete this.dataConnections[remoteId];
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
}