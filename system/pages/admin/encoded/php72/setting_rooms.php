<?php

require __DIR__ . "/../../../../config_session.php";

if (!boomAllow(90)) {
    exit;
}

echo elementTitle($lang["room_management"]);
?>
<div class="page_full">
    <div class="page_element">
        <div id="rooms_list">
            <?php if (canRoom()): ?>
                <div class="admin_add_room">
                    <button onclick="adminCreateRoom();" class="reg_button theme_btn">
                        <i class="ri-save-3-fill"></i> <?= $lang["add_room"]; ?>
                    </button>
                </div>
            <?php endif; ?>
            <div id="rom_search" class="vpad15">
                <div class="search_bar">
                    <input id="search_admin_room" placeholder="☮" class="full_input" type="text"/>
                    <div class="clear"></div>
                </div>
            </div>
            <div id="room_listing">
                <?= adminRoomList2(); ?>
            </div>
        </div>
    </div>
</div>

<?php

function adminRoomList2()
{
    global $mysqli, $lang;
    $list_rooms = "";
    $getrooms = $mysqli->query("SELECT boom_rooms.* FROM boom_rooms ORDER BY room_name ASC");

    if ($getrooms->num_rows > 0) {
        while ($room = $getrooms->fetch_assoc()) {
            $list_rooms .= boomTemplate("element/admin_room", $room);
        }
    } else {
        $list_rooms .= emptyZone($lang["empty"]);
    }

    return $list_rooms;
}

?>
