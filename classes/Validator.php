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
                } elseif ($type === 'mime_types') {
                    $error_message = $this->validateMimetype($attribute_name, $input, $rule);
                } elseif ($type === 'file_size') {
                    $error_message = $this->validateFileSize($attribute_name, $input, $rule);
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

    private function validateRequired(string $name, ?string $input, bool $rule)
    {
        if ($rule === true && is_empty($input)) {
            return "{$name}を入力してください";
        }
    }

    private function validateLength(string $name, ?string $input, array $limits)
    {
        if ((isset($limits['min']) && $limits['min'] < 1) || (isset($limits['max']) && $limits['max'] < 2)) {
            throw new InvalidArgumentException('Validation rules of length must be greater than or equal to ( min->1, max->2 )');
        }

        if (is_empty($input)) {
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

    private function validateMimetype(string $name, ?array $uploaded_file, array $mime_types)
    {
        if (is_empty($uploaded_file)) {
            return null;
        }

        $tmp_name = $uploaded_file['tmp_name'];

        if ($tmp_name === '') {
            return null;
        }

        if (!file_exists($tmp_name)) {
            throw new RuntimeException("{$tmp_name} file doesn't exist.");
        }

        // TODO: $mime_typesのチェック

        if (!($mime_type = mime_content_type($tmp_name))) {
            throw new RuntimeException('Failed to get mime type from file');
        }

        if (!array_search($mime_type, $mime_types)) {
            return "{$name}の種類は" . implode(", ", array_keys($mime_types)) . 'のいずれかにしてください';
        }

        return null;
    }

    private function validateFileSize(string $name, ?array $uploaded_file, array $limits)
    {
        foreach ($limits as $category => $byte) {
            if (!in_array($category, ['min', 'max'])) {
                throw new LogicException('file_size validation category must be min or max');
            }

            if (($category === 'min' && $byte < 1) || ($category === 'max' && $byte < 2)) {
                throw new LogicException('file_size validation value is invalid.');
            }
        }

        if (is_empty($uploaded_file)) {
            return null;
        }

        $tmp_name = $uploaded_file['tmp_name'];

        if ($tmp_name === '') {
            return null;
        }

        $file_size = filesize($tmp_name);

        if ($file_size === false) {
            throw new RuntimeException('Failed to get filesize.');
        }

        if (isset($limits['min']) && isset($limits['max'])) {
            if ($file_size < $limits['min'] || $file_size > $limits['max']) {
                // hamaco: 自分がユーザーだったときのことを考えてください。
                //         例えば(1024x1024x8)バイト以下とか言われても辛くないですか？
                return "{$name}のサイズは{$limits['min']}バイト以上{$limits['max']}バイト以下のファイルにしてください";
            }
        } elseif (isset($limits['min']) && $file_size < $limits['min']) {
            return "{$name}のサイズは{$limits['min']}バイト以上にしてください";
        } elseif (isset($limits['max']) && $file_size > $limits['max']) {
            return "{$name}のサイズは{$limits['max']}バイト以下にしてください";
        }
    }

    private function validateDigit(string $name, ?string $input, int $digit)
    {
        if ($digit < 1) {
            throw new InvalidArgumentException('Validation rules of digit must be greater than or equal to 1');
        }

        if (is_empty($input)) {
            return;
        }

        if (!ctype_digit($input) || strlen($input) !== $digit) {
            return "{$name}は{$digit}桁の半角数字で入力してください";
        }
    }
}