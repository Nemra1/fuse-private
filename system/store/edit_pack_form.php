<?php
if(!boomAllow(100)){
	echo 0;
	die();
}
if(!isset($_POST['pack_id'])){
	echo 0;
	die();
}
$target = escape($_POST['pack_id'], true);
$pack = $boom;

if(empty($pack)){
	echo 0;
	die();
}
?>
<form id="editPackForm" enctype="multipart/form-data" class="pad10">
<div class="modal_content pad15">
    	<div class="pack_image">
    		<img id="avatarHolder" class="pack_edit_img" style=" background-image: url(<?php echo $pack['image']; ?>); ">
            <input type="file" id="avatar" name="avatar_pack" accept="image/*">
    	</div>
	<div class="setting_element ">
		<p class="label">Package Name</p>
		<input id="set_pack_title" class="full_input"  name="packge_name" value="<?php echo $pack['pack_name']; ?>" type="text"/>
	</div>
	<div class="setting_element ">
		<p class="label">Packge Type</p>
		<select id="set_packge_type" name="packge_type" disabled>
        <option value="gold" <?php echo ($pack['type'] == "gold")   ? ' selected': '';?>>Gold</option>
        <option  value="rank" <?php echo ($pack['type'] == "rank")   ? ' selected': '';?>>User Rank</option>
        <option  value="premium" <?php echo ($pack['type'] == "premium")   ? ' selected': '';?>>User premium</option>
		</select>
	</div>
	<?php if ($pack['type']=='rank'){?>
			<div class="setting_element" id="pack_rank_div">
				<p class="label">Choose Rank</p>
				<select id="set_pack_rank" name="pack_rank">
					<?php echo listRank($pack['user_rank'], 0); ?>
				</select>
			</div>
			<div class="setting_element ">
				<p class="label">Rank expiry determinant</p>
				<select id="set_rank_end" name="rank_end" 
					<?php echo ($pack['type'] == "gold") ? 'disabled' : ''; ?>>
					<option <?php echo selCurrent($pack['rank_end'], 7); ?> value="7">7 days</option>
					<option <?php echo selCurrent($pack['rank_end'], 15); ?> value="15">15 days</option>
					<option <?php echo selCurrent($pack['rank_end'], 30); ?> value="30">30 days</option>
					<option <?php echo selCurrent($pack['rank_end'], 180); ?> value="180">6 months</option>
				</select>
			</div>			
	<?php
		}
	?>
	<div class="setting_element" id="pack_Price_div">
		<p class="label">Package Price Per <?php echo ($pack['type'] == 'premium' || $pack['type'] == 'rank') ? 'Gold' : '$USD'; ?></p>
		<input id="set_pack_price" class="full_input" value="<?php echo $pack['price']; ?>"  name="packge_price"  type="number"/>
	</div>
	
	<?php if ($pack['type']=='gold'){?>
	<div class="setting_element" id="pack_amount_div">
		<p class="label"><?php echo $lang['gold_require']; ?></p>
		<select id="set_pack_gold"  name="packge_amount">
		<option value="10000" <?php echo ($pack['p_amounts'] == 10000)   ? ' selected': '';?>>10k</option>    
		<option value="50000" <?php echo ($pack['p_amounts'] == 50000)   ? ' selected': '';?>>50k</option>    
		<option value="100000" <?php echo ($pack['p_amounts'] == 100000)   ? ' selected': '';?>>100k</option>     
		<option value="500000" <?php echo ($pack['p_amounts'] == 500000)   ? ' selected': '';?>>500k</option>     
		<option value="900000" <?php echo ($pack['p_amounts'] == 900000)   ? ' selected': '';?>>900k</option>        
		<option value="1000000" <?php echo ($pack['p_amounts'] == 1000000)   ? ' selected': '';?>>1 MB</option>    
        <option value="2000000" <?php echo ($pack['p_amounts'] == 2000000)   ? ' selected': '';?>>2 MB</option>
        <option value="6000000" <?php echo ($pack['p_amounts'] == 6000000)   ? ' selected': '';?>>6 MB</option>
        <option value="12000000" <?php echo ($pack['p_amounts'] == 12000000)   ? ' selected': '';?>>12 MB</option>
        <option value="24000000" <?php echo ($pack['p_amounts'] == 24000000)   ? ' selected': '';?>>24 MB</option>
        <option value="48000000" <?php echo ($pack['p_amounts'] == 48000000)   ? ' selected': '';?>>48 MB</option>
        <option value="96000000" <?php echo ($pack['p_amounts'] == 96000000)   ? ' selected': '';?>>96 MB</option>
        <option value="256000000" <?php echo ($pack['p_amounts'] == 256000000)   ? ' selected': '';?>>256 MB</option>
        <option value="512000000" <?php echo ($pack['p_amounts'] == 512000000)   ? ' selected': '';?>>512 MB</option>
        <option value="1000000000" <?php echo ($pack['p_amounts'] == 1000000000)   ? ' selected': '';?>>1 GB</option>
        <option value="5000000000" <?php echo ($pack['p_amounts'] == 5000000000)   ? ' selected': '';?>>5 GB</option>
        <option value="10000000000" <?php echo ($pack['p_amounts'] == 10000000000)   ? ' selected': '';?>>10 GB</option>
		</select>
	</div>
	<?php
		}
	?>
	
	<?php if ($pack['type']=='premium'){?>	
	<div class="setting_element">
		<p class="label">Premium expiry determinant</p>
		<select id="set_prim_end" name="prim_end" 
			<?php echo ($pack['type'] == "gold") ? 'disabled' : ''; ?>>
			<option <?php echo selCurrent($pack['prim_end'], 0); ?> value="0">No premium</option>
			<option <?php echo selCurrent($pack['prim_end'], 7); ?> value="7">Premium 7 days</option>
			<option <?php echo selCurrent($pack['prim_end'], 15); ?> value="15">Premium 15 days</option>
			<option <?php echo selCurrent($pack['prim_end'], 30); ?> value="30">Premium 1 month</option>
			<option <?php echo selCurrent($pack['prim_end'], 180); ?> value="180">Premium 6 months</option>
			<option <?php echo selCurrent($pack['prim_end'], 365); ?> value="365">Premium 1 year</option>
		</select>
	</div>
	<?php
		}
	?>	
	<div class="setting_element" id="pack_Discount_div">
		<p class="label">Package Discount %</p>
		<input id="set_pack_discount" class="full_input" value="<?php echo $pack['discount']; ?>" name="packge_discount" type="number"/>
	</div>
	<div class="setting_element ">
		<p class="label">Package status</p>
            <select class="form-control show-tick" name="packge_status">
            <option value="1" <?php echo ($pack['status'] == "1")   ? ' selected': '';?>>on</option>
            <option value="0" <?php echo ($pack['status'] == "0")   ? ' selected': '';?>>off</option>
            </select>
	</div>	
</div>
<div class="pad20 centered_element">
	<button  id="editPackForm_submit" type="button" class="reg_button theme_btn"><i class="ri-save-3-line"></i><?php echo $lang['save']; ?></button>
	<button class="reg_button default_btn cancel_modal"><?php echo $lang['cancel']; ?></button>
	<button onclick="deletePack(<?php echo $pack['id']; ?>);" class="reg_button delete_btn cancel_modal"><?php echo $lang['delete']; ?></button>
</div>
</form>
    <script>
        $(document).ready(function(){

           // Trigger file input when clicking on the avatar holder
            $('#avatarHolder').on('click', function(){
                $('#avatar').click();
            });
            // Update the avatar holder with the selected image
            $('#avatar').on('change', function(){
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#avatarHolder').css('background-image', 'url(' + e.target.result + ')');
                        $('#avatarHolder').text(''); // Clear any text inside the holder
                    }
                    reader.readAsDataURL(file);
                }
            });            

			function editPackForm(event, packId) {
				event.preventDefault(); // Prevent default form submission
				var formData = new FormData($('#editPackForm')[0]); // Create FormData object from the form
				formData.append("token", utk);
				formData.append("f", 'store');
				formData.append("s", 'edit_pack');
				formData.append("pack_id", packId);
				$.ajax({
					url: FU_Ajax_Requests_File(),
					type: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					success: function(response) {
						if (response.status == 200) {
							callSaved(response.message, 1);
						} else {
							// Handle non-200 responses
							callSaved(response.message || 'Error editing the pack', 3);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						callSaved('Error: ' + textStatus + ' - ' + errorThrown, 3);
					}
				});
			}
			// Example usage for the form
			$('#editPackForm_submit').on('click', function(event) {
				editPackForm(event, '<?php echo $pack["id"]; ?>'); // Pass the pack ID dynamically
			});

        });
    </script>