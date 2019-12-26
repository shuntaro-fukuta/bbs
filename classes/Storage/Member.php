<?php

class Storage_Member extends Storage_Base
{
    protected $table_name = 'member';

    protected $register_validation_rules = [
        'name' => [
            'required' => true,
            'length'   => [
                'min' => 3,
                'max' => 16,
            ],
        ],
        'email' => [
            'required' => true,
        ],
        'password' => [
            'required' => true,
            'length'   => [
                'min'    => 8,
                'length' => 16,
            ],
        ],
    ];

    protected $login_validation_rules = [
        'email' => [
            'required' => true,
        ],
        'password' => [
            'required' => true,
            'length'   => [
                'min' => 8,
                'max' => 16,
            ],
        ],
    ];

    public function registerValidate(array $inputs)
    {
        $validator      = new Validator();
        $validator->setAttributeValidationRules($this->register_validation_rules);
        $error_messages = $validator->validate($inputs);

        if (isset($inputs['email']) && !empty($inputs['email'])) {
            $email = $inputs['email'];

            if ($this->count([['email', '=', $email]]) === 1) {
                $error_messages[] = 'このメールアドレスは既に使用されています。';
            } elseif (preg_match('/@/', $email) !== 1) {
                // TODO: 正規表現
                $error_messages[] = 'メールアドレスに＠が含まれていません。';
            }
        }

        return $error_messages;
    }

    public function loginValidate(array $inputs)
    {
        $validator      = new Validator();
        $validator->setAttributeValidationRules($this->login_validation_rules);
        $error_messages = $validator->validate($inputs);

        return $error_messages;
    }
}