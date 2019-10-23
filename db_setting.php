<?php

// ebine
// これだめで、これだと、変数がグローバルになっちゃうので、汚染されよね。
// こういうときは、定数として定義しましょう。
// define('DB_HOST', 'localhost');
// 今どきの書き方 const DB_HOST = 'localhost';

$db_host     = 'localhost';
$db_username = 'root';
$db_password = 'root';
$db_name     = 'bbs';
$db_encoding = 'UTF-8';