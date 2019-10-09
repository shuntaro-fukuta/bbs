<?php

function execute_validations($validation_settings, $inputs) {
    $error_massages = [];

    foreach ($validation_settings as $attribute_name => $settings) {

        if (isset($inputs[$attribute_name])) {
            $input = $inputs[$attribute_name];
        } else {
            $input = null;
        }

        foreach ($settings as $validation_type => $condition) {

            switch ($validation_type) {
                case 'required':
                    $error_massage = validate_input_required($attribute_name, $input, $condition);

                    if (isset($error_massage)) {
                        $error_massages[] = $error_massage;
                        continue 3;
                    }

                    break;
                case 'length':
                    $error_massage = validate_input_length($attribute_name, $input, $condition);

                    if (isset($error_massage)) {
                        $error_massages[] = $error_massage;
                        continue 3;
                    }

                    break;
            }

        }
    }

    return $error_massages;
}

function validate_input_required($attribute_name, $input, $condition) {
    $error_massage = null;

    if ($condition === true && (empty($input) || is_null($input))) {
        $error_massage = "{$attribute_name}を入力してください";
    }

    return $error_massage;
}

function validate_input_length($attribute_name, $input, $length_limits) {
    if (is_null($input)) {
        return;
    }

    $input_length = mb_strlen($input);

    $error_massage = null;

    if (isset($length_limits['min']) && isset($length_limits['max'])) {
        if ($input_length < $length_limits['min'] || $input_length > $length_limits['max']) {
            $error_massage = "{$attribute_name}は{$length_limits['min']}文字以上{$length_limits['max']}文字以内で入力してください";
        }
    } elseif (isset($length_limits['min'])) {
        if ($input_length < $length_limits['min']) {
            $error_massage = "{$attribute_name}は{$length_limits['min']}文字以上入力してください";
        }
    } elseif (isset($length_limits['max'])) {
        if ($input_length > $length_limits['max']) {
            $error_massage = "{$attribute_name}は{$length_limits['max']}文字以内で入力してください";
        }
    }

    return $error_massage;
}
