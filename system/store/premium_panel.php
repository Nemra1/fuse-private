<style>
.premium-drop-zone { max-width: 100%; height: 200px; padding: 5px; display: flex; align-items: center; justify-content: center; text-align: center; font-family: "Quicksand", sans-serif; font-weight: 500; font-size: 20px; cursor: pointer; color: #8b00d1; border: 2px dashed #8b00d1; border-radius: 10px; }
.drop-zone--over { border-style: solid; }
.drop-zone__input { display: none; }
.drop-zone__thumb { width: 100%; height: 100%; border-radius: 10px; overflow: hidden; background-color: #cccccc; background-size: contain; position: relative; background-repeat: no-repeat; margin: 0 auto; background-position: center; }
.drop-zone__thumb::after { content: attr(data-label); position: absolute; bottom: 0; left: 0; width: 100%; padding: 5px 0; color: #ffffff; background: rgba(0, 0, 0, 0.75); font-size: 14px; text-align: center; }
.profile-input-cor.-cor3 { width: 30px; color: white; border-radius: 50%; text-shadow: 0px 0px 5px black; -webkit-animation: fundoPride 0.7s infinite; animation: fundoPride 0.7s infinite; transition: all 0.1s ease-in-out; background-image: linear-gradient(to right, #ff0000, #ff00c8, #9d00ff, #0400ff, #00e7ff, #00ff1f, #d8ff00, #ff8200, #ff0000); border-color: transparent; }
.profile-input-cor { display: block; float: left; width: 130px; margin: 10px 20px 10px 0; border: thin solid; cursor: pointer; text-align: center; padding: 8px 0; font: 12px sans-serif; background-color: white; border-radius: 5px; -webkit-animation: bordaColor 3s infinite; animation: bordaColor 3s infinite; }
#loading-bar { position: relative; z-index: 2147483647; top: 0; left: -6px; width: 0; height: 9px; background: #1d91f3; -moz-border-radius: 1px; -webkit-border-radius: 1px; border-radius: 1px; -moz-transition: width 500ms ease-out, opacity 400ms linear; -ms-transition: width 500ms ease-out, opacity 400ms linear; -o-transition: width 500ms ease-out, opacity 400ms linear; -webkit-transition: width 500ms ease-out, opacity 400ms linear; transition: width 500ms ease-out, opacity 400ms linear; }

</style>

<?php if(isPremium($data)){ ?>
<div class="modal_menu">
	<ul>
		<li id="Profile_Music_button" class="modal_menu_item modal_selected" data="fuse_premium" data-z="music_tabe"><i class="ri-account-circle-line"></i><?php echo $lang['store_music_panel'];?></li>
		<li id="Profile_color_button" class="modal_menu_item" data="fuse_premium" data-z="color_tabe"><i class="ri-account-circle-line"></i><?php echo $lang['store_css_panel'];?></li>
		<li id="control_color_button" class="modal_menu_item" data="fuse_premium" data-z="control_tabe"><i class="ri-account-circle-line"></i><?php echo $lang['store_reset'];?></li>
	</ul>
</div>

<div id="fuse_premium">
	<div id="loading-bar" style="width: 0px; display: none;"></div>
	<div class="modal_zone pad10 top_background" id="music_tabe">
		<div class="profile_music_player" style=" text-align: center; margin: 0 auto; display: flex; justify-content: center; "></div>
		  <div class="premium-drop-zone" id="music_tabe_form">
			<i class="ri-disc-line"></i><span class="drop-zone__prompt">Drop file here or click to upload</span>
			<input type="file" id="profile_song" name="target_mp3_file" class="drop-zone__input" accept=".mp3">
				<div class="progress">
					<div id="uploadProgressBar" class="progress-bar" style="width: 0%; display: none;"></div>
				</div>

		  </div>
			<div class="setting_element full_border">
				<p class="label"><i class="ri-disc-line"></i>Profile Music Background</p>
				<small class="error">When someone visits your profile they will hear this music</small>
			</div>
		  
		<div class="pad10 centered_element full_border" id="target_control" style=" display: none; ">
			<button  class="reg_button theme_btn" onclick="uploadProfileSong();">Update</button>
			<button class="reg_button close_over delete_btn">Cancel</button>
		</div>
	</div>
	<div class="modal_zone pad10 top_background hide_zone" id="color_tabe">
		<div class="clearbox">
			<div class="label"><i class="ri-color-filter-fill"></i>Profile color Customization
				<input  data-line="pro_text_main" type="color" class="color-input hidden" id="pro_text_main" value="<?php echo $data['pro_text_main']; ?>">	
				<input data-line="pro_text_sub" type="color" class="color-input hidden" id="pro_text_sub" value="<?php echo $data['pro_text_sub']; ?>">	
			</div>
			
			<div class="listing_half_element info_pro">
				<label for="pro_text_main" class="profile-input-cor -cor3" title="Text Color">
					<i class="ri-paint-brush-line"></i>
				</label>
				<div class="listing_title pro_text_main">Text Color </div>
				<div class="listing_text pro_subtext">Sub-text color</div>
			</div>

			<div class="listing_half_element info_pro">
				<label for="pro_text_sub" class="profile-input-cor -cor3" title="Sub-text color">
					<i class="ri-paint-brush-line"></i>
				</label>			
				<div class="listing_title pro_text_main">Text Color </div>
				<div class="listing_text pro_subtext">Sub-text color</div>
			</div>
		<div class="pad10 centered_element full_border" id="pro_text_control" style=" display: none; ">
			<button  class="reg_button theme_btn" onclick="UpdateProfile_style();">Upload</button>
			<button class="reg_button close_over delete_btn">Cancel</button>
		</div>			
		</div>
		<div class="label"><i class="ri-color-filter-fill"></i>Profile Background</div>	
		  <div class="premium-drop-zone">
			<i class="ri-folder-image-line"></i><span class="drop-zone__prompt">Drop file here or click to upload</span>
			<input type="file" id="pro_background"  name="pro_background" class="drop-zone__input">
		  </div>		
		<div class="pad10 centered_element full_border" id="ProBackground_control" style=" display: none; ">
			<button  class="reg_button theme_btn" onclick="uploadProBackground();">Upload</button>
			<button class="reg_button close_over delete_btn">Cancel</button>
		</div>
		  
	</div>
	<div class="modal_zone pad10 top_background hide_zone" id="control_tabe">
		<div class="pad20 centered_element">
			<p class="text_ultra theme_color"><i class="ri-command-line"></i></p>
			<p class="bpad15 listing_title pro_text_main">Do you would to Reset Profile style?</p>
			<p class="bpad15">Profile wings,avatar frames,profile colors,Profile Song wil deleted !</p>
			<button onclick="reset_profile();" class="reg_button delete_btn"><?php echo $lang['ok'];?></button>
			<button class="reg_button cancel_modal ok_btn"><?php echo $lang['no'];?></button>
		</div>	
	</div>
</div>
<?php }else{ ?>
<div class="transaction_content box_height600" value="1"><div class="empty_zone">
	<img class="empty_zone_icon" src="<?php echo $data['domain'];?>/default_images/icons/upgrade_icon.gif">
	<p class="empty_zone_text sub_text">This Option for Premium Users Only</p>
</div></div>
<?php } ?>
<script>
document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
  const dropZoneElement = inputElement.closest(".premium-drop-zone");
  dropZoneElement.addEventListener("click", (e) => {
    inputElement.click();
  });
  inputElement.addEventListener("change", (e) => {
    if (inputElement.files.length) {
      updateThumbnail(dropZoneElement, inputElement.files[0]);
    }
  });
  dropZoneElement.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropZoneElement.classList.add("drop-zone--over");
  });
  ["dragleave", "dragend"].forEach((type) => {
    dropZoneElement.addEventListener(type, (e) => {
      dropZoneElement.classList.remove("drop-zone--over");
    });
  });
  dropZoneElement.addEventListener("drop", (e) => {
    e.preventDefault();
    if (e.dataTransfer.files.length) {
      inputElement.files = e.dataTransfer.files;
      updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
    }
    dropZoneElement.classList.remove("drop-zone--over");
  });
});
function startLoadingBar() {
    $("#loading-bar").show();
    $("#loading-bar").width((50 + Math.random() * 30) + "%");
}

function stopLoadingBar() {
    $("#loading-bar").width("101%").delay(200).fadeOut(400, function() {
        $(this).width("0");
    });
}
/**
 * Updates the thumbnail on a drop zone element.
 *
 * @param {HTMLElement} dropZoneElement
 * @param {File} file
 */
function updateThumbnail(dropZoneElement, file) {
  let $dropZoneElement = $(dropZoneElement); // Convert dropZoneElement to jQuery object
  let $thumbnailElement = $dropZoneElement.find(".drop-zone__thumb");
  let $control;  // jQuery selector for the control
  // First time - remove the prompt using jQuery
  $dropZoneElement.find(".drop-zone__prompt").remove();
  // First time - there is no thumbnail element, so let's create it
  if ($thumbnailElement.length === 0) {
    $thumbnailElement = $("<div></div>").addClass("drop-zone__thumb");
    $dropZoneElement.append($thumbnailElement);
  }
  $thumbnailElement.html('');
  // Set the file name as data-label attribute using jQuery
  $thumbnailElement.attr("data-label", file.name);
  // Show thumbnail based on file type
  const reader = new FileReader();
  if (file.type.startsWith("audio/mpeg")) {
    reader.readAsDataURL(file);
    reader.onload = function() {
		$control = $("#target_control");  
      $thumbnailElement.css("background-image", "url('default_images/mp3_icon.gif')");
      $control.show();  // Show the control using jQuery
    };
  } else if (file.type === "image/gif" || file.type === "image/png" || file.type === "image/jpg" || file.type === "image/jpeg") {
    reader.readAsDataURL(file);
    reader.onload = function() {
		$control = $("#ProBackground_control"); 
      $thumbnailElement.css("background-image", `url(${reader.result})`);
      $control.show();  // Hide the control for non-audio files using jQuery
    };
  } else {
    $thumbnailElement.html('<i class="ri-error-warning-fill"></i> Is Not Supported File');
    $thumbnailElement.css("background-image", "none"); // Remove background image
  }
}

var waitAvatarSong = 0;
uploadProfileSong = function() {
    var drpz = $("#music_tabe_form");
    var file_data = $("#profile_song").prop("files")[0];

    if ($("#profile_song").val() === "") {
        callSaved('You must choose a song first', 3);
    } else {
        if (waitAvatarSong == 0) {
            waitAvatarSong = 1;
            uploadIcon('avat_icon_song', 1);

            var form_data = new FormData();
            form_data.append("file", file_data);
            form_data.append("token", utk);
            form_data.append("f", "store");
            form_data.append("s", "profile_music");

            // Show progress bar
            $("#loading-bar").css("width", "0%").show();

            $.ajax({
                url: FU_Ajax_Requests_File(),
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'POST',
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(event) {
                        if (event.lengthComputable) {
                            var percentComplete = (event.loaded / event.total) * 100;
                            //$("#loading-bar").css("width", percentComplete + "%");
							startLoadingBar();
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    if (response.code == 1) {
                        drpz.hide();
                        $(".profile_music_player").html(response.html);
                        callSaved(response.message, 1);
                    } else {
                        callSaved(response.message, 3);
                    }
                    stopLoadingBar(); // Hide progress bar on complete
                    uploadIcon('avat_icon_song', 2);
                    waitAvatarSong = 0;
                },
                error: function() {
                    callSaved('Upload error', 3);
						stopLoadingBar();
                    uploadIcon('avat_icon_song', 2);
                    waitAvatarSong = 0;
                }
            });
        }
    }
}

var waitProBackground = 0;
uploadProBackground = function() {
    var file_data = $("#pro_background").prop("files")[0];
    if ($("#pro_background").val() === "") {
        callSaved(system.noFile, 3);
    } else {
        if (waitProBackground == 0) {
            waitProBackground = 1;
            startLoadingBar();
            uploadIcon('pro_bg_icon', 1);
            var form_data = new FormData();
            form_data.append("file", file_data);
            form_data.append("token", utk);
            form_data.append("f", "store");
            form_data.append("s", "pro_background");
            
            var xhr = new XMLHttpRequest();
            xhr.open("POST", FU_Ajax_Requests_File(), true);
            
            xhr.upload.addEventListener("progress", function(e) {
                if (e.lengthComputable) {
                    var percentComplete = (e.loaded / e.total) * 100;
                    //$("#progressBar").css("width", percentComplete + "%").text(Math.round(percentComplete) + "%");
					startLoadingBar();
                }
            });
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.code == 1) {
                        callSaved(response.message, 3);
                    } else if (response.code == 5) {
                        callSaved(response.message, 1);
                    } else {
                        callSaved(system.error, 3);
                    }
                } else {
                    callSaved(system.error, 3);
                }
                uploadIcon('pro_bg_icon', 2);
                waitProBackground = 0;
                stopLoadingBar();
            };
            
            xhr.onerror = function() {
                callSaved(system.error, 3);
                uploadIcon('pro_bg_icon', 2);
                waitProBackground = 0;
                stopLoadingBar();
            };
            
            xhr.send(form_data);
        } else {
            return false;
        }
    }
}

reset_profile = function() {
	$.post(FU_Ajax_Requests_File(), {
		f: "store",
		s: "reset_style",
		token: utk,
		}, function(response) {
			 callSaved(response.message, 1);
	});	
	
}
UpdateProfile_style = function() {
	var $pro_text_main = $("#pro_text_main");
	var $pro_text_sub = $("#pro_text_sub");
	$.post(FU_Ajax_Requests_File(), {
		f: "store",
		s: "pro_style",
		pro_text_main: $pro_text_main.val(),
		pro_text_sub: $pro_text_sub.val(),
		token: utk,
		}, function(response) {
			 callSaved(response.message, 1);
	});	
}
$(document).ready(function() {
	$(".color-input").on("change", function() {
		var $color = $(this).val();
		var $selected_line = $(this).data('line');
		var $control = $("#pro_text_control");
		if($selected_line=="pro_text_main"){
			$(".pro_text_main").css("color", $color);
		}else if($selected_line=="pro_text_sub"){
			$(".pro_subtext").css("color", $color);
		}
		$control.show();
	});
<?php if (!empty($data['pro_text_main'])) { ?>
var mainTextCol = '<?php echo $data['pro_text_main']; ?>';
$('.listing_title').css('color', mainTextCol);
$('#ex_menu').css('color', mainTextCol);
<?php } ?>
<?php if (!empty($data['pro_text_sub'])) { ?>
var subTextCol = '<?php echo $data['pro_text_sub']; ?>';
$('.listing_text').css('color', subTextCol);
<?php } ?>	
});
</script>
