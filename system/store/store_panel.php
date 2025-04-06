<?php
$store = array();
$store['gold_pack'] = FU_store_market("gold");
$store['rank_pack'] = FU_store_market("rank");
$store['premium'] = FU_store_market("premium");

function Fu_premiumName($plan, $user){	
	if($plan == 0){
		$loadd = '<p class="label">No premium</p>';
	}
	else if($plan == 1){
		$loadd = '<p class="label" title="premium 7 days"><i class="ri-vip-crown-2-line"></i> 7/D</p>';
	}else if($plan == 2){
		$loadd = '<p class="label" title="premium 15 days"><i class="ri-vip-crown-2-line"></i> 15/D</p>';
	}
	else if($plan == 3){
		$loadd = '<p class="label" title="Primum 1 month"><i class="ri-vip-crown-2-line"></i> 1/M</p>';
	}
	else if($plan == 4){
		$loadd = '<p class="label" title="Premium 6 months"><i class="ri-vip-crown-2-line"></i> 6/M</p>';
	}
	else if($plan == 5){
		$loadd = '<p class="label" title="Premium 1 year"><i class="ri-vip-crown-2-line"></i> 1/Y</p>';
	}
	else {
		$loadd = '';
	}
	return $loadd;
}
?>
<style>
.store_grid-container,.store_frames_container { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); grid-gap: 5px; padding-bottom: 15px; }
.store_card { width: 140px; height: 140px; overflow: hidden; background-position: center; background-repeat: no-repeat; background-size: contain; border-radius: 15px; }
.store_content_gold{ background: #b92eff8c; background: -webkit-linear-gradient(to right, #8E54E9, #4776E6); background: linear-gradient(to right, #8e54e98c, #b92effcc); }
.store_content_rank{ background: #b92eff8c; background: -webkit-linear-gradient(to right, #8E54E9, #4776E6); background: linear-gradient(to right, #00000021, #fb832e63); }
.store_content { height: 100%; width: 100%; position: relative; margin: 0 auto; border-radius: 10px; }
.store_logo{ position: absolute; right: 0; left: 0; top: 30%; }
.store_logo img{ width: 50px; height: 50px; display: flex; justify-content: center; justify-items: center; margin: 0 auto; border-radius: 50%; }
.store_main_text{ position: relative; margin: 0 auto; text-align: center; display: flex; justify-content: center; justify-items: center; top: 4% }
.store_price_text{ position: relative; margin: 0 auto; text-align: center; display: flex; justify-content: center; justify-items: center; top: 47%; }
.store_price_text button{ padding: 4px; border-radius: 20px; font-size: smaller; background: #4776E6; background: -webkit-linear-gradient(to right, #8E54E9, #4776E6); background: linear-gradient(to right, #8E54E9, #4776E6); font-weight: bold; font-style: italic; font-size: small; }
.store_main_text p { padding: 5px; background: #000; border-radius: 20px; font-size: smaller; color: white; }
.pack_amount{ position: relative; margin: 0 auto; text-align: center; display: flex; justify-content: center; justify-items: center; top: 49%; color: floralwhite; font-size: small; font-weight: bold; }
.store_card { position: relative; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0px 0px 5px 2px #fb832e8a; }
.store_card:hover { transform: scale(1.05); }
.store_card input[type="radio"] { display: none;}
.store_card input[type="radio"]:checked + .store_content { border: 2px solid #FFD700;box-shadow: 0 0 10px rgba(255, 215, 0, 0.5);}
.check-icon { display: none; position: absolute; top: 0px; right: 0px; color: #fff; font-size: 22px; background: #02bd02; border-radius: 50%; width: 25px; height: 25px; text-align: center; }
.store_card input[type="radio"]:checked + .store_content .check-icon { display: block; }
.pack-detail { display: flex; flex-direction: column; gap: 10px; max-width: 600px; margin: auto; padding-top: 10px; }
.pack-detail-item { display: flex; align-items: center; padding: 5px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease; border-radius: 10px; border: 2px dotted #f7a211; }
.pack-detail-item img { width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; transition: transform 0.3s ease; }
.pack-detail-info { display: flex; flex-direction: column; }
.pack-detail-info .pack-name { font-size: 18px; font-weight: bold;}
.pack-detail-info .p_amount { font-size: 14px; margin-top: 2px; }
.wing-images{ width: 35px !important; height: 35px !important; transition: transform 0.3s ease; text-align: center; }
@media screen and (max-width:768px){
.store_grid-container {grid-template-columns: repeat(auto-fill, minmax(95px, 1fr)); grid-gap: 7px; }
.store_card { width: 100px; height: 115px; border-radius: 10px; overflow: hidden;background-size: cover;}	
.store_logo { top:22%; }
.store_main_text { top: 1%; font-size: smaller;}	
.pack_amount { top: 41%; }
.store_price_text { top:41% }
.store_main_text p {padding: 4px;}
.store_logo img { width: 40px; height: 40px; }
}

</style>
<div class="modal_menu">
	<ul>
		<li id="premium_button" class="modal_menu_item modal_selected" data="fuse_store" data-z="premuim_item"><i class="ri-vip-line"></i><?php echo $lang['store_user_premium'];?></li>
		<li id="gold_button" class="modal_menu_item " data="fuse_store" data-z="gold_tabe"><i class="ri-account-circle-line"></i><?php echo $lang['gold'];?></li>
		<li id="rank_button" class="modal_menu_item" data="fuse_store" data-z="rank_level"><i class="ri-rotate-lock-line"></i><?php echo $lang['store_user_rank'];?></li>
		<?php if(canPhotoFrame() && useFrame()){ ?>
		<li id="frames_button"  onclick="getFrames();" class="modal_menu_item" data="fuse_store" data-z="avatar_frames"><i class="ri-rotate-lock-line"></i><?php echo $lang['store_luxury_frames'];?></li></li>
		<?php } ?>
		<?php if(useWings()){ ?>
			<li id="wings_button" onclick="getWings();" class="modal_menu_item" data="fuse_store" data-z="wings_item"><i class="ri-rotate-lock-line"></i><?php echo $lang['store_luxury_wings'];?></li>
		<?php } ?>
		<li onclick="load_premium();" class="modal_menu_item" data="fuse_store"><i class="ri-bard-line"></i><?php echo $lang['store_premium_panel'];?></li>
	</ul>
</div>
<div id="fuse_store">
<input  type="hidden" name="selected_tab" id="selected_tab" value="gold_tabe" style="display:none;">
	<div id="store_alert"></div>
	<div class="modal_zone pad10 hide_zone" id="gold_tabe">
	<div class="store_container">
		<div class="store_grid-container">
			<?php
			if (!empty($store['gold_pack'])) {
			foreach ($store['gold_pack'] as $key) { ?>    
			<label for="gold_pack_<?php echo $key['id']?>" data-pack-name="<?php echo $key['pack_name']?>" class="store_card" style="background-image: url(<?php echo $key['image']?>);">
				<input data-type="gold_tab" type="radio" data-id="<?php echo $key['id']?>" name="pack_selection" id="gold_pack_<?php echo $key['id']?>" value="<?php echo $key['p_amounts']?>" style="display:none;">
					<div class="store_content store_content_gold">
						<div class="store_logo">
						  <img src="default_images/icons/gold_coin.gif" alt="" class="image">
						</div>	
						<div class="store_main_text">
						  <p><i class="ri-shield-star-line"></i><?php echo $key['pack_name']?></p>
						</div>
						<div class="pack_amount"><i class="ri-copper-diamond-fill"></i><?php echo $key['p_amounts']?></div>						
						<div class="store_price_text">
						  <button class="btn"> <?php echo $lang['store_price'];?>: <i class="ri-money-dollar-circle-line"></i><?php echo $key['price']?></button>
						</div>
						<i class="ri-check-line check-icon"></i>				
					</div>
			</label>
			<?php } 
			}
		?> 

		</div>
	</div>
	</div>
	<div class="modal_zone pad10 hide_zone" id="rank_level">
		<div class="clearbox"></div>
		<div class="store_container">
			<div class="store_grid-container">
			<?php
				if (!empty($store['rank_pack'])) {
				foreach ($store['rank_pack'] as $key) {
					$is_checked = ($data['user_rank'] == $key['user_rank']) ? 'checked' : '';
					?>    
				<label  for="rank_pack_<?php echo $key['id']?>" data-pack-name="<?php echo $key['pack_name']?>" class="store_card" style="background-image: url(<?php echo $key['image']?>);">
					<input data-type="rank_tab" type="radio" data-id="<?php echo $key['id']?>" name="pack_selection" id="rank_pack_<?php echo $key['id']?>" value="<?php echo $key['price']?>" style="display:none;" <?php echo $is_checked; ?>>
						<div class="store_content store_content_rank">
							<div class="store_logo">
							  <img src="default_images/rank/<?php echo rankIcon($key['user_rank']); ?>" alt="" class="image">
							</div>	
							<div class="store_main_text">
							  <p><i class="ri-shield-star-line"></i><?php echo $key['pack_name']?></p>
							</div>
							<!-- disable this
							<div class="pack_amount"><?php echo Fu_premiumName($key['prim_end'],$data)?></div>	
							!-->							
							<div class="store_price_text">
							  <button class="btn"> <?php echo $lang['store_price'];?>: <i class="ri-money-dollar-circle-line"></i><?php echo $key['price']?></button>
							</div>
							<i class="ri-check-line check-icon"></i>				
						</div>
				</label>
				<?php }
				}
				?> 
			</div>
		</div>
	</div>
	<?php if(canPhotoFrame() && useFrame()){ ?>
	<div class="modal_zone pad10 hide_zone" id="avatar_frames">
		<div class="store_container">
			<div class="store_frames box_height500" style=" position: relative; min-height: 320px; ">
			</div>
		</div>
	</div>
	<?php } ?>
	<?php if(useWings()){ ?>
	<div class="modal_zone pad10 hide_zone" id="wings_item">
		<div class="store_container">
			<div class="store_wings box_height500" style=" position: relative; min-height: 320px; ">
			</div>
		</div>
	</div>
	<?php } ?>
	<div class="modal_zone pad10" id="premuim_item">
			<div class="store_container">
				<div class="store_grid-container">
				<?php
				if (!empty($store['premium'])) {
				foreach ($store['premium'] as $key) {
					$is_checked = ($data['user_prim'] == $key['prim_end']) ? 'checked' : '';
					?> 
					<label for="premium_pack_<?php echo $key['id']?>" data-pack-name="<?php echo $key['pack_name']?>" class="store_card" style="background-image: url(<?php echo $key['image']?>);">
						<input data-type="premium_tab" type="radio" data-id="<?php echo $key['id']?>" name="pack_selection" id="premium_pack_<?php echo $key['id']?>" value="<?php echo $key['price']?>" style="display:none;" />
						<div class="store_content store_content_gold">
							<div class="store_logo">
								<img src="<?php echo $key['image']?>" alt="" class="image" />
							</div>
							<div class="store_main_text">
								<p><i class="ri-shield-star-line"></i><?php echo $key['pack_name']?></p>
							</div>
							<div class="store_price_text">
								<button class="btn"><?php echo $lang['store_price'];?>: <i class="ri-money-dollar-circle-line"></i><?php echo $key['price']?></button>
							</div>
							<i class="ri-check-line check-icon"></i>
						</div>
					</label>
				<?php }
				}
				?> 					
				</div>
			</div>

	</div>	
	<div class="pack-detail" id="order_list">
		<div class="pack-detail-item">
			<img src="default_images/icons/coin_placeholder.png" alt="Package Thumb" />
			<div class="pack-detail-info">
				<div class="pack-name">Package Name</div>
				<div class="p_amount">Amount</div>
			</div>
		</div>
	</div>
<div class="action flex-center tpad10">
    <button id="store_buy_button" type="button" class="b-main-color pointer" style=" display: none; "><?php echo $lang['store_buy']?></button>
    <button type="button" class="delete_btn pointer close_over"><?php echo $lang['cancel']?></button>
 </div> 	
</div>
<script>
$(document).ready(function(){
	var selected_tab = $('#selected_tab');
	$(document).on('click', '#store_buy_button', function(event) {
		var button = $(this);
		// Prevent multiple clicks
		if (button.prop('disabled')) return;
		button.prop('disabled', true); // Disable the button
		var tabValue = selected_tab.val(); 
		if (tabValue === "gold_tab" || tabValue === "rank_tab" || tabValue === "premium_tab") {
			console.log(tabValue);
			buy_pack(tabValue);
		} else if(tabValue === "frame_tab") {
			buy_frame();
		} else if(tabValue === "wings_tab") {
			buy_wing();
		}  else {
			console.log("Selected tab is neither gold_tab nor rank_tab");
		}
		// Re-enable the button after a delay (or inside AJAX success callback)
		setTimeout(function() {
			button.prop('disabled', false);
		}, 2000); // Adjust delay as needed
	});

 
	$(document).on('change', 'input[name="pack_selection"]', function(event) {
		if($(this).is(':checked')) {
			var selectedLabel = $('label[for="' + $(this).attr('id') + '"]');
			var amount = $(this).val();
			var packName = selectedLabel.data('pack-name');
			var imageSrc = selectedLabel.find('.image').attr('src');
			var buy_pack_btn = $('#store_buy_button');
			var checked_type = $(this).data('type');
			selected_tab.val(checked_type);
			 // Update pack details in the .pack-detail section
			$('#order_list .pack-name').text(packName);
			$('#order_list .p_amount').text(amount);
			$('#order_list img').attr('src', imageSrc);
			if (amount && amount.length > 0) {
				buy_pack_btn.show(); // Show the send button
			} else {
				buy_pack_btn.hide(); // Hide the send button if nothing is selected
			}                
		  }
	});
});
function buy_pack(tabValue) {
    var pack_type = $('input[name="pack_selection"]').filter(":checked").val();
    var pack_id = $('input[name="pack_selection"]').filter(":checked").data('id');
    if (pack_type === undefined) {
        console.log('Something is wrong');
    } else {
        $.ajax({
            url: FU_Ajax_Requests_File(),
            type: "POST",
            data: {
                f: 'store',
                s: 'buy_pack',
                id: pack_id,
                token: utk
            },
            cache: false,
            success: function(data) {
                var alert_msg;
				var r = data;
                if (r.status === 200) {
                    alert_msg = $("<div>", {
                        class: "alert alert-success",
                        text: r['message']
                    }).prepend('<i class="ri-checkbox-circle-fill"></i>');
                    $("#store_alert").html(alert_msg);
                    callSaved(r['message'], 1);
                    $('.gold_counter').html(r['message'].user_gold);
                    okayPlay();
					//hideOver();
                } else {
                    alert_msg = $("<div>", {
                        class: "alert alert-danger",
                        text: r['message']
                    }).prepend('<i class="ri-xrp-line"></i>');
                    $("#store_alert").html(alert_msg);
                    callSaved(r['message'], 3);
                }
            },
            error: function(xhr, status, error) {
                console.log('An error occurred');
            }
        });
    }

    return false;
}
function getWings(){
    $.post(FU_Ajax_Requests_File(), {
            f: "store",
            s: "get_wings",
            token:utk,
        },
        function(r) {
			$(".store_wings").html(largeSpinner);
			if(r.status ==200){
				$(".store_wings").html(r.html);
				 //callSaved(r['message'], 1);
			}else{
				callSaved(r['message'], 3);
			}
     });
}
function buy_wing() {
    const savedWing = sessionStorage.getItem('selectedWing');
    if (savedWing) {
        const selectedWing = JSON.parse(savedWing);
        $.post(FU_Ajax_Requests_File(), {
                f: "store",
                s: "buy_wings",
                wing: selectedWing,
                token: utk,
            },
            function(r) {
                if (r.status == 200) {
					 userReload(1);
                    callSaved(r['message'], 1);
                } else {
                    callSaved(r['message'], 3);
                }
            });
    } else {
        alert("Please select a wing before proceeding with the purchase.");
    }
}
function selectWing(baseName, wing1Url, wing2Url, goldPrice,ext) {
	let selectedWing = {};
    // Store the wing information in an object
    selectedWing = {
        baseName: baseName,
        wing1Url: wing1Url,
        wing2Url: wing2Url,
        goldPrice: goldPrice,
        ext: ext,
    };
    // Optionally, store this information in sessionStorage for later use
    sessionStorage.setItem('selectedWing', JSON.stringify(selectedWing));
    console.log("Wing selected:", selectedWing);
}
<?php if(canPhotoFrame()){ ?>
var isProcessing = false; // Flag to prevent duplicate submissions
function getFrames(){
    $.post(FU_Ajax_Requests_File(), {
            f: "store",
            s: "get_frames",
            token:utk,
        },
        function(r) {
			$(".store_frames").html(largeSpinner);
			if(r.status ==200){
				$(".store_frames").html(r.html);
				 //callSaved(r['message'], 1);
			}else{
				callSaved(r['message'], 3);
			}
     });
}

function buy_frame() {
    if (isProcessing) {
        console.log("Request is already being processed.");
        return; // Prevent further execution if a request is in progress
    }

    // Check if any radio input for pack_selection is checked
    var frame_elm = $('input[name="pack_selection"]:checked'); // Select the checked input
    if (frame_elm.length > 0) { // Ensure at least one is checked
        var amount = frame_elm.val(); // Get the value (amount) of the checked input
        var checked_type = frame_elm.data('type');
        var ext = frame_elm.data('ext');
        var ext_id = frame_elm.data('id');
        console.log("Selected amount:", amount); // Log the selected amount
        isProcessing = true; // Set the flag to true to prevent duplicate submissions
        // Send AJAX request to buy the frame
        $.post(FU_Ajax_Requests_File(), {
            f: "store",
            s: "buy_frame",
            token: utk, // Assuming 'utk' is defined elsewhere in your script
            ant: amount, // Include the amount in the request
            ext: ext, // Pass the selected frame extension
            fid: ext_id, // Pass the selected frame id
        },
        function(r) {
            // Check the response from the server
            if (r.status === 200) {
                callSaved(r['message'].alert, 1); // Success handler
                userReload(1); // Reload user
            } else {
                callSaved(r['message'].alert, 3); // Error handler
            }
        }).always(function() {
            // Reset the flag after the request is completed
            isProcessing = false;
        });
    } else {
        console.log("No frame selected."); // Log if no frame is selected
        callSaved("Please select a frame to buy.", 2); // Inform user
    }
}
<?php } ?>

</script>