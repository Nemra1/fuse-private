<?php

require __DIR__ . "../../../config_session.php";


if (!boomAllow(90)) {
    exit;
}

echo elementTitle($lang["display_settings"]);
?>
<style>
#imageContainer { display: flex; justify-content: center; align-items: center; height: 282px; margin-top: 15px; border: 1px solid #cccccc66; padding: 0px; background-color: #F44336; max-width: 555px; border-radius: 15px; }
#themeImage ,#loginImage{ max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 15px; }
    
</style>
<div class="page_full">
    <div class="page_element">
        <div class="boom_form">
            <div class="setting_element">
                <p class="label"><?php echo htmlspecialchars($lang["theme"], ENT_QUOTES, 'UTF-8'); ?></p>
                <select id="set_main_theme">
                    <?php echo listTheme($data["default_theme"], 1); ?>
                </select>
                <div id="imageContainer">
                       <img id="themeImage" src="css/themes/<?php echo $data['default_theme']; ?>/<?php echo $data['default_theme']; ?>.png" alt="Theme Image">
                </div>
            </div>
            <div class="setting_element">
                <p class="label"><?php echo htmlspecialchars($lang["login_page"], ENT_QUOTES, 'UTF-8'); ?></p>
                <select id="set_login_page">
                    <?php echo listLogin(); ?>
                </select>
                <div id="imageContainer">
                       <img id="loginImage" src="control/login/<?php echo $data['login_page']; ?>/<?php echo $data['login_page']; ?>.png" alt="login Image">
                </div>
                
            </div>

            <button data="display" type="button" class="save_admin reg_button theme_btn">
                <i class="ri-save-line"></i> <?php echo htmlspecialchars($lang["save"], ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </div>
    </div>
</div>
<script>
$('#set_main_theme').on('change', function() {
   var selectedTheme = $(this).val();
    // Correct image path: theme name as folder and image name
    var themeImage = 'css/themes/' + selectedTheme + '/' + selectedTheme + '.png';
    // Preload the image to avoid flickering or delays
    var img = new Image();
    img.src = themeImage;
    // When the image is fully loaded, apply it with a smooth fade-in effect
    $(img).on('load', function() {
        $('#themeImage').fadeOut(200, function() {
            $(this).attr('src', themeImage).fadeIn(200);
        });
    }).on('error', function() {
        // Handle image loading errors (e.g., show a placeholder image)
        var fallbackImage = 'css/themes/placeholder.png';
        $('#themeImage').fadeOut(200, function() {
            $(this).attr('src', fallbackImage).fadeIn(200);
        });
    });
    console.log('Theme:', selectedTheme);
    console.log('Image:', themeImage);
    
});
$('#set_login_page').on('change', function() {
    var selectedTheme = $(this).val();
    // Correct image path: theme name as folder and image name
    var themeImage = 'control/login/' + selectedTheme + '/' + selectedTheme + '.png';
    // Preload the image to avoid flickering or delays
    var img = new Image();
    img.src = themeImage;
    // When the image is fully loaded, apply it with a smooth fade-in effect
    $(img).on('load', function() {
        $('#loginImage').fadeOut(200, function() {
            $(this).attr('src', themeImage).fadeIn(200);
        });
    }).on('error', function() {
        // Handle image loading errors (e.g., show a placeholder image)
        var fallbackImage = 'control/login/placeholder.png';
        $('#loginImage').fadeOut(200, function() {
            $(this).attr('src', fallbackImage).fadeIn(200);
        });
    });
    console.log('Theme:', selectedTheme);
    console.log('Image:', themeImage);
});

   
</script>

<?php

/**
 * Generates the HTML options for login page selection.
 *
 * @return string HTML options for login page selection.
 */
function listLogin()
{
    global $data;
    global $lang;

    $login_list = "";
    $dir = glob(BOOM_PATH . "/control/login/*", GLOB_ONLYDIR);

    foreach ($dir as $dirnew) {
        $login = basename($dirnew); // Simplifies extracting the directory name
        if (file_exists(BOOM_PATH . "/control/login/$login/login.php")) {
            $selected = selCurrent($data["login_page"], $login);
            $login_list .= "<option value=\"$login\" $selected>" . htmlspecialchars($login, ENT_QUOTES, 'UTF-8') . "</option>";
        }
    }

    return $login_list;
}
?>
