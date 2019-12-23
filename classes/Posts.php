<?php

class Posts extends Table
{
    protected $table_name = 'posts';

    protected $bind_types = [
        'id'         => 'i',
        'title'      => 's',
        'comment'    => 's',
        'image_path' => 's',
        'password'   => 's',
        'created_at' => 's',
    ];

    protected $validation_rule = [
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
}