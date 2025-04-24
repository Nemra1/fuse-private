<div class="external_wrap">
	<div id="login_panel" class="login_panel fleft back_panel rborder">
		<div class="login_panel_inner">
			<div class="reg_menu_container bpad10 bborder">
				<div class="reg_menu">
					<ul>
						<li class="reg_menu_item rselected" data="lpanel_menu" data-z="active_rooms"><?php echo $lang['active_room']; ?></li>
					</ul>
				</div>
			</div>
			<div id="lpanel_menu">
				<div id="active_rooms" class="reg_zone vpad5">
				<?php echo guest_room_list('list'); ?>
				</div>
			</div>
		</div>
		<div id="login_panel_close" class="lpanel_close">
			<div class="lpanel_close_btn btable_height brad100 bclick">
				<div class="bcell_mid_center">
					<i class="ri-close-circle-line lpanel_cicon"></i>
				</div>
			</div>
		</div>
	</div>
	<div id="login_body" class="login_body">
		<div id="intro_top" class="btable">
			<div class="bcell_mid bwidth100">
				<div class="login_body_inner">
					<div class="login_box pad30">
						<div class="centered_element bborder">
							<img id="login_logo" src="<?php echo getLogo(); ?>" />
						</div>
						<div class="login_text vpad20 centered_element">
							<p class="bold text_xlarge bpad10"><?php echo $lang['left_title']; ?></p>
							<p class="text_med"><?php echo $lang['left_welcome']; ?></p>
						</div>
						<div class="centered_element bpad20 bborder">
						<?php if(bridgeMode(0)){ ?>
							<button onclick="getLogin();" class="intro_login_btn mod_button rounded_button theme_btn btnshadow"><i class="ri-send-plane-line"></i><?php echo $lang['login']; ?> </button>
						<?php } ?>
						<?php if(bridgeMode(1)){ ?>
		<button class="intro_login_btn mod_button rounded_button theme_btn btnshadow" onclick="bridgeLogin('<?php echo getChatPath(); ?>');"><?php echo $lang['enter_now']; ?><i class="ri-send-plane-2-line"></i></button>
						<?php } ?>
						<?php if(allowGuest()){ ?>
		<button onclick="getGuestLogin();" class="intro_login_btn mod_button rounded_button theme_btn btnshadow"><i class="ri-send-plane-2-line"></i> <?php echo $lang['guest_login']; ?> </button>
						<?php } ?>	
						</div>
					<?php if(boomUseSocial() && !embedMode()){ ?>
					<div class="intro_social_container">
						<div class="intro_social_content">
							<?php if(boomSocial('facebook_login')){ ?>
							<img onclick="window.location.href='login/facebook_login.php'" class="intro_social_btn bclick" src="<?php echo $data['domain']; ?>/default_images/social/facebook.svg"/>
							<?php } ?>
							<?php if(boomSocial('google_login')){ ?>
							<img onclick="window.location.href='login/google_login.php'" class="intro_social_btn bclick" src="<?php echo $data['domain']; ?>/default_images/social/google.svg"/>
							<?php } ?>
							<?php if(boomSocial('twitter_login')){ ?>
							<img onclick="window.location.href='login/twitter_login.php'" class="intro_social_btn bclick" src="<?php echo $data['domain']; ?>/default_images/social/twitter.svg"/>
							<?php } ?>
						</div>
					</div>
					<?php } ?>						
					</div>
					<?php if(registration()){ ?>
					<div class="centered_element" onclick="getRegistration();">
						<div class="">
							<p class="text_xsmall"><?php echo $lang['not_member']; ?></p>
							<p  class="text_med bold bclick tpad5"><?php echo $lang['register']; ?></p>
						</div>
					</div>
					<?php } ?>	
				</div>
			</div>
			<div onclick="getLanguage();" class="bclick btable" id="intro_lang">
				<div class="bcell_mid centered_element">
					<img alt="flag" class="intro_lang" src="<?php echo $data['domain']; ?>/system/language/<?php echo $cur_lang; ?>/flag.png"/>
				</div>
			</div>
			<div id="login_panel_toggle" class="lpanel_toggle">
				<div class="lpanel_toggle_btn btable_height brad100 bclick theme_btn btnshadow">
					<div class="bcell_mid_center">
						<i class="ri-menu-unfold-line lpanel_ticon"></i>
					</div>
				</div>
			</div>
		</div>
		<div class="section">
			<div class="section_content">
				<!-- add your content here if you need to add more for seo -->
			</div>
		</div>
		<div class="foot vpad25 hpad15 centered_element" id="main_footer">
			<div id="menu_main_footer">
				<?php boomFooterMenu(); ?>
			</div>
		</div>
	</div>
</div>

<script data-cfasync="false" src="js/function_login.js<?php echo $bbfv; ?>"></script>
<script data-cfasync="false" src="js/function_active.js<?php echo $bbfv; ?>"></script>
<script data-cfasync="false">
	var pvisible = 1; // 0 - Hide panel 1 - Show panel.
	showLoginPanel = function(){
		$('#login_panel').show();
		$('#login_body').addClass('login_pbody');
	}
	hideLoginPanel = function(){
		$('#login_panel').hide();
		$('#login_body').removeClass('login_pbody');
	}
	checkLoginPanel = function(){
		var wwidth = $(window).width();
		if(wwidth <= 930){
			hideLoginPanel();
		}
	}
	visibleLoginPanel = function(){
		var wwidth = $(window).width();
		if(pvisible == 1 && wwidth > 930){
			showLoginPanel();
		}
	}
	toggleLoginPanel = function(){
		if(!$('#login_panel:visible').length){
			showLoginPanel();
		}
		else{
			hideLoginPanel();
		}
	}
	$(document).ready(function(){
		checkLoginPanel();
		visibleLoginPanel();
		$(document).on('click', '#login_panel_toggle, #login_panel_close', toggleLoginPanel);
		$(window).on('resize', checkLoginPanel);
	});
</script>