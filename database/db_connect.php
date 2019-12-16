<?php

require_once(__DIR__ . '/../config/database.php');

function connect_mysqli(array $db_settings = null) {
    $host     = $db_settings['host']     ?? DB_HOST;
    $username = $db_settings['username'] ?? DB_USERNAME;
    $password = $db_settings['password'] ?? DB_PASSWORD;
    $db_name  = $db_settings['db_name']  ?? DB_NAME;
    $encoding = $db_settings['encoding'] ?? DB_ENCODING;

    $mysqli = new mysqli($host, $username, $password, $db_name);

    try {
        if ($mysqli->connect_error) {
            throw new Exception("DB接続エラー: {$mysqli->connect_error}");
        }
    } catch (Exception $e) {
        echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
        exit;
    }

    $mysqli->set_charset($encoding);

    return $mysqli;
}
