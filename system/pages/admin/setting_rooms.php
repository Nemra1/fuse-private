<?php

require __DIR__ . "../../../config_session.php";

if (!boomAllow(90)) {
    exit;
}

echo elementTitle($lang["room_management"]);
?>
<div class="page_full">
    <div class="page_element">
        <div id="rooms_list">
            <?php if (canRoom()) : ?>
                <div class="admin_add_room">
                    <button onclick="adminCreateRoom();" class="reg_button theme_btn">
                        <i class="ri-save-3-fill"></i> <?= $lang["add_room"] ?>
                    </button>
                </div>
            <?php endif; ?>
			<div class="setting_element ">
				<p class="label">Enable Room Tabs</p>
				<select id="set_room_tabs">
					<?php echo onOff($data['use_room_tabs']); ?>
				</select>
			</div>

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
<script>
	$(document).on('change', '#set_room_tabs', function(){
		var room_tabs = $(this).val();
		if(room_tabs === 0){
			return false;
		}
		else {
			$.post(FU_Ajax_Requests_File(), {
				f:'action_room',
				s:'admin_update_tabs',
				room_tabs: $(this).val(),
				token: utk,
				}, function(response) {
					callSaved(system.saved, 1);
			});
		}
	});
</script>
<?php

function adminRoomList2()
{
    global $mysqli, $lang;

    $list_rooms = "";
    $query = "SELECT * FROM boom_rooms ORDER BY room_name ASC";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        while ($room = $result->fetch_assoc()) {
            $list_rooms .= boomTemplate("element/admin_room", $room);
        }
    } else {
        $list_rooms .= emptyZone($lang["empty"]);
    }

    return $list_rooms;
}

?>
