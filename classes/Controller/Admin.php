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
        $account = $member->selectRecord(['*'], [
            'condition' => 'id = ?',
            'values'    => [$id],
        ]);

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
                $record = $post->selectRecord(['*'], [
                    'condition' => 'id = ?',
                    'values'    => [$id],
                ]);
                if (is_null($record)) {
                    $this->err400();
                }

                if ($record['is_deleted'] === 0) {
                    if (isset($record['image_path'])) {
                        $uploader->delete($record['image_path']);
                        $post->update(['image_path' => null], ['condition' => 'id = ?', 'values' => [$record['id']]]);
                    }
                    $post->softDelete(['condition' => 'id = ?', 'values' => [$record['id']]]);
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
        $record = $post->selectRecord(['*'], [
            'condition' => 'id = ?',
            'values'    => [$post_id],
        ]);
        if (is_null($record)) {
            $this->err400();
        }

        if (!is_null($record['image_path'])) {
            $uploader = new Uploader();
            $uploader->delete($record['image_path']);
            $post->update(['image_path' => null], ['condition' => 'id = ?', 'values' => [$record['id']]]);
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
        $post->update(['is_deleted' => 0], ['condition' => 'id = ?', 'values' => [$post_id]]);

        $this->redirect('index.php', ['page' => $previous_page]);
    }

    public function search()
    {
        if ($this->getEnv('request-method') === 'GET') {
            $this->render('admin/search.php');

            return;
        }

        $title_search_string   = $this->getParam('title_search_string');
        $comment_search_string = $this->getParam('comment_search_string');
        $image_status          = $this->getParam('image_status');
        $post_status           = $this->getParam('post_status');

        $conditions = [];
        $values     = [];

        if (!is_null($title_search_string)) {
            $conditions[] = 'title LIKE ?';
            $values[]     = '%' . $title_search_string . '%';
        }

        if (!is_null($comment_search_string)) {
            $conditions[] = 'comment LIKE ?';
            $values[]     = '%' . $comment_search_string . '%';
        }

        if (!is_null($image_status)) {
            switch ($image_status) {
                case 'with':
                    $conditions[] = 'image_path IS NOT NULL';
                    break;
                case 'without':
                    $conditions[] = 'image_path IS NULL';
                    break;
            }
        }

        if (!is_null($post_status)) {
            switch ($post_status) {
                case 'on':
                    $conditions[] = 'is_deleted = ?';
                    $values[]     = 0;
                    break;
                case 'delete':
                    $conditions[] = 'is_deleted = ?';
                    $values[]     = 1;
                    break;
            }
        }

        $post = new Storage_Post();

        if (empty($conditions)) {
            $records = $post->selectRecords(['*'], ['order_by' => 'id DESC']);
        } else {
            $condition = implode(' AND ', $conditions);

            $records = $post->selectRecords(['*'], [
                'where' => [
                    'condition' => $condition,
                    'values'    => $values,
                ],
                'order_by' => 'id DESC',
            ]);

        }

        $this->render('admin/search.php', get_defined_vars());
    }
}