let client = AgoraRTC.createClient({mode:'rtc', codec:"vp8"})

let localTracks = {
    audioTrack:null,
    videoTrack:null
}

let localTrackState = {
    audioTrackMuted:false,
    videoTrackMuted:false
}

let remoteTracks = {}

let joinStreams = async () => {
    client.on("user-published", handleUserJoined);
    client.on("user-left", handleUserLeft);
    client.enableAudioVolumeIndicator();
    client.on("volume-indicator", function(evt){
        for (let i = 0; evt.length > i; i++){
            let speaker = evt[i].uid
            let volume = evt[i].level
            if(volume > 0){
                $(`#volume-${speaker}`).attr('src', 'default_images/call/volume-on.svg');
            }else{
                $(`#volume-${speaker}`).attr('src', 'default_images/call/volume-off.svg');
            }
        }
    });
    [appuser, localTracks.audioTrack, localTracks.videoTrack] = await  Promise.all([
        client.join(appid, approom, apptoken ||null, appuser ||null),
        AgoraRTC.createMicrophoneAudioTrack(),
        AgoraRTC.createCameraVideoTrack({
            encoderConfig: {
                resolution: "480p"
            }
        })
    ])
    let player = selfPlayer(appuser);
    $('#vcall_self').append(player).removeClass('vcallhide');
    localTracks.videoTrack.play(`stream-${appuser}`)
    await client.publish([localTracks.audioTrack, localTracks.videoTrack])
}

let handleUserJoined = async (user, mediaType) => {
    remoteTracks[user.uid] = user
    await client.subscribe(user, mediaType)
    if (mediaType === 'video'){
        let player = $(`#video-wrapper-${user.uid}`);
        console.log('player:', player)
        if (player != null){
            player.replaceWith("")
        }
        player = joinPlayer(user.uid);
        $('#vcall_streams').append(player);
        user.videoTrack.play(`stream-${user.uid}`)
		handleUserName(user.uid);
    }
    if (mediaType === 'audio') {
        user.audioTrack.play();
    }
}

let leaveCall = async () => {
    for (let trackName in localTracks) {
        let track = localTracks[trackName]
        if (track) {
            track.stop()
            track.close()
            localTracks[trackName] = null
        }
    }
    await client.leave()
    $('#vcall_streams').html('');
	closeIframe();
	window.location.href = 'call.php';
	
}

let closeIframe = () => {
	window.parent.postMessage("endCall", window.location.origin);
}

function FU_Ajax_Requests_File() {
    return domain + "/requests.php";
}
let handleUserName = (id) => {
	$.post(FU_Ajax_Requests_File(), {
		f: 'action_call',
		s: 'call_user',
		call_user: id,
	}, function(response) {
		if (response && response.user_name) {
			$('#vcall_u' + id).text(response.user_name);
		}
	}, 'json'); // <- Tells jQuery to parse the response as JSON
};

let handleUserLeft = (user) => {
    console.log('Handle user left!')
    delete remoteTracks[user.uid]
    $(`#video-wrapper-${user.uid}`).replaceWith("");
	leaveCall();
}

let selfPlayer = (id) => {
    return `<div class="vcall_container" id="video-wrapper-${id}">
                <div class="vcall_player player" id="stream-${id}">
				</div>
            </div>`;
}
let joinPlayer = (id) => {
	return `<div class="vcall_container" id="video-wrapper-${id}">
				<p class="vcall_user"><img class="vcall_vol" id="volume-${id}" src="default_images/call/volume-on.svg" /><span id="vcall_u${id}"></span></p>
				<div  class="vcall_player player" id="stream-${id}">
				</div>
			</div>`;
}

let startStream = async () => {
    await joinStreams();
}

let upgradeCall = () => {
	$.post(FU_Ajax_Requests_File(), {
			f: 'action_call',
			s: 'upgrade_call',		
			upgrade_call: appcall,
		}, function(response) {
			if (response == 99) {
				leaveCall();
			}
	});	
};

$('#vcall_self').on('click', function() {
    if ($(this).hasClass('vcall_self')) {
        $(this).removeClass('vcall_self');
    } else {
        $(this).addClass('vcall_self');
    }
});

$('#vcall_mic').on('click', async () => {
    if(!localTrackState.audioTrackMuted){
        await localTracks.audioTrack.setMuted(true);
        localTrackState.audioTrackMuted = true
        $('#vcall_mic').addClass('vcall_off');
    }else{
        await localTracks.audioTrack.setMuted(false)
        localTrackState.audioTrackMuted = false
        $('#vcall_mic').removeClass('vcall_off');
    }
})

$('#vcall_cam').on('click', async () => {
    if(!localTrackState.videoTrackMuted){
        await localTracks.videoTrack.setMuted(true);
        localTrackState.videoTrackMuted = true
		$('#vcall_cam').addClass('vcall_off');
    }else{
        await localTracks.videoTrack.setMuted(false)
        localTrackState.videoTrackMuted = false
        $('#vcall_cam').removeClass('vcall_off');
    }
})

$('#vcall_leave').on('click', async () => {
    await leaveCall();
})

$(document).ready(function(){
	$.ajaxSetup({
		data: { token: utk, cp: curPage }
	});
	startStream();
	callUpgrade = setInterval(upgradeCall, 10000);
	upgradeCall();
});