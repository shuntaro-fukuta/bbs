<?php

abstract class Controller_App extends Controller_Base
{
    protected $session_manager;

    protected $member;

    public function __construct()
    {
        $this->session_manager = new SessionManager();
        $this->loadMember();
    }

    public function getMemberId()
    {
        if (!is_null($this->member)) {
            return $this->member['id'];
        }
    }

    public function getMemberName()
    {
        if (!is_null($this->member)) {
            return $this->member['name'];
        }
    }

    protected function isLoggedIn()
    {
        return ($this->member !== null);
    }

    protected function loadMember()
    {
        $member_id = $this->session_manager->getVar('member_id');

        if (!is_null($member_id)) {
            $member       = new Storage_Member();
            $this->member = $member->selectRecord(['*'], [['id', '=', $member_id]]);
        }
    }
}