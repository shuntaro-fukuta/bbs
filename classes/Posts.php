<?php

require_once(dirname(__FILE__) . '/Table.php');

class Posts extends Table
{
    protected $table_name = 'posts';
    protected $bind_types = [
        'id'         => 'i',
        'title'      => 's',
        'comment'    => 's',
        'image'      => 's',
        'password'   => 's',
        'created_at' => 's',
    ];
}