<?php

class Controller_Admin_Post extends Controller_App
{
    const PAGE_ITEM_COUNT = 20;
    const PAGER_COUNT     = 10;

    public function index()
    {
        if (!$this->isAdmin()) {
            $this->redirect('login.php');
        }

        $search_conditions = $this->getParam('search_conditions');

        $post  = new Storage_Post();
        $where = $post->buildWhereToSearch($search_conditions);

        $paginator = $this->createPaginator($post->count($where));
        $page      = (int)$this->getParam('page');
        $paginator->setCurrentPage($page);
        $page_numbers = $paginator->getPageNumbers();

        $records = $post->selectRecords(['*'], [
            'where'    => $where,
            'order_by' => 'id DESC',
            'limit'    => $paginator->getPageItemCount(),
            'offset'   => $paginator->getRecordOffset(),
        ]);

        $display_columns = ['id', 'title', 'comment', 'image_path', 'created_at'];

        $this->render('admin/index.php', get_defined_vars());
    }

    public function deletePosts()
    {
        if (!$this->isAdmin()) {
            $this->redirect('login.php');
        }

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

        $this->redirect('index.php', [
            'page'              => $previous_page,
            'search_conditions' => $this->getParam('search_conditions')
        ]);
    }

    public function deleteImage()
    {
        if (!$this->isAdmin()) {
            $this->redirect('login.php');
        }

        $post_id = $this->getParam('post_id');
        if (is_null($post_id)) {
            $this->err400();
        }

        $page          = $this->getParam('page');
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

        $this->redirect('index.php', [
            'page'              => $previous_page,
            'search_conditions' => $this->getParam('search_conditions'),
        ]);
    }

    public function recover()
    {
        if (!$this->isAdmin()) {
            $this->redirect('login.php');
        }

        $post_id = $this->getParam('post_id');
        if (is_null($post_id)) {
            $this->err400();
        }

        $page          = $this->getParam('page');
        $previous_page = is_null($page) ? 1 : $page;

        $post = new Storage_Post();
        $post->update(['is_deleted' => 0], ['condition' => 'id = ?', 'values' => [$post_id]]);

        $this->redirect('index.php', [
            'page'              => $previous_page,
            'search_conditions' => $this->getParam('search_conditions'),
        ]);
    }

    protected function createPaginator($posts_count)
    {
        $paginator = new Paginator(
            $posts_count,
            self::PAGE_ITEM_COUNT,
            self::PAGER_COUNT
        );

        $paginator->setUri($this->getEnv('request_uri'));

        return $paginator;
    }
}