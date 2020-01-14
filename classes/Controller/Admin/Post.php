<?php

class Controller_Admin_Post extends Controller_App
{
    const PAGE_ITEM_COUNT = 20;
    const PAGER_COUNT     = 10;

    public function __construct()
    {
        parent::__construct();

        if (!$this->isAdmin()) {
            $this->redirect('login.php');
        }
    }

    public function index()
    {
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

        $this->session_manager->setVars('search_conditions', $search_conditions);
        $this->session_manager->setVar('page', $page);

        $this->render('admin/index.php', get_defined_vars());
    }

    public function deletePosts()
    {
        $page          = $this->session_manager->getVar('page');
        $previous_page = (is_null($page)) ? 1 : $page;

        $delete_ids = $this->getParam('delete_ids');
        if (!is_null($delete_ids)) {
            $post     = new Storage_Post();
            $uploader = new Uploader();

            foreach ($delete_ids as $key => $id) {
                $delete_ids[$key] = $post->escape($id, false);
            }
            $records = $post->selectRecords(['*'], [
                'where' => ['condition' => ' id IN (' . implode(', ', $delete_ids) . ')']
            ]);

            foreach ($records as $record) {
                if (isset($record['image_path'])) {
                    $uploader->delete($record['image_path']);
                    $post->update(['image_path' => null], ['condition' => 'id = ?', 'values' => [$record['id']]]);
                }
                $post->softDelete(['condition' => 'id = ?', 'values' => [$record['id']]]);
            }
        }

        $this->redirect('index.php', [
            'page'              => $previous_page,
            'search_conditions' => $this->session_manager->getVar('search_conditions'),
        ]);
    }

    public function deleteImage()
    {
        $post_id = $this->getParam('post_id');
        if (is_null($post_id)) {
            $this->err400();
        }

        $page          = $this->session_manager->getVar('page');
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
            'search_conditions' => $this->session_manager->getVar('search_conditions'),
        ]);
    }

    public function recover()
    {
        $post_id = $this->getParam('post_id');
        if (is_null($post_id)) {
            $this->err400();
        }

        $page          = $this->session_manager->getVar('page');
        $previous_page = is_null($page) ? 1 : $page;

        $post = new Storage_Post();
        $post->update(['is_deleted' => 0], ['condition' => 'id = ?', 'values' => [$post_id]]);

        $this->redirect('index.php', [
            'page'              => $previous_page,
            'search_conditions' => $this->session_manager->getVar('search_conditions'),
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