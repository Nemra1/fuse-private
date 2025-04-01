<?php

require __DIR__ . "/../../../../config_session.php";

if (!boomAllow(100)) {
    exit;
}

echo elementTitle($lang["page"]);
?>
<div class="page_full">
    <div>
        <div class="tab_menu">
            <ul>
                <li class="tab_menu_item tab_selected" data="filter_tab" data-z="privacy_policy">
                    <?php echo $lang["privacy"]; ?>
                </li>
                <li class="tab_menu_item" data="filter_tab" data-z="terms_of_use">
                    <?php echo $lang["rules"]; ?>
                </li>
                <li class="tab_menu_item" data="filter_tab" data-z="help">
                    <?php echo $lang["help"]; ?>
                </li>
            </ul>
        </div>
    </div>
    <div id="filter_tab">
        <div id="privacy_policy" class="tab_zone">
            <div class="page_element">
                <textarea id="privacy_text" class="bmargin10 full_textarea edit_page_box">
                    <?php echo loadPageData("privacy_policy"); ?>
                </textarea>
                <button id="add_word" onclick="savePageData('privacy_policy', 'privacy_text');" type="button" class="reg_button theme_btn">
                    <i class="fa fa-save"></i> <?php echo $lang["save"]; ?>
                </button>
            </div>
        </div>
        <div id="terms_of_use" class="tab_zone hide_zone">
            <div class="page_element">
                <textarea id="terms_text" class="bmargin10 full_textarea edit_page_box">
                    <?php echo loadPageData("terms_of_use"); ?>
                </textarea>
                <button id="add_word" onclick="savePageData('terms_of_use', 'terms_text');" type="button" class="reg_button theme_btn">
                    <i class="fa fa-save"></i> <?php echo $lang["save"]; ?>
                </button>
            </div>
        </div>
        <div id="help" class="tab_zone hide_zone">
            <div class="page_element">
                <textarea id="help_text" class="bmargin10 full_textarea edit_page_box">
                    <?php echo loadPageData("help"); ?>
                </textarea>
                <button id="add_word" onclick="savePageData('help', 'help_text');" type="button" class="reg_button theme_btn">
                    <i class="fa fa-save"></i> <?php echo $lang["save"]; ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
