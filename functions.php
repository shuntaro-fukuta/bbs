<?php

function mb_trim($string) {
    return preg_replace('/\A[\p{Z}]+|[\p{Z}]+\z/u', '', $string);
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function is_empty($var) {
    return ($var === '' || $var === null || $var === []);
}

/*
function get_input($key, $array) {
    if (array_isset($key, $array)) {
        $input = trim($array[$key]);
        return ($input === '') ? null : $input;
    } else {
        return null;
    }
}

function get_inputs($keys, $array) {
    $inputs = [];
    foreach ($keys as $key) {
        $inputs[$key] = get_input($key, $array);
    }

    return $inputs;
}
*/
