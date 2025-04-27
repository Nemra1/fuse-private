<?php

require __DIR__ . "../../../config_admin.php";

if (!boomAllow(100)) {
    exit;
}

echo elementTitle('Websocket Setting');

?>
   <style>
.boom_form {
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form_group {
    margin-bottom: 15px;
	text-align: center;
}

.form_label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.logo_placeholder {
    position: relative;
    width: 200px;
    height: 200px;
    margin: 0 auto;
    text-align: center;
    border: 2px dashed #ccc;
    border-radius: 8px;
    cursor: pointer;
    overflow: hidden;
}

.logo_placeholder img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
}

.hidden_input {
    display: none;
}

.default_btn {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.default_btn:hover {
    background-color: #0056b3;
}

.default_btn i {
    margin-right: 5px;
}
</style>
<div class="page_full">
    <div>
        <div class="tab_menu">
            <ul>
                <li class="tab_menu_item tab_selected" data="main_tab" data-z="logo_zone">Logo Setup</li>
                <li class="tab_menu_item" data="main_tab" data-z="consolet_zone">Console</li>
            </ul>
        </div>
    </div>
    <div id="main_tab">
        <div id="logo_zone" class="tab_zone">
            <div class="page_element">
                <div class="boom_form">
                    <!-- Logo Uploader Form -->
                    <form id="logoUploaderForm" enctype="multipart/form-data">
                        <div class="form_group">
                            <label for="logoFile" class="form_label">Upload Your Logo</label>
                            <!-- Placeholder/Image Container -->
                            <div id="logoPlaceholder" class="logo_placeholder">
                                <img id="previewImage" src="default_images/icon.png" alt="Click to upload logo" />
                                <input type="file" id="logoFile" name="logoFile" accept="image/*" class="hidden_input" />
                            </div>
                        </div>
                        <div class="form_group">
                            <button type="submit" class="default_btn" id="uploadLogoBtn">
                                <i class="ri-upload-cloud-2-line"></i> Upload Logo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="consolet_zone" class="tab_zone hide_zone" style="display: none;">
            <div class="page_element">
                <div class="boom_form">
                    <div class="bpad15"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script data-cfasync="false">
$(document).ready(function () {
    const $logoPlaceholder = $('#logoPlaceholder');
    const $previewImage = $('#previewImage');
    const $logoFileInput = $('#logoFile');
    const $logoUploaderForm = $('#logoUploaderForm');
    // Make the placeholder/image clickable to trigger file input
    $logoPlaceholder.on('click', function () {
        $logoFileInput.trigger('click'); // Trigger the hidden file input
    });
    // Prevent event bubbling from the file input to avoid infinite loops
    $logoFileInput.on('click', function (event) {
        event.stopPropagation(); // Stop the event from propagating to the parent
    });
    // Show preview of the selected image
    $logoFileInput.on('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $previewImage.attr('src', e.target.result); // Update the image source
            };
            reader.readAsDataURL(file);
        } else {
            $previewImage.attr('src', 'default_images/icon.png'); // Reset to default placeholder if no file is selected
        }
    });
    // Handle form submission
    $logoUploaderForm.on('submit', function (event) {
        event.preventDefault(); // Prevent default form submission
        const formData = new FormData(this);
		    formData.append("f", 'admin_actions')
            formData.append("s", 'logo_Setup')
        // Simulate an API call to upload the logo
        $.ajax({
            url: FU_Ajax_Requests_File(), // Dynamic URL for the AJAX request
            type: 'POST',
            data: formData,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will handle it
            success: function (data) {
                if (data.success) {
					 callSaved('Logo uploaded successfully!', 1);
                    // Optionally update the UI to reflect the new logo
                } else {
					 callSaved('Failed to upload logo. Please try again!', 3);
                }
            },
            error: function (error) {
                console.error('Error uploading logo:', error);
				callSaved('An error occurred while uploading the logo.', 3);
            },
        });
    });
});
</script>