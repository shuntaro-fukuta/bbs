<?php

class Controller_Member extends Controller_App
{
    public function register()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('index.php');
        }

        if ($this->getEnv('request-method') === 'GET') {
            $this->render('member/register/form.php');

            return;
        }

        $member    = new Storage_Member();
        $premember = new Storage_Premember();

        $name     = $this->getParam('name');
        $email    = $this->getParam('email');
        $password = $this->getParam('password');

        $inputs = [
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
        ];

        $error_messages = $premember->validate($inputs);
        if (empty($error_messages)) {
            if (!is_null($this->getParam('do_confirm'))) {
                $hidden_pass = str_repeat('*', strlen($inputs['password']));
                $this->render('member/register/confirm.php', get_defined_vars());

                return;
            }

            if (!is_null($this->getParam('do_register'))) {
                $inputs['password'] = password_hash($inputs['password'], PASSWORD_BCRYPT);

                $token           = uniqid(create_random_string(30), true);
                $inputs['token'] = $token;

                $record_count      = $premember->count(['condition' => 'email = ?', 'values' => [$email]]);
                $is_already_exists = ($record_count !== 0);
                if (!$is_already_exists) {
                    $premember->insert($inputs);
                } else {
                    $inputs['created_at'] = date('Y-m-d H:i:s');
                    $premember->update($inputs, [
                        'condition' => 'email = ?',
                        'values'    => [$email],
                    ]);
                }

                $to      = $email;
                $subject = 'アカウント登録はまだ完了しておりません';
                $url     = 'http://centos7-amp7/authenticate.php?token=' . $token;
                $message = 'Hi, Mr.' . $name
                            . PHP_EOL . 'アカウント登録を完了するために、２４時間以内に以下のリンクをクリックしてください。'
                            . PHP_EOL . $url;
                $header  = 'From:BBS@example.com';
                mb_send_mail($to, $subject, $message, $header);

                $this->render('member/register/sent_email.php');

                return;
            }
        }

        $this->render('member/register/form.php', get_defined_vars());
    }

    public function authenticate()
    {
        if ($this->getEnv('request-method') !== 'GET') {
            $this->err400();
        }

        if ($this->isLoggedIn()) {
            $this->redirect('index.php');
        }

        $member    = new Storage_Member();
        $premember = new Storage_Premember();

        $token = $this->getParam('token');
        if (is_null($token)) {
            $this->err400();
        }

        $error_messages = [];

        $account = $premember->selectRecord(['*'], [
            'condition' => 'token = ?',
            'values'    => [$token],
        ]);
        if (is_null($account)) {
            $error_messages[] = '会員登録に失敗しました。' . PHP_EOL . 'もう一度登録し直してください。';
        } elseif ($member->count(['condition'=> 'email = ?', 'values' => [$account['email']]]) !== 0) {
            $error_messages[] = '既に会員登録されています。';
        } elseif ($premember->isExpired($account['created_at'])) {
            $error_messages[] = 'アカウント認証URLの有効期限が切れました。' . PHP_EOL . 'もう一度登録し直してください。';
        }

        if (!empty($error_messages)) {
            $this->render('member/register/form.php', get_defined_vars());

            return;
        }

        $member->insert([
            'name'     => $account['name'],
            'email'    => $account['email'],
            'password' => $account['password'],
        ]);
        $premember->delete([
            'condition' => 'id = ?',
            'values'    => [$account['id']],
        ]);

        $member_id = $member->selectRecord(['id'], [
            'condition' => 'email = ?',
            'values'    => [$account['email']],
        ])['id'];

        $this->session_manager->regenerateId();
        $this->session_manager->setVar('member_id', $member_id);

        $this->render('member/register/complete.php');
    }

    public function login()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('index.php');
        }

        if ($this->getEnv('request-method') === 'POST') {
            $email    = $this->getParam('email');
            $password = $this->getParam('password');

            $member  = new Storage_Member();
            $account = $member->selectRecord(['*'], [
                'condition' => 'email = ?',
                'values'    => [$email],
            ]);

            $error_messages = [];
            if (is_null($account) || !password_verify($password, $account['password'])) {
                $error_messages[] = '入力されたメールアドレスとパスワードに一致するアカウントが見つかりません。';
            } else {
                $this->session_manager->regenerateId();
                $this->session_manager->setVar('member_id', $account['id']);
                $this->redirect('index.php');
            }
        }

        $this->render('member/login/form.php', get_defined_vars());
    }

    public function logout()
    {
        $this->session_manager->unsetVar('member_id');
        $this->redirect('index.php');
    }
}