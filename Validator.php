<?php

// ebine
// クラスファイルはクラス名に合わせて、Validator.php にすること。

class Validator
{
    private $attribute_validation_rules;

    public function validate(array $inputs)
    {
        $error_messages = [];

        foreach ($this->attribute_validation_rules as $attribute_name => $validation_rules) {
            $input = $inputs[$attribute_name] ?? null;

            // ebine
            // length のエラーメッセージは入力ないときいらないよね

            foreach ($validation_rules as $type => $rule) {
                $error_message = null;

                if ($type === 'required') {
                    $error_message = $this->validate_required($attribute_name, $input, $rule);
                } elseif ($type === 'length') {
                    $error_message = $this->validate_length($attribute_name, $input, $rule);
                } elseif ($type === 'digit') {
                    $error_message = $this->validate_digit($attribute_name, $input, $rule);
                }

                if (isset($error_message)) {
                    $error_messages[] = $error_message;
                }
            }
        }

        return $error_messages;
    }

    // ebine
    // コーディング規約違反
    // setAttributeValidationRules() にするように。
    // 上に書いて。
    // 原則、プロパティの値をセットする、ゲットする、は上の方に書くように。
    public function set_attribute_validation_rules(array $attribute_validation_rules)
    {
        $this->attribute_validation_rules = $attribute_validation_rules;
    }

    private function validate_required(string $attribute_name, string $input, bool $rule)
    {
        if ($rule === true && empty($input)) {
            return "{$attribute_name}を入力してください";
        }
    }

    // ebine
    // 変数名がくどい
    private function validate_length(string $attribute_name, string $input, $length_limits)
    {
        if ($this->attribute_validation_rules[$attribute_name]['required'] === false && empty($input)) {
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

    private function validate_digit(string $attribute_name, string $input, int $digit)
    {
        if ($this->attribute_validation_rules[$attribute_name]['required'] === false && empty($input)) {
            return;
        }

        if (!is_numeric($input) || strlen($input) !== $digit) {
            return "{$attribute_name}は{$digit}桁の半角数字で入力してください";
        }
    }
}