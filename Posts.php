<?php

require_once('Table.php');

class Posts extends Table
{
    protected $db_instance;
    protected $table_name = 'posts';
    protected $bind_types = [
        'id'         => 'i',
        'title'      => 's',
        'comment'    => 's',
        'password'   => 's',
        'created_at' => 's',
    ];

    public function __construct(mysqli $db_instance)
    {
        $this->db_instance = $db_instance;
    }
}