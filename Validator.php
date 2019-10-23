<?php

class Validator
{
    private $attribute_validation_rules;

    public function setAttributeValidationRules(array $rules)
    {
        $this->attribute_validation_rules = $rules;
    }

    public function validate(array $inputs)
    {
        $error_messages = [];

        foreach ($this->attribute_validation_rules as $attribute_name => $validation_rules) {
            $input = $inputs[$attribute_name] ?? null;

            foreach ($validation_rules as $type => $rule) {
                $error_message = null;

                if ($type === 'required') {
                    $error_message = $this->validateRequired($attribute_name, $input, $rule);
                } elseif ($type === 'length') {
                    $error_message = $this->validateLength($attribute_name, $input, $rule);
                } elseif ($type === 'digit') {
                    $error_message = $this->validateDigit($attribute_name, $input, $rule);
                }

                if (isset($error_message)) {
                    $error_messages[] = $error_message;
                }
            }
        }

        return $error_messages;
    }

    private function validateRequired(string $name, string $input, bool $rule)
    {
        if ($rule === true && empty($input)) {
            return "{$name}を入力してください";
        }
    }

    private function validateLength(string $name, string $input, $limits)
    {
        if (empty($input)) {
            return;
        }

        $input_length = mb_strlen($input);

        if (isset($limits['min']) && isset($limits['max'])) {
            if ($input_length < $limits['min'] || $input_length > $limits['max']) {
                return "{$name}は{$limits['min']}文字以上{$limits['max']}文字以内で入力してください";
            }
        } elseif (isset($limits['min'])) {
            if ($input_length < $limits['min']) {
                return "{$name}は{$limits['min']}文字以上入力してください";
            }
        } elseif (isset($limits['max'])) {
            if ($input_length > $limits['max']) {
                return "{$name}は{$limits['max']}文字以内で入力してください";
            }
        }
    }

    private function validateDigit(string $name, string $input, int $digit)
    {
        if (empty($input)) {
            return;
        }

        if (!is_numeric($input) || strlen($input) !== $digit) {
            return "{$name}は{$digit}桁の半角数字で入力してください";
        }
    }
}