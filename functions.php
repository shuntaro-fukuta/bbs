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

function get_trimmed_inputs($keys, $array) {
    $inputs = [];
    foreach ($keys as $key) {
        $inputs[$key] = isset($array[$key]) ? mb_trim($array[$key]) : '';
    }

    return $inputs;
}
