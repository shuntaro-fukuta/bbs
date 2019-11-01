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

function trim_values($keys, $values) {
    $trimmed_values = [];

    foreach ($keys as $key) {
        if (isset($values[$key])) {
            $value = mb_trim($values[$key]);

            if ($value === '') {
                $value = null;
            }
        } else {
            $value = null;
        }

        $trimmed_values[$key] = $value;
    }

    return $trimmed_values;
}
