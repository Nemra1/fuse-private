<?php


require __DIR__ . "./../config_session.php";
if (isset($_POST["search_member"])) {
    echo staffsearchmember();
    exit;
}
if (isset($_POST["search_critera"])) {
    echo staffsearchcritera();
    exit;
}
if (isset($_POST["more_search_critera"]) && isset($_POST["last_critera"])) {
    echo staffmorecritera();
}

function staffSearchMember()
{
    global $mysqli;
    global $data;
    global $lang;
    $target = cleanSearch(escape($_POST["search_member"]));
    $list_members = "";
    if (!boomAllow(70)) {
        return "";
    }
    if (filter_var($target, FILTER_VALIDATE_EMAIL)) {
        $getmembers = $mysqli->query("SELECT * FROM boom_users WHERE user_email = '" . $target . "' ORDER BY user_name ASC LIMIT 500");
    } else {
        if (filter_var($target, FILTER_VALIDATE_IP)) {
            $getmembers = $mysqli->query("SELECT * FROM boom_users WHERE user_ip = '" . $target . "' ORDER BY user_name ASC LIMIT 500");
        } else {
            $getmembers = $mysqli->query("SELECT * FROM boom_users WHERE user_name LIKE '" . $target . "%' OR user_ip LIKE '" . $target . "%' ORDER BY user_name ASC LIMIT 500");
        }
    }
    if (0 < $getmembers->num_rows) {
        while ($members = $getmembers->fetch_assoc()) {
            $list_members .= boomTemplate("element/admin_user", $members);
        }
    } else {
        $list_members .= emptyZone($lang["empty"]);
    }
    return "<div class=\"page_element\">" . $list_members . "</div>";
}

function staffMoreCritera()
{
    global $mysqli;
    global $data;
    global $cody;
    $target = escape($_POST["more_search_critera"]);
    $last = escape($_POST["last_critera"]);
    if (!boomAllow(70)) {
        return "";
    }
    if ($target == 100 && !canViewInvisible()) {
        return "";
    }
    $list_members = "";
    $critera = getCritera($target);
    $getmembers = $mysqli->query("SELECT * FROM boom_users WHERE " . $critera . " AND user_id > '" . $last . "' ORDER BY user_id ASC LIMIT 50");
    if (0 < $getmembers->num_rows) {
        while ($members = $getmembers->fetch_assoc()) {
            $list_members .= boomTemplate("element/admin_user", $members);
        }
        return $list_members;
    }
    return 0;
}

function staffSearchCritera()
{
    global $mysqli;
    global $data;
    global $lang;
    $target = escape($_POST["search_critera"]);
    if (!boomAllow(70)) {
        return "";
    }
    if ($target == 100 && !canViewInvisible()) {
        return "";
    }
    $list_members = "";
    $count = 0;
    $critera = getCritera($target);
    $getmembers = $mysqli->query("SELECT * FROM boom_users WHERE " . $critera . " ORDER BY user_id ASC LIMIT 50");
    if (0 < $getmembers->num_rows) {
        while ($members = $getmembers->fetch_assoc()) {
            $list_members .= boomTemplate("element/admin_user", $members);
        }
        $get_count = $mysqli->query("SELECT user_id FROM boom_users WHERE " . $critera);
        $count = $get_count->num_rows;
    } else {
        $list_members .= emptyZone($lang["empty"]);
    }
    $list = "<div id=\"search_admin_list\" class=\"page_element\">" . $list_members . "</div>";
    if (50 < $count) {
        $list .= "<div id=\"search_for_more\" class=\"page_element\"><button onclick=\"moreAdminSearch(" . $target . ");\" class=\"default_btn full_button reg_button\">" . $lang["load_more"] . "</button></div>";
    }
    return $list;
}

?>