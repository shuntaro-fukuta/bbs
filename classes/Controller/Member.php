<?php

class Controller_Member  extends Controller_Base
{

    public function register()
    {
        // GET
        // トークンがない
        //     登録フォーム
        // トークンがある
        //     トークンチェックok
        //         memberテーブルに保存
        //         prememberから削除
        //             登録完了画面

        // POST
        // 確認ボタン
        //     validation ok
        //         確認画面

        // submitボタン
        //     トークン作成
        //     pre_memberテーブルに情報を保存
        //         メールを送る url='register.php?token=トークン'
        //             メール送った画面
    }
}