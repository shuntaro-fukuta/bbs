<?php

// ebine
// ページャーで、クラスを作ったので、
// これもクラスにしてみましょう。

function execute_validations($validation_settings, $inputs) {
    $error_messages = [];

    foreach ($validation_settings as $attribute_name => $settings) {
        $input = $inputs[$attribute_name] ?? null;

        // ebine
        // これだと1つの入力に対して、1つのエラーメッセージしか出ない。
        // 中には、複数のrルールがあって、2つ以上のルール違反が発生する可能性もある。
        // その場合に、これだとユーザビリティが悪いので、実装を見直してみましょう。

        foreach ($settings as $validation_type => $condition) {
            if ($validation_type === 'required') {
                $error_message = validate_input_required($attribute_name, $input, $condition);

                if (isset($error_message)) {
                    $error_messages[] = $error_message;
                    continue 2;
                }
            } elseif ($validation_type === 'length') {
                $error_message = validate_input_length($attribute_name, $input, $condition);

                if (isset($error_message)) {
                    $error_messages[] = $error_message;
                    continue 2;
                }
            } elseif ($validation_type === 'digit') {
                $error_message = validate_digit($attribute_name, $input, $condition);

                if (isset($error_message)) {
                    $error_messages[] = $error_message;
                    continue 2;
                }
            }
        }
    }

    return $error_messages;
}

function validate_input_required($attribute_name, $input, $condition) {
    if ($condition === true && (empty($input))) {
        return "{$attribute_name}を入力してください";
    }
}

function validate_input_length($attribute_name, $input, $length_limits) {
    if (empty($input)) {
        return;
    }

    $input_length = mb_strlen($input);

    if (isset($length_limits['min']) && isset($length_limits['max'])) {
        if ($input_length < $length_limits['min'] || $input_length > $length_limits['max']) {
            return "{$attribute_name}は{$length_limits['min']}文字以上{$length_limits['max']}文字以内で入力してください";
        }
    } elseif (isset($length_limits['min'])) {
        if ($input_length < $length_limits['min']) {
            return "{$attribute_name}は{$length_limits['min']}文字以上入力してください";
        }
    } elseif (isset($length_limits['max'])) {
        if ($input_length > $length_limits['max']) {
            return "{$attribute_name}は{$length_limits['max']}文字以内で入力してください";
        }
    }
}

function validate_digit($attribute_name, $input, $digit) {
    if (empty($input)) {
        return;
    }

    if (!is_numeric($input) || (strlen($input) !== $digit)) {
        return "{$attribute_name}は{$digit}桁の半角数字で入力してください";
    }
}
