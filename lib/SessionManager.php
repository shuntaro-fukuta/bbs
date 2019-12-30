<?php

class SessionManager
{
    public function __construct()
    {
        $this->sessionStart();
    }

    public function sessionStart()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function getParam(string $key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }

    public function setParam(string $key, string $value)
    {
        $_SESSION[$key] = $value;
    }

    public function unsetParam(string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroyParam()
    {
        $_SESSION = [];
    }
}