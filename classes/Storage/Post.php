<?php

class Storage_Post extends Storage_Base
{
    protected $table_name = 'post';

    protected $validation_rule = [
        'name'  => [
            'required' => false,
            'length'   => [
                'min' => '3',
                'max' => '16',
            ],
        ],
        'title' => [
            'required' => true,
            'length'   => [
                'min' => 10,
                'max' => 32,
            ],
        ],
        'comment' => [
            'required' => true,
            'length'   => [
                'min' => 10,
                'max' => 200,
            ],
        ],
        'image_file' => [
            'required'   => false,
            'mime_types' => [
                'jpeg' => 'image/jpeg',
                'jpg'  => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
            ],
            'file_size' => [
                'max' => 1024 * 1024,
            ],
        ],
        'password' => [
            'required' => false,
            'digit'    => 4,
        ],
    ];

    public function validate(array $inputs)
    {
        $validator = new Validator();
        $validator->setAttributeValidationRules($this->validation_rule);

        return $validator->validate($inputs);
    }
}