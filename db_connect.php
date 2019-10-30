<?php

require_once('db_config.php');

function connect_mysqli(array $db_settings = null) {
    $host     = isset($db_settings['host']) ? $db_settings['host'] : DB_HOST;
    $username = isset($db_settings['username']) ? $db_settings['username'] : DB_USERNAME;
    $password = isset($db_settings['password']) ? $db_settings['password'] : DB_PASSWORD;
    $db_name  = isset($db_settings['db_name']) ? $db_settings['db_name'] : DB_NAME;
    $encoding = isset($db_settings['encoding']) ? $db_settings['encoding'] : DB_ENCODING;

    try {
        $mysqli = new mysqli($host, $username, $password, $db_name);

        if ($mysqli->connect_error) {
            throw new Exception("DB接続エラー: {$mysqli->connect_error}");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }

    $mysqli->set_charset($encoding);

    return $mysqli;
}
