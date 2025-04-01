<?php

$gifts = array();
function userGiftList($id){
	global $mysqli, $data, $lang;
  	$id = escape($id);
	$id = cleanString($id);
	$gift = array();
     $query  = mysqli_query($mysqli, "SELECT * FROM `boom_users_gift` WHERE boom_users_gift.target ='{$id}' ORDER BY boom_users_gift.gift_count DESC");
	   if (mysqli_num_rows($query)) { 
	       while ($row = mysqli_fetch_assoc($query)) {
	        $gift_id = $row['gift'];
	        $to = userDetails($row['target']);
	        $from =  userDetails($row['hunter']);
	        $fu_gifts['gift_data'] =  gift_list_byId($gift_id);
	        $fu_gifts['gift_xtimes'] =  $row['gift_count'];
	        $fu_gifts['to'] =$to['user_name'];
	         $fu_gifts['from'] =$from['user_name'];
			 $gift[] = $fu_gifts; 
	       }    
	   }   
    return $gift;

}
if (isset($_POST['get_gift'],$_POST['user_id'])){
    $user_id = escape($_POST['user_id']);
	$user_id = cleanString($_POST['user_id']);
    $gifts['list'] =  userGiftList($user_id);
}
?>
<ul class="gift_list_container">
<?php
if (!empty($gifts['list'])) {
  foreach ($gifts['list'] as $key) { 
     $gift_data = $key['gift_data'];
  ?>
<li class="view_gift fborder bhover pgcard" onclick="play_gift(this)" 
	data-src="<?php echo $gift_data['gif_file']; ?>"
    data-to="<?php echo $key['to']; ?>"
    data-from="<?php echo $key['from']; ?>"
    data-price="<?php echo $gift_data['gift_cost']; ?>"
    data-gname="<?php $gift_data['gift_title']; ?>"
    data-icon="<?php echo $gift_data['gift_url']; ?>"
>
	<img class="pgcard_img" data-src="gift/clown.svg" src="<?php echo $gift_data['gift_url']; ?>">
	<div class="btable_auto gtag pgcard_count">
		<div class="bcell_mid text_small">
			<div class="btable_auto">
				<div class="bcell_mid hpad3 bold"><?php echo $key['gift_xtimes']; ?></div>
			</div>
		</div>
	</div>
</li>

 <?php 
    } 
}else{
   echo emptyZone($lang['empty']);
}
?>
</ul>