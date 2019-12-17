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

function trim_values(array $keys, array $values) {
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

function convert_byte_unit(int $byte) {
    if ($byte < 0) {
        throw new InvalidArgumentException('Byte must be greater than or equal to 0');
    }

    $unit_min_bytes = [
        'TB' => pow(1024, 4),
        'GB' => pow(1024, 3),
        'MB' => pow(1024, 2),
        'KB' => pow(1024, 1),
        'B'  => pow(1024, 0),
    ];

    foreach ($unit_min_bytes as $unit => $min_byte) {
        if ($byte >= $min_byte) {
            return round($byte / $min_byte) . $unit;
        }
    }
}

function get_file(string $name) {
    if (isset($_FILES[$name]) && is_uploaded_file($_FILES[$name]['tmp_name'])) {
        return $_FILES[$name];
    }

    return null;
}

function create_random_string(int $length = 10){
    if ($length < 1) {
        throw new InvalidArgumentException('Length must be greater than or equal to 1.');
    }

    $character_list  = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
    $character_count = count($character_list);

    $string = '';
    for ($i = 1; $i <= $length; $i++) {
        $string .= $character_list[mt_rand(0, $character_count - 1)];
    }

    return $string;
}
