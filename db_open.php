<?php
// $dbserver = "localhost";
$dbserver = "mysql220.phy.lolipop.lan";
$dbname = "LAA1570395-naga";
$dbuser = "LAA1570395";
$dbpasswd = "Ryo130626";
$opt = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
];
$dbh = new PDO('mysql:host=' . $dbserver . ';dbname='.$dbname,
$dbuser, $dbpasswd, $opt );