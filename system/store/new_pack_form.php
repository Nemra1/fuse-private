<form id="addPackForm" enctype="multipart/form-data" class="pad10">
    <div class="modal_content pad15">
    	<div class="pack_image">
    		<img  id="avatarHolder" class="pack_edit_img"/></div>
            <input type="file" id="avatar" name="avatar_pack" accept="image/*">
    	</div>
    	<div class="setting_element ">
    		<p class="label">Package Name</p>
    		<input id="set_pack_title" name="packge_name" class="full_input" value="" type="text"/>
    </div>
    	<div class="setting_element ">
    		<p class="label">Packge Type</p>
    		<select id="set_packge_type"  name="packge_type" >
            <option value="gold">Gold</option>
            <option  value="rank">User Rank</option>
            <option  value="premium">User Premium</option>
    		</select>
		</div>	
		<div class="setting_element"  id="premium_expiry_div">
			  <p class="label">Premium expiry determinant</p>
				<select id="set_prim_end" name="prim_end">
					<option value="0">No premium</option>
					<option value="7">Premium 7 days</option>
					<option value="15">Premium 15 days</option>
					<option value="30">Premium 1 month</option>
					<option value="180">Premium 6 months</option>
					<option value="365">Premium 1 year</option>

			   </select>
		</div>

		
		<div class="setting_element" id="pack_rank_div">
				<p class="label">Choose Rank</p>
				<select id="set_pack_rank" name="pack_rank">
				<?php echo listRank(0, 0); ?>
				</select>
		</div>		
    	<div class="setting_element" id="pack_Price_div">
    		<p class="label label_text">Package Price</p>
    		<input id="set_pack_price" name="packge_price" class="full_input" value="" type="number"/>
			 <p id="gold_notice" style="display:none; color: red;">Price for this package type is in gold. min=1000</p>
		</div>
		
    <div class="setting_element" id="pack_amount_div">
    		<p class="label">Gold Amount</p>
    		<select id="set_pack_gold" name="packge_amount">
    		<option value="10000">10k</option>    
    		<option value="50000" >50k</option>    
    		<option value="100000">100k</option>     
    		<option value="500000">500k</option>     
    		<option value="900000">900k</option>        
    		<option value="1000000">1 MB</option>    
            <option value="2000000">2 MB</option>
            <option value="6000000">6 MB</option>
            <option value="12000000">12 MB</option>
            <option value="24000000">24 MB</option>
            <option value="48000000">48 MB</option>
            <option value="96000000">96 MB</option>
            <option value="256000000">256 MB</option>
            <option value="512000000">512 MB</option>
            <option value="1000000000">1 GB</option>
            <option value="5000000000">5 GB</option>
            <option value="10000000000">>10 GB</option>
    		</select>
    </div>
    	<div class="setting_element" id="pack_Discount_div">
    		<p class="label">Package Discount %</p>
    		<input id="set_pack_discount" name="packge_discount" class="full_input" value="" type="number"/>
    </div>
    <div class="setting_element ">
    		<p class="label">Package status</p>
                <select class="form-control show-tick" name="packge_status">
                <option value="1">on</option>
                <option value="0">off</option>
                </select>
    </div>	
    </div>
    <div class="pad20 centered_element">
    	<button  type="submit" class="reg_button theme_btn"><i class="ri-save-3-line"></i><?php echo $lang['save']; ?></button>
    	<button class="reg_button delete_btn cancel_modal"><?php echo $lang['cancel']; ?></button>
    </div>
</form>
    <script>
        $(document).ready(function(){

		function togglePackFields() {
				var selectedValue = $('#set_packge_type').val();
				if (selectedValue === 'gold') {
					$('#pack_Price_div').show().prop('disabled', false);
					$('#pack_Price_div .label_text').text('Package price Now is $USD');
					$('#set_pack_price').show().prop('disabled', false).attr('min', '1').val('1');
                    $('#gold_notice').hide();
					$('#pack_rank_div').hide().prop('disabled', true);
                    $('#premium_expiry_div').hide();
					$('#pack_amount_div').show().prop('disabled', false);
				} else if (selectedValue === 'rank') {
					$('#pack_Price_div').show().prop('disabled', false);
					$('#pack_Price_div .label_text').text('Package price Now is gold');
					$('#set_pack_price').show().prop('disabled', false).attr('min', '1000').val('1000');
                    $('#gold_notice').show();
					$('#pack_rank_div').show().prop('disabled', false);
					$('#pack_amount_div').hide().prop('disabled', true);
                    $('#premium_expiry_div').hide();
				} else if (selectedValue === 'premium') {
                    $('#pack_Price_div').show().prop('disabled', false);
                   $('#pack_Price_div .label_text').text('Package price Now is gold');
                    $('#gold_notice').show();
                    $('#pack_rank_div').hide().prop('disabled', true);
                    $('#pack_amount_div').hide().prop('disabled', true);
                    $('#premium_expiry_div').show();
                }
		}
		$('#set_packge_type').change(togglePackFields);
		togglePackFields();
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
			$('#addPackForm').on('submit', function(event){
				event.preventDefault();
				// Validation
				var price = $('#set_pack_price').val().trim();
				var discount = $('#set_pack_discount').val().trim();
				var selectedValue = $('#set_packge_type').val();
				if (price === '' || isNaN(price) || price <= 0) {
					alert('Please enter a valid price.');
					return;
				}
				if (selectedValue === 'gold' && price < 1000) {
					alert('Gold package price must be at least 1000.');
					return;
				}
				if (discount !== '' && (isNaN(discount) || discount < 0 || discount > 100)) {
					alert('Discount must be between 0 and 100.');
					return;
				}
				var formData = new FormData(this);
				formData.append("token", utk);
				formData.append("f", 'store');
				formData.append("s", 'add_pack');
				$.ajax({
					url: FU_Ajax_Requests_File(),
					type: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					success: function(r){
						if(r.status==200){
							edit_pack(r.result.lastInsertedId);
							callSaved(r.message, 1);
							okayPlay();
						} else {
							alert_msg = $("<div>", {
								class: "alert alert-danger",
								text: r['message']
							}).prepend('<i class="ri-checkbox-circle-fill"></i>');
							$("#store_alert").html(alert_msg);
							callSaved(r['message'], 3);
						}
					}
				});
			});

			
			
			
			
        });
    </script>