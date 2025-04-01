<?php


require __DIR__ . "./../config_session.php";

if (isset($_POST["delete_player"])) {
    echo deleteplayer();
    exit;
}
if (isset($_POST["player_url"]) && isset($_POST["player_alias"])) {
    echo staffaddplayer();
    exit;
}
if (isset($_POST["new_stream_url"]) && isset($_POST["new_stream_alias"]) && isset($_POST["player_id"])) {
    echo staffeditstream();
    exit;
}

function deletePlayer()
{
    global $mysqli;
    global $data;
    $delplay = escape($_POST["delete_player"]);
    if (!boomAllow(90)) {
        return 0;
    }
    $mysqli->query("UPDATE boom_rooms SET room_player_id = 0 WHERE room_player_id = '" . $delplay . "'");
    $mysqli->query("DELETE FROM boom_radio_stream WHERE id = '" . $delplay . "'");
    if ($delplay == $data["player_id"]) {
        $mysqli->query("UPDATE boom_setting SET player_id = 0 WHERE id = 1");
        return 2;
    }
    return 1;
}

function staffAddPlayer()
{
    global $mysqli;
    global $data;
    $player_url = escape($_POST["player_url"]);
    $player_alias = escape($_POST["player_alias"]);
    if (!boomAllow(90)) {
        return 0;
    }
    if ($player_url != "" && $player_alias != "") {
        $count_player = $mysqli->query("SELECT id FROM boom_radio_stream WHERE id > 0");
        $playcount = $count_player->num_rows;
        $mysqli->query("INSERT INTO boom_radio_stream (stream_url, stream_alias) VALUE ('" . $player_url . "', '" . $player_alias . "')");
        if ($playcount < 1) {
            $last_id = $mysqli->insert_id;
            $mysqli->query("UPDATE boom_setting SET player_id = '" . $last_id . "' WHERE id = 1");
        }
        return 1;
    }
    return 2;
}

function staffEditStream()
{
    global $mysqli;
    global $data;
    $id = escape($_POST["player_id"]);
    $alias = escape($_POST["new_stream_alias"]);
    $url = escape($_POST["new_stream_url"]);
    if (!boomAllow(90)) {
        return 0;
    }
    if (!empty($alias) && !empty($url)) {
        $mysqli->query("UPDATE boom_radio_stream SET stream_url = '" . $url . "', stream_alias = '" . $alias . "' WHERE id = '" . $id . "'");
        return 1;
    }
    return 0;
}

?>