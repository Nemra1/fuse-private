<?php 
require('../config_session.php');

if(!isOwner($data)){
    die();
} 
?>
<style>
#SocketMonitor_wrap_stream { max-width: 640px; min-width: 520px; height: 360px; border-top: 1px solid #333; }
@media screen and (max-width:768px){
#SocketMonitor_wrap_stream { max-width: 640px; min-width: 370px; }
}
</style>
<div id="SocketMonitor" class="background_box">
	<div id="SocketMonitor_wrap_stream"></div>
</div>
<script data-cfasync="false">
FUSE_SOCKET.logSocket();
</script>