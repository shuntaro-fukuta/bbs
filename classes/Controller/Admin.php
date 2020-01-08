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
        $post = new Storage_Post();

        $paginator = new Paginator($post->count());
        $page      = (int)$this->getParam('page');
        $paginator->setCurrentPage($page);
        $paginator->setPageItemCount($this->page_item_count);
        $paginator->setMaxPagerCount($this->max_pager_count);
        $page_numbers = $paginator->getPageNumbers();

        $display_columns = ['id', 'title', 'comment', 'image_path', 'created_at'];

        $records = $post->selectRecords(['*'], [
            'order_by' => 'id DESC',
            'limit'    => $paginator->getPageItemCount(),
            'offset'   => $paginator->getRecordOffset(),
        ]);

        $this->render('admin/index.php', get_defined_vars());
    }

    public function deletePosts()
    {
        $page          = $this->getParam('page');
        $previous_page = (is_null($page)) ? 1 : $page;

        $delete_ids = $this->getParam('delete_ids');
        if (!is_null($delete_ids)) {
            $post     = new Storage_Post();
            $uploader = new Uploader();

            foreach ($delete_ids as $id) {
                $record = $post->selectRecord(['*'], [['id', '=', $id]]);
                if (is_null($record)) {
                    $this->err400();
                }

                if ($record['is_deleted'] === 0) {
                    if (isset($record['image_path'])) {
                        $uploader->delete($record['image_path']);
                        $post->update(['image_path' => null], [['id', '=', $record['id']]]);
                    }
                    $post->softDelete([['id', '=', $record['id']]]);
                }
            }
        }

        $this->redirect('index.php', ['page' => $previous_page]);
    }

    public function deleteImage()
    {
        $post_id = $this->getParam('post_id');
        $page    = $this->getParam('page');

        if (is_null($post_id)) {
            $this->err400();
        }

        $previous_page = is_null($page) ? 1 : $page;

        $post   = new Storage_Post();
        $record = $post->selectRecord(['*'], [['id', '=', $post_id]]);
        if (is_null($record)) {
            $this->err400();
        }

        if (!is_null($record['image_path'])) {
            $uploader = new Uploader();
            $uploader->delete($record['image_path']);
            $post->update(['image_path' => null], [['id', '=', $record['id']]]);
        }

        $this->redirect('index.php', ['page' => $previous_page]);
    }

    public function recover()
    {
        $post_id = $this->getParam('post_id');
        $page    = $this->getParam('page');

        if (is_null($post_id)) {
            $this->err400();
        }

        $previous_page = is_null($page) ? 1 : $page;

        $post = new Storage_Post();
        $post->update(['is_deleted' => 0], [['id', '=', $post_id]]);

        $this->redirect('index.php', ['page' => $previous_page]);
    }
}