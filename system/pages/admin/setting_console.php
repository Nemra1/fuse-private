<?php

require __DIR__ . "../../../config_session.php";
if(!canManageConsole()){
	die();
}
echo elementTitle($lang["system_logs"]);
?>

<div class="page_full">
    <div class="page_element">
        <?php if (boomAllow($cody["can_clear_console"])): ?>
            <div class="bpad15">
                <button onclick="clearConsole();" class="reg_button delete_btn">
                    <i class="ri-delete-bin-2-fill"></i> <?php echo htmlspecialchars($lang["clear"], ENT_QUOTES, 'UTF-8'); ?>
                </button>
            </div>
        <?php endif; ?>

        <div id="console_logs_box">
            <div class="bpad15 console_logs_search">
                <input onkeyup="searchSystemConsole();" id="search_system_console" placeholder="🧿" class="full_input" type="text" />
            </div>
            <div id="console_results" class="box_height"></div>
            <div id="console_spinner" class="vpad10 centered_element" style="min-height: 295px;position: relative;"></div>
                
            
        </div>
    </div>
</div>
<script>
$("#console_spinner").html(largeSpinner);    
</script>
<?php
?>
