<style>
.user_item { cursor: pointer; }
input[type="radio"]:checked + .user_item_avatar { border: 2px solid #f94e00; border-radius: 20%; }
.user_item_avatar { width: 40px; }
</style>
<div class="modal_menu" style=" justify-content: center; display: flex; ">
		<ul>
			<li class="modal_menu_item modal_selected" data="meditprofile" data-z="personal_Wallet"><i class="ri-wallet-3-fill"></i>Wallet</li>
			<li class="modal_menu_item" data="meditprofile" data-z="proselfsend"><i class="ri-money-dollar-circle-line"></i>Send Money</li>
			<li class="modal_menu_item" data="meditprofile" data-z="proselftrans" onclick="get_transaction(1)"><i class="ri-money-dollar-circle-line"></i>Transaction</li>
		</ul>
	</div>
<div id="meditprofile">
        <section class="modal_zone" id="personal_Wallet">
        <div class="screen flex-center">
    	<div id="submit_payment" action="post" class="popup_wallet flex p-md">
    		<!-- CARD FORM -->
    		<div class="flex-fill flex-vertical">
    			<div class="wallet_header flex-between flex-vertical-center">
    				<div class="flex-vertical-center">
    					<i class="ri-btc-line size-xl pr-sm f-main-color"></i>
    					<span class="title"> <strong>Your Wallet</strong><span>Panel</span> </span>
    				</div>
    				<div class="points_countr" data-id="points">
    				  <div class="f-main-color pointer">  <i class="ri-copper-coin-line"></i> Coins: <span><?php echo $data['user_gold']; ?></span> </div>
    				</div>
    			</div>
    			<div class="card-data flex-fill flex-vertical">
    				<!-- Card Number -->
    				<div class="flex-between flex-vertical-center">
    					<div class="card-property-title">
    						<strong>Pickup Amount</strong>
    						<span>Choose How much You would to withdraw</span>
    					</div>
    					<div class="f-main-color pointe points_countr"><i class="ri-money-dollar-circle-line"></i> Credit:<span> <?php echo $data['wallet']; ?>(<?php echo $data['currency']; ?>)</span></div>
    				</div>
    
    				<!-- Card Field -->
    				<div class="flex-between">
    					<div class="card-number flex-vertical-center flex-fill">
    						<div class="card-number-field flex-vertical-center flex-fill">
                                  <div class="card-property-value flex-vertical-center row">
                                	<div class="input-container half-width">
                                		<input class="numbers" data-bound="mm_mock" data-def="00" type="radio" name="amount_pack" id="amount_pack_1" min="10" value="10" checked="" />
                                		<label for="amount_pack_1" class="half-width">10</label>
                                	</div>
                                    <div class="input-container half-width">
                                		<input class="numbers" data-bound="mm_mock" data-def="00" type="radio" name="amount_pack" id="amount_pack_2" min="15" value="15" checked="" />
                                		<label for="amount_pack_1" class="half-width"> 15</label>
                                	</div> 
                                    <div class="input-container half-width">
                                		<input class="numbers" data-bound="mm_mock" data-def="00" type="radio" name="amount_pack" id="amount_pack_3" min="25" value="25" checked="" />
                                		<label for="amount_pack_1" class="half-width">25</label>
                                	</div>
                                    <div class="input-container half-width">
                                		<input class="numbers" data-bound="mm_mock" data-def="00" type="radio" name="amount_pack" id="amount_pack_4" min="35" value="35" checked="" />
                                		<label for="amount_pack_1" class="half-width">35</label>
                                	</div>                               	
                                </div>
  							
    						</div>
    					</div>
    				</div>
    
    				<!-- Expiry Date -->
    				<div class="flex-between">
    					<div class="card-property-title">
    						<strong>Your Country</strong>
    						<span>Select Your Country Name</span>
    					</div>
    					<div class="card-property-value flex-vertical-center">
    						<div class="input-container full-width ">
                            	<select id="payment_profile_language">
                            		<?php echo listCountry($data['country']); ?>
                            	</select>
    						</div>
    					</div>
    				</div>
    
    				<!-- CCV Number -->
    				<div class="flex-between">
    					<div class="card-property-title">
    						<strong>Your Phone number</strong>
    						<span>Ex - +2010000000</span>
    					</div>
    					<div class="card-property-value">
    						<div class="input-container">
    							<input id="user_phone" type="text" />
    						</div>
    					</div>
    				</div>
    
    				<!-- Name -->
    				<div class="flex-between">
    					<div class="card-property-title">
    						<strong>Payment Provider</strong>
    						<span>Choose your payment method</span>
    					</div>
    					<div class="card-property-value">
    						
    						<div class="card-property-value flex-vertical-center row">
                               	<div class="input-container full-width input-container-payment" style="background-repeat: no-repeat; background-size: contain;background-image: url(./default_images/payment/paypal.png);">
                                		<input class="form-check-input" type="radio" required="true" id="paymentOption-paypal" name="paymentOption" value="paypal" checked/>
                                		<label for="paymentOption-securionpay" class="full-width"></label>
                                	</div>
                                </div>

    					</div>
    				</div>
    			</div>
    			<div class="action flex-center">
    				<button id="deposit_button" onclick="deposit_button();" type="button" class="b-main-color pointer">Deposit</button>
    					<button  type="button" class="delete_btn pointer cancel_modal">Cancel</button>
    			</div>
    		</div>
    
    		<!-- SIDEBAR -->
    	</div>
    </div>
        </section>
        <section class="modal_zone hide_zone pad10 tpad10" id="proselfsend" value="0">
            <div class="form" id="send-money-form" autocomplete="off">
                <div id="sender-box-pro-form-alert"></div>
            	<!-- Name -->
            	<div class="flex-between pb-md">
            		<div class="card-property-title">
            			<strong>Amount</strong>
            			<span>How much you would to send?</span>
            		</div>
            		<div class="card-property-value">
            			<div class="input-container">
            				<input type="number" placeholder="0" min="1" max="1000" name="amount_to_user" id="amount_to_user" class="uppercase" />
            				<i class="ai-person"></i>
            			</div>
            		</div>
            	</div>
            	<div class="flex-between pb-md">
            		<div class="card-property-title">
            			<strong>Your Friend Name</strong>
            			<span>Search by Username or first name</span>
            		</div>
            		<div class="card-property-value">
            			<div class="input-container">
            				<input id="amount_search" type="text" name="amount_search" placeholder="Search by username or email" class="uppercase" />
            				<input type="hidden" id="recipient_user_id" name="user_id" />
            				<i class="ai-person"></i>
            			</div>
            		</div>
            	</div>
            	<div class="search_users_content donate_search_users_content"></div>
            	<div class="action flex-center">
            	    <input type="hidden"  min="1" name="recev_user_id" value="" id="recev_user_id" />
    				<button id="sendMoney_button" type="button" class="b-main-color pointer" style="display:none">Send</button>
    				<button type="button" class="delete_btn pointer cancel_modal">Cancel</button>
    			</div>
            </div>
            
        </section>    
        <section class="modal_zone hide_zone pad10 tpad10" id="proselftrans" value="0">
        	<div class="form" id="send-money-form" autocomplete="off">
        		<!-- Name -->
                <div class="transaction_content box_height600"></div>
        		<div class="action flex-center">
        			<button type="button" class="delete_btn pointer cancel_modal">Cancel</button>
        		</div>
        	</div>
        </section>

    </div>
    

<script>
$(document).ready(function() {
    _body.on('click', '#deposit_button', function(event) {
    });
    $(document).on('keyup', '#amount_search', function(event) {
                var q = $('#amount_search').val();
                console.log("Input value:", q); // Debugging line to check input value
                if (q.length > 3) {
                    $.ajax({
                         url: FU_Ajax_Requests_File(),
                        type: "POST",
                        data: {
                            f: 'wallet',
                            s: 'send_money_search',
                            q: q,
                            token: 'your_token', // Replace with actual token
                            search_box: 'wallet_search'
                        },
                        success: function(response) {
                            $(".donate_search_users_content").html(response);
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error:", status, error); // Handle errors
                        }
                    }); 
                }
            });   
        $(document).on('change', '#send-money-form label.confirm_uid', function() {
            var selectedId = $('[name=user_selection]:checked').val();
            var send_btn = $('#sendMoney_button');
            var recev_user_id = $('#recev_user_id');
            recev_user_id.val(selectedId);
            if (selectedId && selectedId.length > 0) {
                send_btn.show(); // Show the send button
            } else {
                send_btn.hide(); // Hide the send button if nothing is selected
            }
        });
     $(document).on('click', '#sendMoney_button', function(event) {
        var recev_amount = $('#amount_to_user').val();
         var recev_id =  $('#recev_user_id').val();
        $.ajax({
            url: FU_Ajax_Requests_File(), // The same page for handling the payment
            type: 'POST',
            dataType: 'json',
            data: { 
         	    f:'wallet',
        	    s:'send',
        	    amount_to_user:recev_amount,
        	    user_id:recev_id,
        		token: utk,
            },
            success: function(data) {
                var alert_msg;
                if (data.status === 200) {
                    alert_msg = $("<div>", {
                        class: "alert alert-success",
                        text: data['message'].alert
                    }).prepend('<i class="ri-checkbox-circle-fill"></i>');
                    $("#sender-box-pro-form-alert").html(alert_msg);
                    callSaved(data['message']['alert'], 1);
                    okayPlay();
                } else {
                    alert_msg = $("<div>", {
                        class: "alert alert-danger",
                        text: data['message'].alert
                    }).prepend('<i class="ri-xrp-line"></i>');
                    $("#sender-box-pro-form-alert").html(alert_msg);
                    callSaved(data['message']['alert'], 3);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
            }
        });      
  });
});
deposit_button = function(){
        console.log("Button clicked"); // Debugging log
        var checkedAmount = $('input[name="amount_pack"]:checked');
        console.log("Checked amount:", checkedAmount.val()); // Debugging log
        $.ajax({
            url: FU_Ajax_Requests_File(),
            type: 'POST',
            dataType: 'json',
            data: { 
                action: 'create_payment',
                f: 'wallet',
                s: 'pay_paypal',
                amount: checkedAmount.val(),
                token: utk,
            },
            success: function(response) {
                console.log("AJAX success", response); // Debugging log
                if (response.approvalUrl) {
                    window.location.href = response.approvalUrl;
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX error:", textStatus); // Debugging log
                alert('AJAX error: ' + textStatus);
            }
        });
    
}
</script>
