<?php


require __DIR__ . "./../config_session.php";
if (isset($_POST["version_install"]) && boomAllow(100)) {
    $version = escape($_POST["version_install"]);
    echo boomupdatechat($version);
    exit;
}
exit;

function boomUpdateChat($v)
{
    global $mysqli;
    global $data;
    global $cody;

    if ($v <= $data["version"]) {
        return boomCode(0, ["error" => "Version is already installed"]);
    }
    $install = ["key" => $data["boom"], "domain" => $data["domain"]];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $install);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
    $result = curl_exec($curl);
    curl_close($curl);
    if (!isBoomJson($result)) {
        return boomCode(0, ["error" => "Unable to install the update at this time please contact us for support."]);
    }
    $udata = json_decode($result);
    if ($udata->code != 99) {
        return boomCode(0, ["error" => $udata->error]);
    }
    $fpath = BOOM_PATH . "/updates/" . $v . "/files.zip";
    $upath = BOOM_PATH . "/updates/" . $v . "/update.php";
    $epath = BOOM_PATH . "/";
    if (file_exists($fpath)) {
        $zip = new ZipArchive();
        if ($zip->open($fpath) !== true) {
            return boomCode(0, ["error" => "unable to process automatic update please refer to manual update procedure or contact us for support."]);
        }
        $zip->extractTo($epath);
        $zip->close();
    }
    if (file_exists($upath)) {
        require $upath;
    }
    return boomCode(2);
}

?>