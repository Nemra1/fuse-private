<?php 
require('system/config_call.php');

$bbfv = boomFileVersion();

$call_type = 0;
if(isset($_GET['call'])){
	$id = escape($_GET['call'], true);
	$call = callDetails($id);
	if(!empty($call)){
		if(myself($call['call_hunter']) || myself($call['call_target'])){
			$call_type = $call['call_type'];
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Call</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='css/main.css<?php echo $bbfv; ?>'>
    <link rel='stylesheet' type='text/css' media='screen' href='css/colors.css<?php echo $bbfv; ?>'>
	<link rel="stylesheet" type="text/css" href="css/fonts/remixicon.css?v=6.3" />
	<script data-cfasync="false" src="js/jquery-3.7.1.min.js<?php echo $bbfv; ?>"></script>
	<?php if(boomLogged()){ ?>
	<script data-cfasync="false">
		var utk = '<?php echo setToken(); ?>';
		var curPage = 'call';
	</script>
	<?php } ?>
</head>
<body class="call_back">
	<?php
	if($data['call_server_type']=="agora"){
		if($call_type == 1){
			include('control/video_call.php');
		}
		else if($call_type == 2){
			include('control/audio_call.php');
		}
		else {
			include('control/end_call.php');
		}		
	}else if($data['call_server_type']=="peerjs"){
		if($call_type == 1){
			include('control/peerjs_video_call.php');
		}
		else if($call_type == 2){
			include('control/peerjs_audio_call.php');
		}
		else {
			include('control/end_call.php');
		}		
	}

	?>
</body>
</html>