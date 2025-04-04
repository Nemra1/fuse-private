<style>
.d-flex{display:-ms-flexbox;display:flex}.room-tabs-container{position:relative;overflow:hidden;border-radius:0 0 3px 10px}.room-tabs{position:relative;width:100%;height:35px;margin-bottom:-1px}.title-bar{display:flex;list-style:none;transition:transform .3s ease}
.title-bar > li { position: relative; flex: 0 0 auto; margin-right: 3px; }
.title-bar .nav-link { display: block; max-width: 190px; height: 36px; padding: 3px 2px; font-weight: 600; text-decoration: none; border-top-left-radius: 6px; border-top-right-radius: 6px; background-color: #333; color: #fff; border: 2px dotted #8b00d1; transition: background-color .3s; position: relative; padding-right: 20px; }
.title-bar > li > div .title-room{display:inline-block;width:100%;max-width:130px;padding-left:18px;background:url(default_images/private-chat-icon.png) no-repeat left center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;padding-right:18px;position:relative;top:4px}.title-bar .close{float:right;font-size:14px;font-weight:700;margin-top:-5px;cursor:pointer;color:#fff;text-shadow:0 1px 0 #fff;position:absolute;z-index:5;right:0}
.title-bar > li:only-child .close{display:none}
#slidePrevRoomTab,#slideNextRoomTab{cursor:pointer;background:none;border:none;outline:none;color:#fff;font-size:20px;position:absolute;top:50%;transform:translateY(-50%);z-index:10}#slidePrevRoomTab{left:0;background:#8b00d1e8;height:35px}#slideNextRoomTab{right:0;background:#fb832e;height:35px}.slide-room-tabs{display:flex;justify-content:space-between;align-items:center}.chat_topping{position:absolute;z-index:11;width:100%;margin:0 auto;left:0;right:0}.notification-counter{background:red;color:#fff;position:absolute;top:17px;right:1px;padding:2px 3px;border-radius:9px;font-size:11px;font-weight:700}
.tab_overflow { overflow: hidden; width: 100%; position: relative; display: flex; overflow-x: auto; white-space: nowrap; left: 30px;}
@media (max-width: 768px){
.title-bar .nav-link { max-width: 165px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-right: 2px;height: 30px;font-size: smaller;}
	.room-tabs { width: 490px; height: 30px;}
	.title-bar .close {font-size: 18px;}
}

</style>

<div id="chat_toping" class="chat_topping">
	<div class="room-tabs-container background_box">
		<div class="d-flex slide-room-tabs">
			<button id="slidePrevRoomTab" class="prev"><i class="ri-arrow-left-circle-line"></i></button>
			<button id="slideNextRoomTab" class="next"><i class="ri-arrow-right-circle-line"></i></button>
		</div>
		<div class="room-tabs" id="slider_content">
			<div id="empty_top_mob" class="bcell_mid hpad10"></div>
			<ul id="roomsTab" class="nav d-flex title-bar tab_overflow" role="tablist">
				<li class="nav-item slide switch_room" data-roomid="<?php echo $data['user_roomid'];?>" id="slide_roomid_<?php echo $data['user_roomid'];?>">
					<div class="nav-link active" href="#room_<?php echo $data['user_roomid'];?>">
						<span class="text-hidden title-room" onclick="switchRoom(<?php echo $data['user_roomid'];?>, 0, 0);"><?php echo $room['room_name'];?></span>
						<span class="close" onclick="exitRoom(<?php echo $data['user_roomid'];?>)"><i class="ri-close-circle-line"></i></span>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	let position = 0;
		$(function() {
		let isDragging = false;
		let startX;
		let scrollLeft;
		// Starting position
		const itemWidth = $('.nav-item').outerWidth(true); // Get item width including margin
		const totalItems = $('.nav-item').length; // Total number of items
		const maxPosition = (totalItems * itemWidth) - $('.room-tabs-container').width(); // Maximum scrollable position
		// Next button functionality
		$('#slideNextRoomTab').on('click', function() {
			if (position < maxPosition) {
				position += itemWidth;
				$('.tab_overflow').css('transform', `translateX(-${position}px)`);
			}
		});
		// Previous button functionality
		$('#slidePrevRoomTab').on('click', function() {
			if (position > 0) {
				position -= itemWidth;
				$('.tab_overflow').css('transform', `translateX(-${position}px)`);
			}
		});

	});	
});
</script>
