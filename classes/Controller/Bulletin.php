<?php

class Controller_Bulletin extends Controller_Base
{
    protected $image_dir = '';

    public function __construct()
    {
        $this->image_dir = Uploader::UPLOAD_DIR_NAME;
    }

    public function index()
    {
        $bulletin = new Storage_Bulletin();

        $paginator = new Paginator($bulletin->count());

        $page = (int) $this->getParam('page');

        $paginator->setCurrentPage($page);

        $page_numbers = $paginator->getPageNumbers();

        $records = $bulletin->selectRecords(['*'], [
            'where'    => [['is_deleted', '=', 0]],
            'order_by' => 'id DESC',
            'limit'    => $paginator->getPageItemCount(),
            'offset'   => $paginator->getRecordOffset(),
        ]);

        $this->render('bulletin/index.php', get_defined_vars());
    }

    public function post()
    {
        $inputs = [
            'name'       => $this->getParam('name'),
            'title'      => $this->getParam('title'),
            'comment'    => $this->getParam('comment'),
            'password'   => $this->getParam('password'),
            'image_file' => get_file('image'),
        ];

        $bulletin = new Storage_Bulletin();

        $validator      = new Validator();
        $validator->setAttributeValidationRules($bulletin->getValidationRule());
        $error_messages = $validator->validate($inputs);

        if (empty($error_messages)) {
            if (!empty($inputs['password'])) {
                $inputs['password'] = password_hash($inputs['password'], PASSWORD_BCRYPT);
            }

            $insert_values = [
                'name'     => $inputs['name'],
                'title'    => $inputs['title'],
                'comment'  => $inputs['comment'],
                'password' => $inputs['password'],
            ];

            if (!empty($inputs['image_file'])) {
                $uploader = new Uploader();

                $insert_values['image_path'] = $uploader->upload($inputs['image_file']);
            } else {
                $insert_values['image_path'] = null;
            }

            $bulletin->insert($insert_values);

            $this->redirect('index.php');
        } else {
            $this->render('bulletin/post.php', get_defined_vars());
        }
    }

    public function delete()
    {
        $id            = $this->getParam('id');
        $pass          = $this->getParam('password');
        $previous_page = $this->getParam('previous_page');

        if (empty($id)) {
            $this->err400();
        }

        $previous_page     = (empty($page)) ? 1 : (int)$previous_page;
        $previous_page_url = "index.php?page={$previous_page}";

        $bulletin = new Storage_Bulletin();

        $record = $bulletin->selectRecord(['*'], [['id', '=', $_POST['id']]]);

        if (is_null($record) || $record['is_deleted'] === 1) {
            $this->err400();
        }

        $exists_password     = false;
        $is_correct_password = false;

        if (isset($record['password'])) {
            $exists_password = true;

            if (isset($_POST['password'])) {
                if (password_verify($_POST['password'], $record['password'])) {
                    $is_correct_password = true;
                }
            }
        }

        if ($is_correct_password && $this->getParam('do_delete')) {
            if (!empty($record['image_path'])) {
                $uploader = new Uploader();
                $uploader->delete($record['image_path']);
            }

            $bulletin->softDelete([['id', '=', $_POST['id']]]);

            $this->redirect('index.php', ['page' => $previous_page]);
        }

        $this->render('bulletin/delete.php', get_defined_vars());
    }

    public function edit()
    {
        $id            = $this->getParam('id');
        $pass          = $this->getParam('password');
        $previous_page = $this->getParam('previous_page');

        if (empty($id)) {
            $this->err400();
        }

        $previous_page     = (empty($previous_page)) ? 1 : (int)$previous_page;
        $previous_page_url = "index.php?page={$previous_page}";

        $bulletin = new Storage_Bulletin();

        $record = $bulletin->selectRecord(['*'], [['id', '=', $_POST['id']]]);
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

            if (isset($_POST['password'])) {
                if (password_verify($_POST['password'], $record['password'])) {
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
            $validator->setAttributeValidationRules($bulletin->getValidationRule());
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

                $bulletin->update($update_values, [['id', '=', $_POST['id']]]);

                $this->redirect('index.php', array('page' => $previous_page));
            }
        }

        $this->render('bulletin/edit.php', get_defined_vars());
    }
}