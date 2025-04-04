var waitReply = 0;
let recaptchaWidgets = {
	login: null,
	register: null,
	guest: null
};

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
			resetRecaptcha('guest');    // Reset the Recaptcha for the guest form
			if(response != 0){
				showModal(response);
				renderRecaptcha('guest', 'recaptcha_guest'); // re-render after AJAX load
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
				renderRecaptcha('register', 'boom_recaptcha_register');
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
sendLogin = function() {
    var upass = $('#user_password').val();
    var uuser = $('#user_username').val();
    let loginToken = getRecaptchaToken('login');  // Get the Recaptcha token for the login form
    if (upass == '' || uuser == '') {
        callSaved(system.emptyField, 3);
        return false;
    }
    else if (/^\s+$/.test($('#user_password').val())) {
        callSaved(system.emptyField, 3);
        $('#user_password').val("");
        return false;
    }
    else if (/^\s+$/.test($('#user_username').val())) {
        callSaved(system.emptyField, 3);
        $('#user_username').val("");
        return false;
    }
    else {
        // Validate Recaptcha if required
        if (recapt > 0 && loginToken === '') {
            callSaved(system.missingRecaptcha, 3);
            return false;
        }
        // Proceed with login if no issues
        if (waitReply == 0) {
            waitReply = 1;
            $.post(FU_Ajax_Requests_File(), {
                f: "system_login",
                s: "member_login",
                password: upass,
                username: uuser,
                recaptcha_response: loginToken  // Include Recaptcha token in the request
            }, function(res) {
                if (res.code == 1) {
                    callSaved(system.badLogin, 3);
                    $('#user_password').val("");
                }
                else if (res.code == 2) {
                    callSaved(system.badLogin, 3);
                    $('#user_password').val("");
                }
                else if (res.code == 3) {
                    callSaved(res.msg, 1);
                    setTimeout(function() {
                        location.reload(true);  // Reload the page after a successful login
                    }, res.reload_delay || 2000); // Default delay if not specified
                }
                waitReply = 0;
            });
        } else {
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
	let regRecapt = getRecaptchaToken('register');
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
    var gname = $('#guest_username').val().trim();
    var ggender = $('#guest_gender').val();
    var gage = $('#guest_age').val();
    var guestRecapt = getRecaptchaToken('guest');
    // Check if the username is empty or just whitespace
    if (!gname || /^\s+$/.test(gname)) {
        callSaved(system.emptyField, 3);
        $('#guest_username').val(""); // Clear input
        return false;
    }
    // Check reCAPTCHA
    if (recapt > 0 && guestRecapt === '') {
        callSaved(system.missingRecaptcha, 3);
        return false;
    }
    if (waitReply === 0) {
        waitReply = 1;
        $.post(FU_Ajax_Requests_File(), {
            f: "system_login",
            s: "guest_login",
            guest_name: gname,
            guest_gender: ggender,
            guest_age: gage,
            recaptcha: guestRecapt
        }, function(response) {
            // Reset guest reCAPTCHA only if needed
            if (response.code != 1) {
                resetRecaptcha('guest');
            }
            // Handle server response
            switch (response.code) {
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
                case 13:
                    callSaved(system.selAge, 3);
                    break;
                case 14:
                    callSaved(system.error, 3);
                    break;
                case 16:
                    callSaved(system.maxReg, 3);
                    break;
                case 1:
                    location.reload(); // Success
                    break;
                default:
                    callSaved(system.error, 3);
            }

            waitReply = 0;
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

// Render Recaptcha for any form (login, register, etc.)
function renderRecaptcha(form, elementId) {
    if (typeof grecaptcha === 'undefined') return;  // Check if Recaptcha script is loaded
    if (recaptchaWidgets[form] !== null) {
        // If Recaptcha widget is already rendered, reset it
        grecaptcha.reset(recaptchaWidgets[form]);
    } else if (document.getElementById(elementId)) {
        // If the element exists, render the Recaptcha
        recaptchaWidgets[form] = grecaptcha.render(elementId, {
            'sitekey': recaptKey // Ensure 'recaptKey' is set correctly
        });
    }
}
function resetRecaptcha(form) {
    if (recaptchaWidgets[form] !== null) {
        grecaptcha.reset(recaptchaWidgets[form]);
    }
}
function getRecaptchaToken(form) {
    if (recaptchaWidgets[form] !== null) {
        const token = grecaptcha.getResponse(recaptchaWidgets[form]);
        console.log(token); // Debugging: Log the token to check if it's being retrieved
        return token;
    }
    return '';
}
// Ensure Recaptcha is rendered when the page loads for login form
document.addEventListener('DOMContentLoaded', function () {
    if (typeof grecaptcha !== 'undefined') {
        renderRecaptcha('login', 'recaptcha_login');
		renderRecaptcha('register', 'recaptcha_register');
    }

});
// Example: Reset Recaptcha for login form after the page reloads
window.addEventListener('load', function() {
    resetRecaptcha('login');    // Reset the Recaptcha for the login form
    resetRecaptcha('register'); // Reset the Recaptcha for the register form
    resetRecaptcha('guest');    // Reset the Recaptcha for the guest form
});