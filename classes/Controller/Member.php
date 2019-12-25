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
            $is_confirm = $this->getParam('confirm');
            $is_submit  = $this->getParam('submit');

            if ($is_confirm) {
                // バリデーション
            }

            if ($is_submit) {
                //     トークン作成
                //     pre_memberテーブルに情報を保存
                //         メールを送る url='register.php?token=トークン'
                //             メール送った画面
            }
        }
    }
}