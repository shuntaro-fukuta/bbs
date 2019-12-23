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

        $error_messages = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputs               = trim_values(['title', 'comment' , 'password'], $_POST);
            $inputs['image_file'] = get_file('image');

            $validator = new Validator();
            $validator->setAttributeValidationRules($posts->getValidationRule());
            $error_messages = $validator->validate($inputs);

            if (empty($error_messages)) {
                if (!is_null($inputs['password'])) {
                    $inputs['password'] = password_hash($inputs['password'], PASSWORD_BCRYPT);
                }

                $insert_values = [
                    'title'    => $inputs['title'],
                    'comment'  => $inputs['comment'],
                    'password' => $inputs['password'],
                ];

                if (!is_null($inputs['image_file'])) {
                    $uploader = new Uploader();

                    $uploaded_path = $uploader->upload($inputs['image_file']);
                    $insert_values['image_path'] = $uploaded_path;
                }

                $posts->insert($insert_values);

                header("Location: {$_SERVER['SCRIPT_NAME']}");
                exit;
            } else {
                if (isset($inputs['title'])) {
                    $title = $inputs['title'];
                }
                if (isset($inputs['comment'])) {
                    $comment = $inputs['comment'];
                }
            }
        }

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

    public function delete()
    {
        $id            = $this->getParam('id');
        $pass          = $this->getParam('password');
        $previous_page = $this->getParam('previous_page');

        if (empty($id)) {
            $this->err400();
        }

        $previous_page = (empty($page)) ? 1 : (int)$previous_page;

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
}