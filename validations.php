<?php

function execute_validations($validation_settings, $inputs) {
    $error_messages = [];

    foreach ($validation_settings as $attribute_name => $settings) {

        // null合体演算子に
        if (isset($inputs[$attribute_name])) {
            $input = $inputs[$attribute_name];
        } else {
            $input = null;
        }

        foreach ($settings as $validation_type => $condition) {

            switch ($validation_type) {
                case 'required':
                    $error_message = validate_input_required($attribute_name, $input, $condition);

                    if (isset($error_message)) {
                        $error_messages[] = $error_message;
                        continue 3;
                    }

                    break;
                case 'length':
                    $error_message = validate_input_length($attribute_name, $input, $condition);

                    if (isset($error_message)) {
                        $error_messages[] = $error_message;
                        continue 3;
                    }

                    break;
                case 'digit':
                    $error_message = validate_digit($attribute_name, $input, $condition);

                    if (isset($error_message)) {
                        $error_messages[] = $error_message;
                        continue 3;
                    }
            }

        }
    }

    return $error_messages;
}

function validate_input_required($attribute_name, $input, $condition) {
    $error_message = null;

    if ($condition === true && (empty($input))) {
        $error_message = "{$attribute_name}を入力してください";
    }

    return $error_message;
}

function validate_input_length($attribute_name, $input, $length_limits) {
    if (empty($input)) {
        return;
    }

    $input_length = mb_strlen($input);

    $error_message = null;

    if (isset($length_limits['min']) && isset($length_limits['max'])) {
        if ($input_length < $length_limits['min'] || $input_length > $length_limits['max']) {
            $error_message = "{$attribute_name}は{$length_limits['min']}文字以上{$length_limits['max']}文字以内で入力してください";
        }
    } elseif (isset($length_limits['min'])) {
        if ($input_length < $length_limits['min']) {
            $error_message = "{$attribute_name}は{$length_limits['min']}文字以上入力してください";
        }
    } elseif (isset($length_limits['max'])) {
        if ($input_length > $length_limits['max']) {
            $error_message = "{$attribute_name}は{$length_limits['max']}文字以内で入力してください";
        }
    }

    return $error_message;
}

function validate_digit($attribute_name, $input, $digit) {
    if (empty($input)) {
        return;
    }

    if (!is_numeric($input) || (strlen($input) !== $digit)) {
        return "{$attribute_name}は{$digit}桁の半角数字で入力してください";
    }

    return;
}
