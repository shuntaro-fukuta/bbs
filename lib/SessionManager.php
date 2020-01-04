<?php

class SessionManager
{
    public function __construct()
    {
        $this->sessionStart();
    }

    protected function sessionStart()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function getVar(string $key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }

    public function setVar(string $key, string $value)
    {
        $_SESSION[$key] = $value;
    }

    public function unsetVar(string $key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy()
    {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', 1);
        }
        session_destroy();
    }

    public function regenerateId(bool $delete_old_session = true)
    {
        session_regenerate_id($delete_old_session);
    }
}