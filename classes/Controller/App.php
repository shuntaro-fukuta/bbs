<?php

abstract class Controller_App extends Controller_Base
{
    protected $session_manager;

    protected $member = [];

    public function __construct()
    {
        $this->session_manager = new SessionManager();
        $this->setMember();
    }

    protected function isLoggedIn()
    {
        return ($this->session_manager->getVar('member_id') !== null);
    }

    protected function setMember()
    {
        if ($this->isLoggedIn()) {
            $member_id = $this->session_manager->getVar('member_id');

            $member       = new Storage_Member();
            $this->member = $member->selectRecord(['*'], [['id', '=', $member_id]]);
        }
    }
}