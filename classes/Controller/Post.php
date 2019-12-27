<?php

class Controller_Post extends Controller_Base
{
    protected $image_dir = '';

    public function __construct()
    {
        $this->image_dir = Uploader::UPLOAD_DIR_NAME;
    }

    public function index()
    {
        $post = new Storage_Post();

        $paginator = new Paginator($post->count());

        $page = (int) $this->getParam('page');

        $paginator->setCurrentPage($page);

        $page_numbers = $paginator->getPageNumbers();

        $records = $post->selectRecords(['*'], [
            'where'    => [['is_deleted', '=', 0]],
            'order_by' => 'id DESC',
            'limit'    => $paginator->getPageItemCount(),
            'offset'   => $paginator->getRecordOffset(),
        ]);

        $this->render('post/index.php', get_defined_vars());
    }

    public function post()
    {
        $name       = $this->getParam('name');
        $title      = $this->getParam('title');
        $comment    = $this->getParam('comment');
        $password   = $this->getParam('password');
        $image_file = get_file('image');
        $member_id  = $this->getSession('member_id');

        $inputs = [
            'name'       => $name,
            'title'      => $title,
            'comment'    => $comment,
            'password'   => $password,
            'image_file' => $image_file,
        ];

        $post = new Storage_Post();

        $validator      = new Validator();
        $validator->setAttributeValidationRules($post->getValidationRule());
        $error_messages = $validator->validate($inputs);

        if (empty($error_messages)) {
            if (!empty($inputs['password'])) {
                $inputs['password'] = password_hash($inputs['password'], PASSWORD_BCRYPT);
            }

            $insert_values = [
                'name'      => $inputs['name'],
                'title'     => $inputs['title'],
                'comment'   => $inputs['comment'],
                'password'  => $inputs['password'],
                'member_id' => $member_id,
            ];

            if (!empty($inputs['image_file'])) {
                $uploader = new Uploader();

                $insert_values['image_path'] = $uploader->upload($inputs['image_file']);
            } else {
                $insert_values['image_path'] = null;
            }

            $post->insert($insert_values);

            $this->redirect('index.php');
        } else {
            $this->render('post/post.php', get_defined_vars());
        }
    }

    public function delete()
    {
        $id            = $this->getParam('id');
        $pass          = $this->getParam('password');
        $previous_page = $this->getParam('previous_page');
        $password      = $this->getParam('password');

        if (empty($id)) {
            $this->err400();
        }

        $previous_page     = (empty($page)) ? 1 : (int)$previous_page;
        $previous_page_url = "index.php?page={$previous_page}";

        $post = new Storage_Post();

        $record = $post->selectRecord(['*'], [['id', '=', $id]]);

        if (is_null($record) || $record['is_deleted'] === 1) {
            $this->err400();
        }

        $exists_password     = false;
        $is_correct_password = false;

        if (isset($record['password'])) {
            $exists_password = true;

            if (!is_null($password)) {
                if (password_verify($password, $record['password'])) {
                    $is_correct_password = true;
                }
            }
        }

        if ($is_correct_password && $this->getParam('do_delete')) {
            if (!empty($record['image_path'])) {
                $uploader = new Uploader();
                $uploader->delete($record['image_path']);
            }

            $post->softDelete([['id', '=', $id]]);

            $this->redirect('index.php', ['page' => $previous_page]);
        }

        $this->render('post/delete.php', get_defined_vars());
    }

    public function edit()
    {
        $id            = $this->getParam('id');
        $pass          = $this->getParam('password');
        $previous_page = $this->getParam('previous_page');
        $password      = $this->getParam('password');

        if (empty($id)) {
            $this->err400();
        }

        $previous_page     = (empty($previous_page)) ? 1 : (int)$previous_page;
        $previous_page_url = "index.php?page={$previous_page}";

        $post = new Storage_Post();

        $record = $post->selectRecord(['*'], [['id', '=', $id]]);
        if (empty($record) || $record['is_deleted'] === 1) {
            $this->err400();
        }

        $name       = $record['name'];
        $title      = $record['title'];
        $comment    = $record['comment'];
        $image_path = $record['image_path'];

        $is_edit_form        = true;
        $exists_password     = false;
        $is_correct_password = false;

        if (isset($record['password'])) {
            $exists_password = true;

            if (isset($password)) {
                if (password_verify($password, $record['password'])) {
                    $is_correct_password = true;
                }
            }
        }

        if ($is_correct_password && $this->getParam('do_edit')) {
            $inputs = [
                'name'       => $this->getParam('name'),
                'title'      => $this->getParam('title'),
                'comment'    => $this->getParam('comment'),
                'image_file' => get_file('image'),
            ];

            $validator = new Validator();
            $validator->setAttributeValidationRules($post->getValidationRule());
            $error_messages = $validator->validate($inputs);

            if (empty($error_messages)) {
                $update_values = [
                    'name'    => $inputs['name'],
                    'title'   => $inputs['title'],
                    'comment' => $inputs['comment'],
                ];

                $uploader = new Uploader();

                if ($this->getParam('delete_image')) {
                    $uploader->delete($record['image_path']);

                    $update_values['image_path'] = null;
                } else {
                    if (!empty($inputs['image_file'])) {
                        $update_values['image_path'] = $uploader->upload($inputs['image_file']);
                    }
                }

                $post->update($update_values, [['id', '=', $id]]);

                $this->redirect('index.php', array('page' => $previous_page));
            }
        }

        $this->render('post/edit.php', get_defined_vars());
    }
}