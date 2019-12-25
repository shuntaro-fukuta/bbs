<?php

class Controller_Member  extends Controller_Base
{

    public function register()
    {
        $request = $this->getEnv('request-method');


        if ($request === 'GET') {
            $token = $this->getParam('token');

            if (empty($token)) {
                $this->render('member/register.php');
            } else {
                //     トークンチェックok
                //         memberテーブルに保存
                //         prememberから削除
                //             登録完了画面
            }
        }

        if ($request === 'POST') {
            $inputs = [
                'name'     => $this->getParam('name'),
                'email'    => $this->getParam('email'),
                'password' => $this->getParam('password'),
            ];

            $pre_member = new Storage_Premember();

            if ($this->getParam('confirm') === '1') {
                $error_messages = $pre_member->validate($inputs);

                if (empty($error_messages)) {
                    // 確認画面
                } else {
                    $this->render('member/register.php', get_defined_vars());
                }
            }

            // submit
                //     トークン作成
                //     pre_memberテーブルに情報を保存
                //         メールを送る url='register.php?token=トークン'
                //             メール送った画面
        }
    }
}