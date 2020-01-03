<?php

class Controller_Member  extends Controller_Base
{
    protected $session_manager;

    public function __construct()
    {
        $this->session_manager = new SessionManager();
    }

    public function register()
    {
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

                if ($premember->count([['email', '=', $email]]) === 0) {
                    $premember->insert($inputs);
                } else {
                    $inputs['created_at'] = date('Y-m-d H:i:s');
                    $premember->update($inputs, [['email', '=', $email]]);
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

        $member    = new Storage_Member();
        $premember = new Storage_Premember();

        $token = $this->getParam('token');
        if (is_null($token)) {
            $this->err400();
        }

        $error_messages = [];

        $account = $premember->selectRecord(['*'], [['token', '=', $token]]);
        if (is_null($account)) {
            $error_messages[] = '会員登録に失敗しました。' . PHP_EOL . 'もう一度登録し直してください。';
        } elseif ($member->count([['email', '=', $account['email']]]) === 1) {
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

        $premember->delete([['id', '=', $account['id']]]);

        $this->render('member/register/complete.php');
    }

    public function login()
    {
        if (!is_null($this->session_manager->getVar('member_id'))) {
            $this->redirect('index.php');
        }

        if ($this->getEnv('request-method') === 'POST') {
            $email    = $this->getParam('email');
            $password = $this->getParam('password');

            $member  = new Storage_Member();
            $account = $member->selectRecord(['*'], [['email', '=', $email]]);

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
        if (is_null($this->session_manager->getVar('member_id'))) {
            $this->redirect('login.php');
        }

        $this->session_manager->destroyVar();
        // hamaco: SessionManager があるのにこの辺は自分で書くの？
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', 1);
        }
        session_destroy();

        $this->redirect('index.php');
    }
}