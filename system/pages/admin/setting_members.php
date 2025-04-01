<?php

require_once('../../config_session.php');

if (!boomAllow(70)) {
    exit;
}

echo elementTitle($lang["users_management"]);
?>
<div class="page_full">
    <div class="page_element">
        <?php if (boomAllow(90)): ?>
            <button onclick="createUser();" class="theme_btn bmargin10 reg_button">
                <i class="ri-save-3-fill"></i> <?php echo $lang["add_user"]; ?>
            </button>
        <?php endif; ?>
        <p class="label"><?php echo $lang["search_member"]; ?></p>
        <div class="admin_search">
            <div class="admin_input bcell">
                <input class="full_input" id="member_to_find" type="text"/>
            </div>
            <div id="search_member" class="admin_search_btn default_btn">
                <i class="ri-search-eye-fill" aria-hidden="true"></i>
            </div>
        </div>
        <div class="setting_element">
            <p class="label"><?php echo $lang["advance_search"]; ?></p>
            <select id="member_critera">
                <option value="0" selected disabled><?php echo $lang["select_critera"]; ?></option>
                <?php if ($data["allow_guest"] == 1): ?>
                    <option value="1"><?php echo $lang["guest"]; ?></option>
                <?php endif; ?>
                <option value="2"><?php echo $lang["user"]; ?></option>
                <option value="3"><?php echo $lang["vip"]; ?></option>
                <option value="11"><?php echo $lang["premium"]; ?></option>
                <option value="4"><?php echo $lang["mod"]; ?></option>
                <option value="5"><?php echo $lang["admin"]; ?></option>
                <option value="10"><?php echo $lang["super_admin"]; ?></option>
                <option value="6"><?php echo $lang["user_bot"]; ?></option>
                <option value="7"><?php echo $lang["muted"]; ?></option>
                <option value="8"><?php echo $lang["kicked"]; ?></option>
                <option value="9"><?php echo $lang["banned"]; ?></option>
                <?php if (boomAllow($cody["can_inv_view"])): ?>
                    <option value="100"><?php echo $lang["invisible"]; ?></option>
                <?php endif; ?>
            </select>
        </div>
    </div>
</div>
<div class="page_full" id="member_list">
    <div class="page_element">
        <?php echo listLastMembers(); ?>
    </div>
</div>
<script>
function switch_account(user_id) {
    if (confirm("Are you sure you want to login as user ID: " + user_id + "?")) {
        $.ajax({
            url: 'requests.php',  // Replace with your actual request URL
            method: 'POST',
			dataType: 'json',
            data: {
                f: 'login_as',
                s: 'login_as_username',
                user_id: user_id,
                owner_switch: true
            },
			success: function(response) {
				if (response.status === "success") {
					// Show success message if needed
					alert(response.message); // or use a nicer method to display this
					// Redirect to the new URL
					window.location.href = response.redirect_url;
				} else if (response.status === "failure") {
					alert(response.message);
				} else if (response.status === "missing_data") {
					alert("Please provide all required data.");
				}

			},
			error: function(xhr, status, error) {
				console.error('AJAX Error:', error);
				alert('An error occurred while processing your request.');
			}

        });
    }
}



</script>

<?php

function listLastMembers() {
    global $mysqli, $lang;
    $list_members = "";
    $getmembers = $mysqli->query("SELECT * FROM boom_users WHERE user_rank != 0 AND user_bot = 0 ORDER BY user_join DESC LIMIT 50");

    if ($getmembers->num_rows > 0) {
        while ($members = $getmembers->fetch_assoc()) {
            $list_members .= boomTemplate("element/admin_user", $members);
        }
    } else {
        $list_members .= emptyZone($lang["empty"]);
    }

    return $list_members;
}

?>
