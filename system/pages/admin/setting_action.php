<?php

require __DIR__ . "../../../config_session.php";
//if(!boomAllow(min($setting['can_kick'], $setting['can_ghost'], $setting['can_mute'], $setting['can_ban']))){
//	die();
//}
if (!boomAllow(70)) {
    exit;
}

echo elementTitle($lang["manage_action"]);

?>

<div class="page_full">
    <div>
        <div class="tab_menu">
            <ul>
                <?php if(canMute()){ ?>
                <li class="tab_menu_item tab_selected" data="action_filter" data-z="muted_filter">
                    <?php echo $lang["muted"]; ?>
                </li>
                <li class="tab_menu_item " data="action_filter" data-z="mmuted_filter">
                    <?php echo $lang["main_muted"]; ?>
                </li>  
                <li class="tab_menu_item " data="action_filter" data-z="pmuted_filter">
                    <?php echo $lang["private_muted"]; ?>
                </li>                 
                <?php } ?>
                <?php if(canGhost()){ ?>
                <li class="tab_menu_item " data="action_filter" data-z="ghosted_filter">
                    <?php echo $lang["ghosted"]; ?>
                </li>                 
                 <?php } ?>
                <?php if (canKick()): ?>
                    <li class="tab_menu_item" data="action_filter" data-z="kicked_filter">
                        <?php echo $lang["kicked"]; ?>
                    </li>
                <?php endif; ?>
                <?php if (canBan()): ?>
                    <li class="tab_menu_item" data="action_filter" data-z="banned_filter">
                        <?php echo $lang["banned"]; ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div id="action_filter">
        <div id="muted_filter" class="tab_zone">
            <div class="page_element">
                <div id="action_muted_list">
                    <?php echo getActionList("muted"); ?>
                </div>
            </div>
        </div>
        
         <div id="mmuted_filter" class="tab_zone hide_zone">
            <div class="page_element">
                <div id="action_mmuted_list">
                    <?php echo getActionList("mmuted"); ?>
                </div>
            </div>
        </div> 
       <div id="pmuted_filter" class="tab_zone hide_zone">
            <div class="page_element">
                <div id="action_pmuted_list">
                    <?php echo getActionList("pmuted"); ?>
                </div>
            </div>
        </div>
         <?php if (canGhost()): ?>
            <div id="ghosted_filter" class="hide_zone tab_zone">
                <div class="page_element">
                    <div id="action_ghosted_list">
                        <?php echo getActionList("ghosted"); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>     
        <?php if (canKick()): ?>
            <div id="kicked_filter" class="hide_zone tab_zone">
                <div class="page_element">
                    <div id="action_kicked_list">
                        <?php echo getActionList("kicked"); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (canBan()): ?>
            <div id="banned_filter" class="tab_zone hide_zone">
                <div class="page_element">
                    <div id="action_banned_list">
                        <?php echo getActionList("banned"); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
?>