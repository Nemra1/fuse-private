<?php
require __DIR__ . "../../../config_session.php";

// Check if the user has the required permission (level 9)
if (!boomAllow(80)) {
    exit;
}

// Display the title of the page
echo elementTitle($lang["ban_management"]);

echo '
<div class="page_full">
    <div class="page_element">
        <div id="ip_search">
            <div class="search_bar">
                <input id="search_ip" placeholder="🧿" class="full_input" type="text" />
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="page_element">
        <div id="ip_list">';
        
// Display the list of banned IPs
echo listAdminIp();

echo '
        </div>
    </div>
</div>';

/**
 * Fetches and returns the list of banned IPs
 * 
 * @return string
 */
function listAdminIp()
{
    global $mysqli;
    global $lang;
    
    $list_ip = "";
    $getip = $mysqli->query("SELECT * FROM boom_banned ORDER BY ip ASC");
    
    if ($getip->num_rows > 0) {
        while ($ip = $getip->fetch_assoc()) {
            // Use the boomTemplate function to generate HTML for each IP
            $list_ip .= boomTemplate("element/admin_ip", $ip);
        }
    } else {
        // Display a message if no banned IPs are found
        $list_ip .= emptyZone($lang["empty"]);
    }
    
    return $list_ip;
}
?>
