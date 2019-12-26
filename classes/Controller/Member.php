<?php

class Controller_Member  extends Controller_Base
{
    public function register()
    {
        // クリックジャッキング
        // csrf
        $request_method = $this->getEnv('request-method');

        if ($request_method === 'GET') {
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

        $error_messages = $member->validate($inputs);
        if (empty($error_messages)) {
            if ($this->getParam('do_confirm') === '1') {
                $hidden_pass = str_repeat('*', strlen($inputs['password']));
                $this->render('member/register/confirm.php', get_defined_vars());
                return;
            }

            if ($this->getParam('do_register') === '1') {
                $inputs['password'] = password_hash($inputs['password'], PASSWORD_BCRYPT);

                $token           = uniqid(create_random_string(30), true);
                $inputs['token'] = $token;

                $premember->insert($inputs);

                $to      = $email;
                $subject = 'アカウント登録はまだ完了しておりません';
                $url     = 'http://centos7-amp7/authenticate.php?token=' . $token;
                $message = 'Hi, Mr.' . $name
                            . PHP_EOL . 'アカウント登録を完了するために、２４時間以内に以下のリンクをクリックしてください。'
                            . PHP_EOL . $url;
                $header  = 'From:BBS';
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

        $member    = new Storage_Member();
        $premember = new Storage_Premember();

        $token = $this->getParam('token');
        if (is_null($token)) {
            $this->err400();
        }

        $account = $premember->selectRecord(['*'], [['token', '=', $token]]);
        if (empty($account)) {
            $error_messages = ['会員登録に失敗しました。' . PHP_EOL . 'もう一度登録し直してください。'];
            $this->render('member/register/form.php', get_defined_vars());
            return;
        }

        if (!$premember->isExpired($account['date'])) {
            $member->insert([
                'name'     => $account['name'],
                'email'    => $account['email'],
                'password' => $account['password'],
            ]);

            $premember->delete([['id', '=', $account['id']]]);

            $this->render('member/register/complete.php');
        }
    }
}