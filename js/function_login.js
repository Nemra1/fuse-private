var waitReply = 0;

$(document).ready(function(){

	selectIt();
	bcCookie();
	
	$(document).keypress(function(e) {
		if(e.which == 13) {
			if($('#login_form_box:visible').length){
				sendLogin();
			}
			else if($('#registration_form_box:visible').length){
				sendRegistration();
			}
			else if($('#guest_form_box:visible').length){
				sendGuestLogin();
			}
			else {
				return false;
			}
		}
	});

});

bcCookie = function(){
	var checkCookie = navigator.cookieEnabled;
	if(checkCookie == false){
		alert("you need to enable cookie for the site to be able to log in");
	}
}
getLogin = function(){
	$.post('system/box/login.php', {
		}, function(response) {
			if(response != 0){
				showModal(response);
			}
			else {
				return false;
			}
	});
}
getGuestLogin = function(){
	$.post('system/box/guest_login.php', {
		}, function(response) {
			if(response != 0){
				showModal(response);
				renderRecaptcha();
			}
			else {
				return false;
			}
	});
}
getRegistration = function(){
	$.post('system/box/registration.php', {
		}, function(response) {
			if(response != 0){
				showModal(response);
				renderRecaptcha();
			}
			else {
				return false;
			}
	});
}
moreLogin = function(){
	$.post('system/box/more_login.php', {
		}, function(response) {
			if(response != 0){
				showModal(response, 300);
			}
			else {
				return false;
			}
	});
}
getRecovery = function(){
	$.post('system/box/pass_recovery.php', {
		}, function(response) {
			if(response != 0){
				showModal(response);
			}
			else {
				return false;
			}
	});
}
hideArrow = function(d){
	if($("#last_active .last_10 .active_user").length <= d){
		$("#last_active .left-arrow, #last_active .right-arrow").hide();
	}
	else {
		$("#last_active .left-arrow, #last_active .right-arrow").show();	
	}
}
sendLogin = function(){
	var upass = $('#user_password').val();
	var uuser = $('#user_username').val();
	if(upass == '' || uuser == ''){
		callSaved(system.emptyField, 3);
		return false;
	}
	else if (/^\s+$/.test($('#user_password').val())){
		callSaved(system.emptyField, 3);
		$('#user_password').val("");
		return false;
	}
	else if (/^\s+$/.test($('#user_username').val())){
		callSaved(system.emptyField, 3);
		$('#user_username').val("");
		return false;
	}
	else {
		if(waitReply == 0){
			waitReply = 1;
			$.post(FU_Ajax_Requests_File(), {
				f:"system_login",
				s:"member_login",
				password: upass, 
				username: uuser
				}, function(res) {
					if(res.code == 1){
						callSaved(system.badLogin, 3);
						$('#user_password').val("");
					}
					else if (res.code == 2){
						callSaved(system.badLogin, 3);
						$('#user_password').val("");
					}
					else if (res.code == 3){
						callSaved(res.msg, 1);
						// Secure reload with delay
						setTimeout(function() {
							location.reload(true); // true forces reload from server
						}, res.reload_delay || 2000); // Default 3s if not specified

					}
					waitReply = 0;
			});
		}
		else {
			return false;
		}
	}
}
sendRegistration = function() {
    var upass = $('#reg_password').val().trim(); // Trim inputs to remove spaces
    var uuser = $('#reg_username').val().trim();
    var uemail = $('#reg_email').val().trim();
    var ugender = $('#login_select_gender').val();
    var uage = $('#login_select_age').val();
    var regRecapt = getRecapt();

    // Validate empty fields
    if(upass === '' || uuser === '' || uemail === ''){
        callSaved(system.emptyField, 3);
        return false;
    }

    // Validate username, password, and email not being only whitespace
    if (/^\s+$/.test(uuser)){
        callSaved(system.emptyField, 3);
        $('#reg_username').val(""); // Clear input
        return false;
    }
    if (/^\s+$/.test(upass)){
        callSaved(system.emptyField, 3);
        $('#reg_password').val(""); // Clear input
        return false;
    }
    if (/^\s+$/.test(uemail)){
        callSaved(system.emptyField, 3);
        $('#reg_email').val(""); // Clear input
        return false;
    }

    // Validate recaptcha if required
    if(recapt > 0 && regRecapt === ''){
        callSaved(system.missingRecaptcha, 3);
        return false;
    }

    // Process registration if all checks pass
    if(waitReply === 0){
        waitReply = 1;
        $.post(FU_Ajax_Requests_File(), {
			f:"system_login",
			s:"system_register",			
            password: upass,
            username: uuser,
            email: uemail,
            age: uage,
            gender: ugender,
            recaptcha: regRecapt
        }, function(res) {
            if(res.code != 1){
                resetRecaptcha();
            }

           switch(String(res.code)) { 
                case '2':
                case '3':
                    callSaved(res.msg, 3);
                    $('#reg_password').val('');
                    $('#reg_username').val('');
                    $('#reg_email').val('');
                    break;
                case '4':
                    callSaved(system.invalidUsername, 3);
                    $('#reg_username').val('');
                    break;
                case '5':
                    callSaved(system.usernameExist, 3);
                    $('#reg_username').val('');
                    break;
                case '6':
                    callSaved(system.invalidEmail, 3);
                    $('#reg_email').val('');
                    break;
                case '7':
                    callSaved(system.missingRecaptcha, 3);
                    break;
                case '10':
                    callSaved(system.emailExist, 3);
                    $('#reg_email').val('');
                    break;
                case '13':
                    callSaved(system.selAge, 3);
                    break;
                case '14':
                    callSaved(system.error, 3);
                    break;
                case '16':
                    callSaved(system.maxReg, 3);
                    break;
                case '17':
                    callSaved(system.shortPass, 3);
                    $('#reg_password').val('');
                    break;
                case '1':
				setTimeout(function() {
					location.reload();
				}, 2000); // Small delay to ensure everything updates
				 callSaved(res.msg, 1);
                    break;
                case '0':
                    callSaved(system.registerClose, 3);
                    break;
                default:
                    callSaved(res.msg, 1);
                    break;
            }

            waitReply = 0; // Reset waitReply flag
        });
    }
    return false; // Prevent form submission
}

sendGuestLogin = function(){
    var gname = $('#guest_username').val().trim(); // Trim leading/trailing spaces
    var ggender = $('#guest_gender').val();
    var gage = $('#guest_age').val();
    var guestRecapt = getRecapt();

    // Check if the name is empty
    if(gname === ''){
        callSaved(system.emptyField, 3);
        return false;
    }

    // Check if the username contains only spaces
    if (/^\s+$/.test(gname)){
        callSaved(system.emptyField, 3);
        $('#guest_username').val(""); // Clear input
        return false;
    }

    // Check if recaptcha is required and missing
    if (recapt > 0 && guestRecapt === ''){
        callSaved(system.missingRecaptcha, 3);
        return false;
    }

    // If all checks pass, proceed to send the AJAX request
    if(waitReply === 0){
        waitReply = 1;
        $.post(FU_Ajax_Requests_File(), {
			f:"system_login",
			s:"guest_login",
            guest_name: gname,
            guest_gender: ggender,
            guest_age: gage,
            recaptcha: guestRecapt
        }, function(response) {
            // Reset recaptcha if necessary
            if(response.code != 1){
                resetRecaptcha();
            }

            // Handle different responses
            switch(response.code) {
                case 4:
                    callSaved(system.invalidUsername, 3);
                    $('#guest_username').val("");
                    break;
                case 5:
                    callSaved(system.usernameExist, 3);
                    $('#guest_username').val("");
                    break;
                case 6:
                    callSaved(system.missingRecaptcha, 3);
                    break;
                case 16:
                    callSaved(system.maxReg, 3);
                    break;
                case 13:
                    callSaved(system.selAge, 3);
                    break;
                case 14:
                    callSaved(system.error, 3);
                    break;
                case 1:
                    location.reload(); // Successful login
                    break;
                default:
                    callSaved(system.error, 3);
            }

            waitReply = 0; // Reset waitReply flag
        });
    }
    return false; // Ensure no form submission
}

sendRecovery = function() {
    var rEmail = $('#recovery_email').val().trim(); // Trim input to remove spaces

    // Validate email field is not empty or just whitespace
    if (rEmail === '') {
        callSaved(system.emptyField, 3);
        return false;
    }

    // Check if recovery email is only whitespace
    if (/^\s+$/.test(rEmail)) {
        callSaved(system.emptyField, 3);
        $('#recovery_email').val(""); // Clear input
        return false;
    }

    // Proceed with recovery request if validation passes
    if (waitReply === 0) {
        waitReply = 1;
        $.post('system/action/recovery.php', {
            remail: rEmail
        }, function(response) {
            switch(response) {
                case '1': // Successful recovery
                    $('#recovery_email').val("");
                    hideModal();
                    callSaved(system.recoverySent, 1);
                    break;
                case '2': // No user found
                    $('#recovery_email').val("");
                    callSaved(system.noUser, 3);
                    break;
                case '3': // Invalid email
                    $('#recovery_email').val("");
                    callSaved(system.invalidEmail, 3);
                    break;
                default: // Handle other errors
                    hideModal();
                    callSaved(system.error, 3);
                    break;
            }
            waitReply = 0; // Reset waitReply flag
        });
    } 
    return false; // Prevent form submission
}

bridgeLogin = function(path){
	if(waitReply == 0){
		waitReply = 1;
		$.post('../boom_bridge.php', {
			path: path,
			special_login: 1,
			}, function(response) {
				if (response == 1){
					location.reload();
				}
				else {
					callSaved(system.siteConnect, 3);
				}
				waitReply = 0;
		});
	}
}
hideCookieBar = function(){
	$.post('system/action/cookie_law.php', {
		cookie_law: 1,
		}, function(response) {
			$('.cookie_wrap').fadeOut(400);
	});
}
resetRecaptcha = function(){
	if(recapt > 0){
		grecaptcha.reset();
	}
}
renderRecaptcha = function(){
	if(recapt > 0){
		grecaptcha.render("boom_recaptcha", { 'sitekey': recaptKey, });
	}
}
getRecapt = function(){
	if(recapt > 0){
		return grecaptcha.getResponse();
	}
	else {
		return 'disabled';
	}
}