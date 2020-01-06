<?php

abstract class Controller_App extends Controller_Base
{
    protected $session_manager;

    public function __construct()
    {
        $this->session_manager = new SessionManager();
    }

    protected function isLoggedIn()
    {
        return ($this->session_manager->getVar('member_id') !== null);
    }
}