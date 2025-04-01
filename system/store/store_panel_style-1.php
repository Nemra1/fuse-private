<?php
$store = array();
$store['gold_pack'] = FU_store_market("gold");
?>
<style>
.slider { --slider-padding: 1rem; --slider-column-gap: 1rem; --slide-width: 50%; --slide-min-width: 15rem; position: relative; overflow: hidden; }
.slider__track { display: flex; overflow-x: auto; -ms-scroll-snap-type: x mandatory; scroll-snap-type: x mandatory; padding-inline: var(--slider-padding); scroll-behavior: smooth; list-style-type: none; padding: 0; margin-right: calc(var(--slider-column-gap) * -1); scrollbar-width: none; }
.slider__track > * { flex: 0 0 var(--slide-width); min-width: var(--slide-min-width); scroll-snap-align: start; scroll-snap-stop: always; padding-right: var(--slider-column-gap); }
.slider__track::-webkit-scrollbar { display: none; }
.slider__buttons { margin-top: 1rem; position: relative; z-index: 1; text-align: center; }
.slider__buttons [disabled] { opacity: 0.5; border: 1px solid #9E9E9E; }
.slide { display: flex; align-items: center; justify-content: center; width: 100%; aspect-ratio: 1 / 1; border-radius: 30px; background: #6c5ce7; color: #a29bfe; font-weight: 700; }
.store_container { max-width: 1200px; margin: 0 auto; }
.hero { display: inline-block; position: relative; width: 270px; min-width: 270px; height: 270px; border-radius: 30px; overflow: hidden; box-shadow: 5px 5px 30px rgba(0, 0, 0, 0.3); margin: 5px; }
.image { height: 100%; width: 100%; }
.text { background-image: linear-gradient(0deg , #3f5efb, #fc466b); border-radius: 30px; position: absolute; top: 64%; left: -5px; height: 65%; width: 108%; transform: skew(19deg, -9deg); }
.second .text { background-image: linear-gradient(-20deg , #bb7413, #e7d25c) }
.store_logo { height: 80px; width: 80px; border-radius: 20px; background-color: #fff; position: absolute; bottom: 17%; left: 10px; overflow:hidden; box-shadow: 5px 5px 30px rgba(0, 0, 0, 0.7); }
.store_logo img { height: 100%; }
.main-text { position: absolute; color: #fff; font-weight: 900; left: 130px; bottom: 26%; }
.hero-btn { position: absolute; color: #fff; right: 30px; bottom: 6%; padding: 10px 20px; border: 1px solid #fff; border-radius: 20px; }
.hero-btn:hover{ animation: none; }
.pack_amount { display: flex; justify-content: center; position: absolute; right: 0; left: 0; background: #1313149e; color: orange; font-size: x-large; }
.s_btn {color: inherit; padding: 7px 7px; border: 1px solid #FFC107; border-radius: 20px; font-size: medium; }
.pack-detail { display: flex; flex-direction: column; gap: 10px; max-width: 600px; margin: auto; padding-top: 10px; }
.pack-detail-item { display: flex; align-items: center; background-color: #fff; border-radius: 8px; padding: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease; background-image: linear-gradient(218deg, #e5497c, #f5f5f54f); }
.pack-detail-item img { width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; transition: transform 0.3s ease; }
.pack-detail-info { display: flex; flex-direction: column; }
.pack-detail-info .pack-name { font-size: 18px; font-weight: bold; color: #333; }
.pack-detail-info .p_amount { font-size: 14px; color: #666; margin-top: 2px; }
.pack-label { display: block; cursor: pointer; position: relative; }
.pack-label .slide { pointer-events: none; }
input[type="radio"]:checked + label .slide { border: 2px solid #FF5722; }
input[type="radio"] { position: absolute; opacity: 0; z-index: -1; }
@media screen and (max-width:640px){
.hero { width: 200px; min-width: 200px; height: 200px; }
.store_logo { height: 50px; width: 50px; border-radius: 50%; bottom: 27%; left: 5px; }
.main-text { left: 65px; bottom: 34%; }
}
</style>
<div class="store_container pad10">
    <div id="store-box-pro-form-alert"></div> 
  <div class="slider" data-slider>
    <ul class="slider__track" data-slider-track>
	<?php
foreach ($store['gold_pack'] as $key) { ?>    
      <li>
        <input type="radio" data-id="<?php echo $key['id']?>" name="pack_selection" id="pack_<?php echo $key['id']?>" value="<?php echo $key['p_amounts']?>" style="display: none;">
         <label for="pack_<?php echo $key['id']?>" class="pack-label" data-pack-name="<?php echo $key['pack_name']?>">
        <div class="slide">
                  <div class="first hero">
                      <div class="pack_amount"><i class="ri-copper-diamond-fill"></i><?php echo $key['p_amounts']?></div>
                    <img src="<?php echo $key['image']?>" alt="" class="image">
                    <div class="text"></div>
                    <div class="store_logo">
                      <img src="<?php echo $key['image']?>" alt="">
                    </div>
                    <div class="main-text">
                      <p><?php echo $key['pack_name']?></p>
                    </div>
                    <div class="hero-btn">
                      <button class="btn"> Price <i class="ri-money-dollar-circle-line"></i><?php echo $key['price']?></button>
                    </div>
                  </div>            
            
            </div>
        </label>
      </li>
<?php } ?>     
    </ul>
    <div class="slider__buttons">
      <button class="slider__button s_btn" data-slider-prev disabled>
       <i class="ri-arrow-left-circle-line"></i> Previous
      </button>
      <button class="slider__button s_btn" data-slider-next>
       <i class="ri-arrow-right-circle-line"></i> Next
      </button>
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
    <button id="store_buy_button" onclick="buy_pack();" type="button" class="b-main-color pointer" style=" display: none; ">buy</button>
    <button type="button" class="delete_btn pointer close_over">Cancel</button>
 </div>  
</div>
<script>
$(document).ready(function(){
$('input[name="pack_selection"]').change(function(){
	if($(this).is(':checked')) {
		var selectedLabel = $('label[for="' + $(this).attr('id') + '"]');
		var amount = $(this).val();
		var packName = selectedLabel.data('pack-name');
		var imageSrc = selectedLabel.find('.image').attr('src');
		var buy_pack_btn = $('#store_buy_button');
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
 const slider = document.querySelector("[data-slider]");
const track = slider.querySelector("[data-slider-track]");
const prev = slider.querySelector("[data-slider-prev]");
const next = slider.querySelector("[data-slider-next]");
if (track) {
  prev.addEventListener("click", () => {
    next.removeAttribute("disabled");

    track.scrollTo({
      left: track.scrollLeft - track.firstElementChild.offsetWidth,
      behavior: "smooth"
    });
  });

  next.addEventListener("click", () => {
    prev.removeAttribute("disabled");

    track.scrollTo({
      left: track.scrollLeft + track.firstElementChild.offsetWidth,
      behavior: "smooth"
    });
  });

  track.addEventListener("scroll", () => {
    const trackScrollWidth = track.scrollWidth;
    const trackOuterWidth = track.clientWidth;

    prev.removeAttribute("disabled");
    next.removeAttribute("disabled");

    if (track.scrollLeft <= 0) {
      prev.setAttribute("disabled", "");
    }

    if (track.scrollLeft === trackScrollWidth - trackOuterWidth) {
      next.setAttribute("disabled", "");
    }
  });
}       
});
function buy_pack() {
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
                if (data.status === 200) {
                    alert_msg = $("<div>", {
                        class: "alert alert-success",
                        text: data['message'].alert
                    }).prepend('<i class="ri-checkbox-circle-fill"></i>');
                    $("#store-box-pro-form-alert").html(alert_msg);
                    callSaved(data['message']['alert'], 1);
                    $('.gold_counter').html(data['message'].user_gold);
                    okayPlay();
                } else {
                    alert_msg = $("<div>", {
                        class: "alert alert-danger",
                        text: data['message'].alert
                    }).prepend('<i class="ri-xrp-line"></i>');
                    $("#store-box-pro-form-alert").html(alert_msg);
                    callSaved(data['message']['alert'], 3);
                }
            },
            error: function(xhr, status, error) {
                console.log('An error occurred');
            }
        });
    }

    return false;
}

    
</script>