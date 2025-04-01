<?php
require __DIR__ . "../../../config_admin.php";

if(!boomAllow(100)){
	die();
}
?>
<?php echo elementTitle('Rewards Settings'); ?>
<div class="page_full">
	<div class="page_element">
		<div class="form_content">
		
                <div class="setting_element ">
                    <p class="label">Which can send coins</p>
                    <select id="set_allow_sendcoins">
                        <?php echo listRank($data['allow_sendcoins'], 1); ?>
                    </select>
                </div>
                <div class="setting_element ">
                    <p class="label">That can deduct coins</p>
                    <select id="set_allow_takecoins">
                        <?php echo listRank($data['allow_takecoins'], 1); ?>
                    </select>
                </div>
                <div class="page_element">
                    <div class="page_top_elem">
                        <div class="bold page_top_title" style="font-size: 15px;">
                           <i class="ri-btc-line"></i>  Send a free gold credit code
                        </div>
                    </div>
                    <div class="boom_form">
                        <div class="setting_element ">
                            <p class="label">Write Gift code</p>
							<small class="bcolor3"><i class="ri-information-line"></i> No special characters or uppercase letters</small>
                            <input id="set_coins_gift" class="full_input" type="text" placeholder="Say Something Nice..." />
                        </div>
                        <div class="setting_element ">
                            <p class="label">How Much Gold in this Code</p>
                            <input id="set_coins_code" class="full_input" type="number" placeholder="number of coins in the gift..." />
                        </div>
                    </div>
                    <button type="button" style="background:#d803d1;" onclick="sendCoinsGift();" class="reg_button theme_btn"><i class="ri-share-line"></i> Send the gift</button>
                </div>
		</div>
		<div class="form_control">
		    <button data="gold_reward" type="button" class="save_admin reg_button theme_btn"><i class="ri-save-line"></i>  <?php echo $lang['save']; ?></button>
		</div>
	</div>
</div>
<script data-cfasync="false">
sendCoinsGift = function() {
    var coin_gift = $('#set_coins_gift').val().trim();  // Get and trim the input value
    var coins_code = $('#set_coins_code').val();
    // Regular expression to remove symbols and keep only Arabic, English letters, numbers, and spaces
    var cleaned_coin_gift = coin_gift.replace(/[^\u0600-\u06FFa-zA-Z0-9\s]/g, ' ').replace(/\s+/g, ' ').trim();
    // Check if the cleaned value differs from the original, meaning it contained invalid symbols
    if (coin_gift !== cleaned_coin_gift) {
        // Display error message
        callSaved("Error: The coin gift input contains invalid characters. Please remove any symbols.", 3);
        console.log("Validation failed: input contains symbols.");
        return false;  // Stop the request
    }
    // If input is valid, proceed with the AJAX request
    $.post(FU_Ajax_Requests_File(), {
        f: 'store',
        s: 'send_reward',
        set_coins_gift: cleaned_coin_gift,  // Use cleaned value
        set_coins_code: coins_code,
        token: utk,
    }, function(r) {
        if (r.status == 200) {
            callSaved(r['message'], 1);
        } else {
            callSaved(r['message'], 3);
        }
    });
};

</script>