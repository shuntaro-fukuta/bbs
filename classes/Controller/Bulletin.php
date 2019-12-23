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
        $posts = new Posts();

        $paginator = new Paginator($posts->count());

        $page = (int) $this->getParam('page');

        $paginator->setCurrentPage($page);

        $page_numbers = $paginator->getPageNumbers();

        $records = $posts->selectRecords(['*'], [
            'order_by' => 'id DESC',
            'limit'    => $paginator->getPageItemCount(),
            'offset'   => $paginator->getRecordOffset(),
        ]);

        $this->render('bulletin/index.php', get_defined_vars());
    }

    public function post()
    {
        $title      = $this->getParam('title');
        $comment    = $this->getParam('comment');
        $password   = $this->getParam('password');
        $image_file = get_file('image');

        $inputs = [
            'title'      => $title,
            'comment'    => $comment,
            'password'   => $password,
            'image_file' => $image_file,
        ];

        $posts = new Posts();

        $validator      = new Validator();
        $validator->setAttributeValidationRules($posts->getValidationRule());
        $error_messages = $validator->validate($inputs);

        if (empty($error_messages)) {
            if (!empty($inputs['password'])) {
                $inputs['password'] = password_hash($inputs['password'], PASSWORD_BCRYPT);
            }

            $insert_values = [
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

            $posts->insert($insert_values);

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

        $posts = new Posts();

        $record = $posts->selectRecord(['*'], [['id', '=', $_POST['id']]]);

        if (is_null($record)) {
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

            $posts->delete([['id', '=', $_POST['id']]]);

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

        $posts = new Posts();

        $record = $posts->selectRecord(['*'], [['id', '=', $_POST['id']]]);
        if (empty($record)) {
            $this->err400();
        }

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
            $title      = $this->getParam('title');
            $comment    = $this->getParam('comment');
            $image_file = get_file('image');

            $inputs = [
                'title'      => $title,
                'comment'    => $comment,
                'image_file' => $image_file,
            ];

            $validator = new Validator();
            $validator->setAttributeValidationRules($posts->getValidationRule());
            $error_messages = $validator->validate($inputs);

            if (empty($error_messages)) {
                $update_values = [
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

                $posts->update($update_values, [['id', '=', $_POST['id']]]);

                $this->redirect('index.php', array('page' => $previous_page));
            }
        }

        $this->render('bulletin/edit.php', get_defined_vars());
    }
}