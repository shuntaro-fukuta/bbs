<?php

class Controller_Admin extends Controller_App
{
    protected $page_item_count = 20;
    protected $max_pager_count = 10;

    public function login()
    {
        if ($this->getEnv('request-method') === 'GET') {
            $this->render('admin/login.php');

            return;
        }

        $id       = $this->getParam('id');
        $password = $this->getParam('password');

        $member  = new Storage_Member();
        $account = $member->selectRecord(['*'], [['id', '=', $id]]);

        $error_messages = [];
        if (is_null($account) || !password_verify($password, $account['password'])) {
            $error_messages[] = 'idまたはパスワードが間違っています。';
        } elseif ($account['is_admin'] !== 1) {
            $error_messages[] = '管理者権限がありません。';
        }

        if (empty($error_messages)) {
            $this->session_manager->regenerateId();
            $this->session_manager->setVar('member_id', $account['id']);
            $this->redirect('index.php');
        }

        $this->render('admin/login.php', get_defined_vars());
    }

    public function index()
    {
        $post      = new Storage_Post();

        $paginator = new Paginator($post->count());
        $page      = (int)$this->getParam('page');
        $paginator->setCurrentPage($page);
        $paginator->setPageItemCount($this->page_item_count);
        $paginator->setMaxPagerCount($this->max_pager_count);
        $page_numbers = $paginator->getPageNumbers();

        $display_columns = ['id', 'title', 'comment', 'image_path', 'created_at'];

        $records = $post->selectRecords($display_columns, [
            'order_by' => 'id DESC',
            'limit'    => $paginator->getPageItemCount(),
            'offset'   => $paginator->getRecordOffset(),
        ]);

        $this->render('admin/index.php', get_defined_vars());
    }
}