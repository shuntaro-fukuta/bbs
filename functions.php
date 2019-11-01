<?php

function debug($var) {
    echo '<pre>';
        var_dump($var);
    echo '</pre>';
}

function mb_trim($string) {
    return preg_replace('/\A[\p{Z}]+|[\p{Z}]+\z/u', '', $string);
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function is_empty($var) {
    return ($var === null || $var === '' || $var === []);
}

// ebine
// これ微妙。inputs って何を表してる？
// ユーザー入力ですね。
// この関数って何やってる？
// 渡されたデータをトリムしてるだけだよね。
// 関数名が悪い。
// trim_values()
function get_trimmed_inputs($keys, $array) {
    $inputs = [];
    foreach ($keys as $key) {
        // ebine
        // 空文字列になるよね。
        // データがないときとか、スペースだけだったりするとき。
        // その時に、使う側はどうそれを扱いたいか？
        // 例えば、この関数をユーザー入力に対して使う場合、
        // ユーザーが入力しなかった。
        // じゃあそのとき、空文字列なのか、null なのか。
        $inputs[$key] = isset($array[$key]) ? mb_trim($array[$key]) : '';
    }

    return $inputs;
}
