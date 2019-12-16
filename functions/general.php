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

    // ebine
    // ここの実装、ださいよね。
    // もうちょいキレイに実装しましょう。

    if ($byte < 1024) {
        return $byte . 'B';
    }

    if ($byte < 1024 * 1024) {
        $kilobyte = round($byte / 1024, 1);

        return  $kilobyte . 'KB';
    }

    if ($byte < 1024 * 1024 * 1024) {
        $megabyte = round($byte / (1024 * 1024), 1);

        return $megabyte . 'MB';
    }

    if ($byte < (1024 * 1024 * 1024 * 1024)) {
        $gigabyte = round($byte / (1024 * 1024 * 1024), 1);

        return $gigabyte . 'GB';
    }

    $terabyte = round($byte / (1024 * 1024 * 1024 * 1024), 1);

    return $terabyte . 'TB';
}

function get_file(string $name) {
    if (isset($_FILES[$name]) && is_uploaded_file($_FILES[$name]['tmp_name'])) {
        return $_FILES[$name];
    }

    return null;
}
