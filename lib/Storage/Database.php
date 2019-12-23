<?php

abstract class Storage_Database
{
    protected $conn = null;

    protected $config = array(
        'host'     => '127.0.0.1',
        'port'     => '',
        'name'     => '',
        'user'     => '',
        'password' => '',
    );

    abstract public function connect();

    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);

        if (function_exists('get_db_config')) {
            $this->config = array_merge($this->config, get_db_config());
        }

        $this->conn = $this->connect();
    }
}
