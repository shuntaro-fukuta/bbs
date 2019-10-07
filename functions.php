<?php

function mb_trim($string) {
    return preg_replace('/\A[\p{Z}]+|[\p{Z}]+\z/u', '', $string);
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function validate($validations, $inputs) {
    $error_massages = [];

    foreach ($inputs as $attribute_name => $input) {
        $validation_lists = $validations[$attribute_name];

        foreach ($validation_lists as $validation_type => $condition) {
            $error_massage = null;

            switch ($validation_type) {
                case 'required':
                    if ($condition === true && empty($input)) {
                        $error_massage = "{$attribute_name}を入力してください";
                    }
                    break;
                case 'min_length':
                    if (mb_strlen($input) < $condition) {
                        $error_massage = "{$attribute_name}は{$condition}文字以上入力してください";
                    }
                    break;
                case 'max_length':
                    if (mb_strlen($input) > $condition) {
                        $error_massage = "{$attribute_name}は{$condition}文字以内で入力してください";
                    }
                    break;
            }

            if (isset($error_massage)) {
                $error_massages[] = $error_massage;
                break;
            }
        }
    }

    return $error_massages;
}