<?php

function connect_mysqli(array $db_settings = null) {
    $host     = $db_settings['host']     ?? DATABASE_HOST;
    $username = $db_settings['username'] ?? DATABASE_USER;
    $password = $db_settings['password'] ?? DATABASE_PASSWORD;
    $db_name  = $db_settings['db_name']  ?? DATABASE_NAME;
    $encoding = $db_settings['encoding'] ?? DATABASE_ENCODING;

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
