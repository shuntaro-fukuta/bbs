<?php

function execute_validations($validation_settings, $inputs) {
    $error_massages = [];

    foreach ($validation_settings as $attribute_name => $settings) {
        $input = $inputs[$attribute_name];

        $error_massage = null;

        if (isset($settings['required'])) {
            if ($settings['required'] === true && empty($input)) {
                $error_massages[] = "{$attribute_name}を入力してください";

                continue;
            }
        }

        if (isset($settings['length'])) {
            $error_massage = validate_input_length($attribute_name, $input, $settings['length']);

            if ($error_massage) {
                $error_massages[] = $error_massage;

                continue;
            }
        }
    }

    return $error_massages;
}

function validate_input_length($attribute_name, $input, $length_limits) {
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
