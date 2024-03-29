<?php

function debug() {
    echo '<pre style="background: #fff; color: #333; ' .
    'border: 1px solid #ccc; margin: 5px; padding: 10px;">';

    foreach (func_get_args() as $value) {
        var_dump($value);
    }

    echo '</pre>';
}

function mb_trim($string) {
    return preg_replace('/\A[\p{Z}]+|[\p{Z}]+\z/u', '', $string);
}

function h($string, $flags = null, $encoding = null) {
    if (empty($flags)) {
        $flags = ENT_QUOTES;
    }

    if (empty($encoding)) {
        $encoding = 'UTF-8';
    }

    return htmlspecialchars($string, $flags, $encoding);
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

function create_random_string(int $length = 10) {
    $charas   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $char_len = strlen($charas);

    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $charas[mt_rand(0, $char_len - 1)];
    }

    return $string;
}

function add_include_path($path, $prepend = false)
{
    $current = get_include_path();

    if ($prepend) {
    set_include_path($path . PATH_SEPARATOR . $current);
    } else {
    set_include_path($current . PATH_SEPARATOR . $path);
    }
}

function get_db_config()
{
    $config = array();

    $keys = array('HOST', 'NAME', 'USER', 'PASSWORD');
    foreach ($keys as $key) {
        if (defined('DATABASE_' . $key)) {
            $config[strtolower($key)] = constant('DATABASE_' . $key);
        } else {
            throw new Exception(__FUNCTION__ . "() DATABASE_{$key} is not defined.");
        }
    }

    return $config;
}