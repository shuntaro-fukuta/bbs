<?php

$host     = 'localhost';
$username = 'root';
$password = 'root';
$db_name  = 'bbs';
$encoding = 'UTF-8';

$mysqli = new mysqli($host, $username, $password, $db_name);


if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->set_charset($encoding);