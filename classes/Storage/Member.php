<?php

class Storage_Member extends Storage_Base
{
    protected $table_name = 'member';

    protected $validation_rules = [
        'name' => [
            'required' => true,
            'length'   => [
                'min' => 3,
                'max' => 16,
            ],
        ],
        'email' => [
            'required' => 'true',
        ],
        'password' => [
            'required' => true,
            'length'   => [
                'min'    => 8,
                'length' => 16,
            ],
        ],
    ];

    public function validate(array $values)
    {
        $error_messages = [];

        $validator = new Validator();
        // TODO: メソッド名変える
        $validator->setAttributeValidationRules($this->validation_rules);
        $error_messages = $validator->validate($values);

        if (isset($values['email']) && !empty($values['email'])) {
            $email = $values['email'];

            if ($this->count([['email', '=', $email]]) === 1) {
                $error_messages[] = 'このメールアドレスは既に使用されています。';
            } elseif (preg_match('/@/', $email) !== 1) {
                // TODO: 正規表現
                $error_messages[] = 'メールアドレスに＠が含まれていません。';
            }
        }

        return $error_messages;
    }
}