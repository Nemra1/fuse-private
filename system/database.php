<?php
// optional base domain
define('BOOM_DOMAIN', '');
// you can edit these lines to configure new setting for your chat
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "v6";
$db = new MysqliDb(
    Array(
        'host' => $DB_HOST,
        'username' => $DB_USER,
        'password' => $DB_PASS,
        'db' => $DB_NAME,
        'port' => '3306',
        'prefix' => 'boom_', // your prefix
        'charset' => 'utf8mb4'
    )
);
// Please do not modify this line post installation
$encryption = "eO0B3bae7adbM9551ae7O8-cfa-811116-3dd97";
$check_install = 1;
global $db;

?>