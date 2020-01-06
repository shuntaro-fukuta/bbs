<?php

class Storage_Premember extends Storage_Base
{
    protected $table_name = 'premember';

    protected $validation_rules = [
        'name' => [
            'required' => true,
            'length'   => [
                'min' => 3,
                'max' => 16,
            ],
        ],
        'email' => [
            'required' => true,
            'email'    => true,
        ],
        'password' => [
            'required' => true,
            'length'   => [
                'min'    => 8,
                'length' => 16,
            ],
        ],
    ];

    public function validate(array $inputs)
    {
        $validator      = new Validator();
        $validator->setAttributeValidationRules($this->validation_rules);

        return $validator->validate($inputs);
    }

    public function isExpired(string $datetime)
    {
        $registration_timestamp = strtotime($datetime);
        if ($registration_timestamp === false) {
            throw new LogicException("Invalid date '{$datetime}' passed.");
        }

        if ((time() - $registration_timestamp) > 60 * 60  * 24) {
            return true;
        }

        return false;
    }
}