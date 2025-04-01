<?php


require __DIR__ . "../../../config_session.php";
if (!boomAllow(100)) {
    exit;
}
?>
<div class="page_indata">
	<div id="page_wrapper">
		<div class="page_full">
				<?php  echo elementTitle('Wallet Settings'); ?>
		</div>
		<div class="page_full">
			<div>
				<div class="tab_menu">
					<ul>
						<li class="tab_menu_item tab_selected" data="main_tab" data-z="main_zone">Main</li>
						<li class="tab_menu_item" data="main_tab" data-z="maint_zone">Payment Gateway</li>
					</ul>
				</div>
			</div>
			<div id="main_tab">
				<div id="main_zone" class="tab_zone" style="display: block;">
					<div class="page_element">
						<div class="boom_form">
                        	<div class="setting_element">
								<p class="label">Enable Wallet Service</p>
								<select id="use_wallet">
								<?php echo yesNo($data["use_wallet"]); ?>
								</select>

							</div>						    
							<div class="setting_element">
								<p class="label">Exchange Rate</p>
								<input id="dollar_to_point_cost" class="full_input" value="<?php echo ($data['point_cost']); ?>" type="number" />
							</div>
							<div class="setting_element">
								<p class="label">Currency</p>
							<select id="currency" name="currency">
                             <?php foreach (common_currency() as $key => $value): ?>
                                    <option data-symbol="<?php echo htmlspecialchars($value['symbol']); ?>" value="<?php echo htmlspecialchars($value['code']); ?>" <?php if ($data['currency'] == $value['code']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($value['code']); ?> : <?php echo htmlspecialchars($value['name']); ?>
                                    </option>
                                <?php endforeach; ?>
								</select>

								
							</div>						
						
						
						</div>
					<button data="gateway_mods" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> Save</button>
					</div>
				</div>
				<div id="maint_zone" class="tab_zone hide_zone" style="display: none;">
					<div class="page_element">
						<div class="boom_form">
							<div class="setting_element">
								<p class="label">Enable Paypal</p>
								<select id="allow_paypal" >
									<?php echo yesNo($data["allow_paypal"]); ?>
								</select>

							</div>
                        <div class="setting_element">
								<p class="label">Paypal Mode</p>
								<select id="paypal_mode">
									<option value="sandbox" <?php if ($data['paypal_mode'] == 'sandbox') echo 'selected'; ?>>sandbox</option>
									<option value="live" <?php if ($data['paypal_mode'] == 'live') echo 'selected'; ?>>live</option>
								</select>
							</div>	
                        <div class="setting_element">
					    	<p class="label">paypal Testing Client key</p>
					    	<input id="paypalTestingClientKey" class="full_input" value="<?php echo $data["paypalTestingClientKey"]; ?>" type="text" />
					    </div>
                        <div class="setting_element">
					    	<p class="label">paypal Testing Secret key</p>
					    	<input id="paypalTestingSecretKey" class="full_input" value="<?php echo $data["paypalTestingSecretKey"]; ?>" type="text" />
					    </div>	
                        <div class="setting_element">
					    	<p class="label">Paypal Live Client key</p>
					    	<input id="paypalLiveClientKey" class="full_input" value="<?php echo $data["paypalLiveClientKey"]; ?>" type="text" />
					    </div>
                        <div class="setting_element">
					    	<p class="label">Enter your Live Secret Key</p>
					    	<input id="paypalLiveSecretKey" class="full_input" value="<?php echo $data["paypalLiveSecretKey"]; ?>" type="text" />
					    </div>					    
						</div>
						<input id="gateway_mods" class="full_input" value="gateway" type="hidden" />
						<button data="gateway_mods" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i> Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>

</script>