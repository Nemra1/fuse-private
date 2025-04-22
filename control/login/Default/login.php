<div id="login_wrap" class="back_login">
	<div id="header2" class="background_header">
		<div id="wrap_main_header">
			<div id="main_header" class="out_head headers">
				<div class="head_logo">
					<img id="main_logo" alt="logo" src="<?php echo getLogo('logo'); ?>"/>
				</div>
				<div class="bcell_mid login_main_menu">
				</div>
				<div onclick="getLanguage();" class="bclick bcell_mid_center" id="open_login_menu">
					<img alt="flag" class="intro_lang" src="<?php echo $data['domain']; ?>/system/language/<?php echo $cur_lang; ?>/flag.png"/>
				</div>
			</div>
		</div>
	</div>
	<div class="empty_subhead">
	</div>
	<div id="intro_top" class="btable">
		<div class="bcell_mid">
			<div id="login_all" class="pad30">
				<div class="login_text bpad15 centered_element">
						<p class="login_title_text bold text_jumbo bpad5">شات مملكة الحب | شات عربي تعارف</p>
						<p class="login_sub_text bold text_med">أهلا وسهلاً بكم في شات مملكة الحب أكبر موقع عربي كتابي وصوتي في الوطن العربي</p>
				</div>
				<div class="centered_element login_box">
					<?php if(bridgeMode(0)){ ?>
					<button onclick="getLogin();" class="intro_login_btn large_button_rounded  ok_btn"><i class="fa fa-users"></i> دخول الاعضاء</button>
					<?php } ?>
					<?php if(allowGuest()){ ?>
					<div class="clear"></div>
					<button onclick="getGuestLogin();" class="intro_guest_btn large_button_rounded default_btn"><i class="fa fa-paw"></i> <?php echo $lang['guest_login']; ?></button>
					<?php } ?>
					<div class="clear"></div>
					<button onclick="window.open('https://play.google.com/store/apps/details?id=com.dandnachat.inc', '_blank');" class="intro_guest_btn large_button_rounded theme_btn"><i class="fa fa-android"></i> حمل التطبيق</button>
				</div>
				<?php if(boomUseSocial()){ ?>
				<div class="intro_social_container">
					<div class="intro_social_content">
						<?php if(boomSocial('facebook_login')){ ?>
						<div class="intro_wrap_social">
							<div class="intro_social_btn">
								<div onclick="window.location.href='login/facebook_login.php'" class="bclick fbl_button social_in_btn">
									<i class="fa fa-facebook"></i>
								</div>
							</div>
						</div>
						<?php } ?>
						<?php if(boomSocial('google_login')){ ?>
						<div class="intro_wrap_social">
							<div class="intro_social_btn">
								<div onclick="window.location.href='login/google_login.php'" class="bclick gplus_button social_in_btn">
									<i class="fa fa-google-plus"></i>
								</div>
							</div>
						</div>
						<?php } ?>
						<?php if(boomSocial('twitter_login')){ ?>
						<div class="intro_wrap_social">
							<div class="intro_social_btn">
								<div onclick="window.location.href='login/twitter_login.php'" class="bclick twit_button social_in_btn">
									<i class="fa fa-twitter"></i>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
				<?php if(registration()){ ?>
				<div id="not_yet_member" class="login_not_member bclick">
					<p onclick="getRegistration();" class="inblock login_register_text pad10"><?php echo $lang['not_member']; ?></p>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<div style="padding:20px 0 0 0;" class="section back_xlite centered_element" id="intro_section_user">
		<div class="section back_xlite" id="intro_section_user">
				  <div class="left-arrow"></div>
				  <div class="right-arrow"></div>
<h1 class="titlez">مملكة الحب شات - موقع دردشة عربية كتابية وصوتية</h1>
<br>
<div class="maincontent1">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-question-circle-o" aria-hidden="true"> الأسئلة الشائعة: لماذا شات مملكة الحب</i></h3>
<p class="text_med bpad10 textlinez1">أنّ الفتاة محرم عليها التحدث مع شاب خارج نطاق الزواج كما أنّ الشاب محرم عليه أَيْضًا التحدث مع فتاة ومن الصعوبة أن يحصل تعارف بدون تواصل لذلك يعد شات عربي هو مصدر تعارف وتواصل وترابط وبناء علاقات وأصدقاء.</p>
<p class="text_med bpad10 textlinez1">الشات العربي في مجتمعنا هو أحد أبرز طرق التعبير عن الرأي بالكتابة بدون صوت حيث يلجئ شباب وصبايا الوطن العربي إلي الشات العربي الكتابي لممارسة حريات أوسع في التعبير عما في داخلهم بدون تحديد هوية الشخص ومكان تواجده وإجراء دردشة بدون أي مشاكل كإبراز وجه الفتاة أو الشباب مما يسمح بدردشة ممتعة ومسلية مع الشاب أو الفتاة.</p>
</div><br>
<div class="maincontent2">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-gift" aria-hidden="true"> ما الذي ستحصل عليه فعلاً؟</i></h3>
<p class="text_med bpad10 textlinez1">•	تغيير نوع الخط واللون و الحجم.</p>
<p class="text_med bpad10 textlinez1">• إرسال رسائل كتابية خاصّة و عامة غير محدودة.</p>
<p class="text_med bpad10 textlinez1">• إرسال صور من المعرض أو من كاميرا التصوير في المحادثات العامة الخاصّة.</p>
<p class="text_med bpad10 textlinez1">• إرسال رموز سمايلي في الغرف العامة والمحادثات الخاصّة.</p>
<p class="text_med bpad10 textlinez1">• تغيير أيقونة أو صورة المتحدث الشخصية في الدردشة.</p>
<p class="text_med bpad10 textlinez1">• يمكن تجاهل الرسائل الخاصّة و العامة من شخص معين.</p>
<p class="text_med bpad10 textlinez1">• منع استقبال رسائل خاصّة من الأشخاص.</p>
<p class="text_med bpad10 textlinez1">• البحث عن اسم ضيف أو مستخدم في قائمة المتواجدين في الغرفة.</p>
<p class="text_med bpad10 textlinez1">• تغيير لون الاسم في قائمة المستخدمين إلى ما يناسبك.</p>
<p class="text_med bpad10 textlinez1"> • تغيير لون خلفية الرسائل النصية المرسلة في الغرف والمحادثة الخاصّة..</p>
<p class="text_med bpad10 textlinez1">• عضوية أشراف تتضمن مراقبة الزوار من طرد و كتم عام و يحصل صاحب العضوية على لون مميز.</p>
</div><br>
<div class="maincontent3">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-ban" aria-hidden="true"> كيفية تجاهل الأشخاص المزعجين؟</i></h3>
<p class="text_med bpad10 textlinez1">يمكن تجاهل الرسائل الخاصة والعامة من شخص معين عن طريق فتح الملف الشخصي الخاص بالعضو المزعج والضغط علامة <i class="fa fa-ban"></i> "تجاهل" وهذا الامر يجعل الدردشه منتظمة.</p>
</div><br>
<div class="maincontent4">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-ban" aria-hidden="true"> ماذا عليّ أن أفعل لتجنب حظري من الشات؟</i></h3>
<p class="text_med bpad10 textlinez1">يجب عليك تجنب الدخول بأسماء غير لائقة.</p>
<p class="text_med bpad10 textlinez1">احترام قوانين "شات عربي" والأشخاص داخل الدردشة.</p>
<p class="text_med bpad10 textlinez1">عدم الإساءة لأحد الأشخاص أو لأي مذهب ديني.</p>
</div>
<br>
<div class="maincontent5">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-user-circle" aria-hidden="true"> الملف الشخصي</i></h3>
<p class="text_med bpad10 textlinez1">يمكنك تعديل الملف الشخصي الخاص بك من أيقونة <i class="fa fa-user-circle"></i> تعديل الجنس العمر الحالة واللغة وكلمة المرور وتفاصيل الحساب وغير ذلك الكثير.</p>
</div>
<br>
<div class="maincontent6">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-handshake-o" aria-hidden="true"> قبول / اضافة أصدقاء</i></h3><p class="text_med bpad10 textlinez1"> في القائمة العلوية من أيقونة <i class="fa fa-user-plus"></i> يمكنك عرض طلبات أصدقائك المعلقة الحالية.يمكنك إضافة أصدقاء من الملف الشخصي للأعضاء عن طريق النقر على زر "إضافة صديق" في أعلى الملف الشخصي. يمكنك عرض قائمة الأصدقاء الحاليين عن طريق النقر <i class="fa fa-user-plus"></i> على الرمز في أسفل الصفحة.</p>
</div>
<br>
<div class="maincontent7">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-comments-o" aria-hidden="true"> الرسائل الخاصّة</i></h3>
<p class="text_med bpad10 textlinez1">يمكنك بدء محادثة خاصّة أو محادثة جماعية مع الأشخاص.</p>
<p class="text_med bpad10 textlinez1">في القائمة العلوية من أيقونة <i class="fa fa-envelope"></i> يمكنك عرض القائمة الخاصة النشطة الحالية وإشعار خاص جديد غير مقروء.يمكنك فتح محادثة خاصة مع مستخدم عن طريق النقر على الصورة الرمزية للعضو المرغوب في الدردشة أو بالنقر على اسم المستخدم الخاص به في قائمة المستخدمين.</p>
</div>
<br>
<div class="maincontent8">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-bell-o" aria-hidden="true"> إشعارات الدردشة</i></h3>
<p class="text_med bpad10 textlinez1"> في القائمة العلوية من أيقونة <i class="fa fa-bell-o"></i> يمكنك عرض الإشعار الحالي حول ما يحدث على حسابك. يوجد إخطار غير مقروء في الأعلى ولونه مختلف.</p>
</div>
<br>
<div class="maincontent9">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-youtube-play" aria-hidden="true"> مشاركة الفيديوهات</i></h3>
<p class="text_med bpad10 textlinez1">بإمكانك مشاركة ونشر فيديوهات من اليوتيوب مع أصدقائك والجميع.</p>
</div>
<br>
<div class="maincontent10">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-music" aria-hidden="true"> الصوتيات</i></h3>
<p class="text_med bpad10 textlinez1">بإمكانك الاستماع لجميع محطات الراديو والصوتيات.</p>
</div><br>
<div class="maincontent11">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-film" aria-hidden="true"> مشاركة الصور</i></h3>
<p class="text_med bpad10 textlinez1">بإمكانك مشاركة صور من الإنترنت أو من جهازك الشخصي مع جميع الأشخاص.</p>
</div>
<br>
<div class="maincontent12">
<h3 class="text_large bpad10 headlinez1"><i class="fa fa-rss" aria-hidden="true"> حائط اليوميات</i></h3>
<p class="text_med bpad10 textlinez1">تستطيع نشر يوميات على حائطك الخاص بك ومشاركتها مع أصدقائك.</p>
</div>
<br>
<h2 class="titlez">دردشة عربية إلتقي بأصدقاء عرب جدد من مختلف أنحاء العالم واستمتع بالدردشة الجماعية أو بدء محادثة خاصّة في موقع شات عربي تعارف بدون تسجيل أو اشتراك مجانًا</h2>
<div style="text-align: center; padding-top: 10px;">
</div>
		<div class="clear"></div>
	</div>
	<div class="section" id="intro_section_bottom">
	</div>
	<div style="
    background: #03add8;
    color: white;
" class="section intro_footer" id="main_footer">
		<div class="section_content">
			<div class="section_inside">
				<?php boomFooterMenu(); ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</div>
<?php if(boomCookieLaw()){ ?>
<div class="cookie_wrap back_med">
	<p class="bpad10"><?php echo str_replace('%data%', '<span onclick="openSamePage(\'privacy.php\');" class="bold bclick link_like">' . $lang['privacy'] . '</span>', $lang['cookie_law']); ?></p>
	<button onclick="hideCookieBar();" class="ok_btn reg_button"><i class="fa fa-check"></i> <?php echo $lang['ok']; ?></button>
</div>
<?php } ?>
<script data-cfasync="false" src="js/login.js<?php echo $bbfv; ?>"></script>