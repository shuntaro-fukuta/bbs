<?php

class Controller_Admin_Authentication extends Controller_App
{
    public function login()
    {
        if ($this->isAdmin()) {
            $this->redirect('index.php');
        }

        if ($this->getEnv('request-method') === 'GET') {
            $this->render('admin/login.php');

            return;
        }

        $id       = $this->getParam('id');
        $password = $this->getParam('password');

        $member  = new Storage_Member();
        $account = $member->selectRecord(['*'], [
            'condition' => 'id = ?',
            'values'    => [$id],
        ]);

        $error_messages = [];
        if (is_null($account) || !password_verify($password, $account['password'])) {
            $error_messages[] = 'idまたはパスワードが間違っています。';
        }

        if (empty($error_messages)) {
            $this->session_manager->regenerateId();
            $this->session_manager->setVar('member_id', $account['id']);
            $this->redirect('index.php');
        }

        $this->render('admin/login.php', get_defined_vars());
    }

    public function logout()
    {
        $this->session_manager->destroy();
        $this->redirect('index.php');
    }
}
