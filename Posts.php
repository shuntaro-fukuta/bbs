<?php

require_once('Table.php');

class Posts extends Table
{
    protected $table_name = 'posts';
    protected $bind_types = [
        'id'         => 'i',
        'title'      => 's',
        'comment'    => 's',
        'password'   => 's',
        'created_at' => 's',
    ];
}