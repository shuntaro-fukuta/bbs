<?php

require_once(dirname(__FILE__) . '/../functions/general.php');
require_once(dirname(__FILE__) . '/../classes/Validator.php');
require_once(dirname(__FILE__) . '/../classes/Posts.php');
require_once(dirname(__FILE__) . '/../classes/ImageUploader.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['password']) || !isset($_POST['id'])) {
    header('HTTP/1.0 400 Bad Request');
    exit;
}

$post_edit_validation_rules = [
    'title' => [
        'required' => true,
        'length'   => [
            'min' => 10,
            'max' => 32,
        ],
    ],
    'comment' => [
        'required' => true,
        'length'   => [
            'min' => 10,
            'max' => 200,
        ],
    ],
    // TODO: カラム名に関する修正
    'image_path' => [
        'mime_types' => [
            'jpeg' => 'image/jpeg',
            'jpg'  => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
        ],
        'file_size' => [
            'max' => 1000000,
        ],
    ],
];

$posts = new Posts();

try {
    $record = $posts->selectRecord(['*'], [['id', '=', $_POST['id']]]);

    if (is_null($record)) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    $previous_page     = $_POST['previous_page'] ?? 1;
    $previous_page_url = "index.php?page={$previous_page}";

    $exists_password     = isset($record['password']);
    $is_correct_password = password_verify($_POST['password'], $record['password']);
    $do_edit             = isset($_POST['do_edit']);

    if ($exists_password && $is_correct_password && $do_edit) {
        $inputs = trim_values(['title', 'comment'], $_POST);

        if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'] !== '') {
            $inputs['image_path'] = $_FILES['image']['tmp_name'];
        } else {
            $inputs['image_path'] = null;
        }

        $validator = new Validator();
        $validator->setAttributeValidationRules($post_edit_validation_rules);
        $error_messages = $validator->validate($inputs);

        if (empty($error_messages)) {
            if ($_POST['delete_image']) {
                // 画像を削除する
                unlink($record['image_path']);

                $inputs['image_path'] = null;
            } elseif (!is_null($inputs['image_path'])) {
                $uploader = new ImageUploader();

                $uploaded_path = $uploader->upload($inputs['image_path']);

                $inputs['image_path'] = $uploaded_path;
            }

            $posts->update($inputs, [['id', '=', $_POST['id']]]);

            header("Location: {$previous_page_url}");
            exit;
        }
    }
} catch (Exception $e) {
    echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
    exit;
}

?>

<html>
  <body>
    <?php if (!$exists_password) : ?>
      <p>この投稿にはパスワードが設定されていないため、削除できません。</p>
      <p><?php echo h($record['title']) ?></p>
      <p><?php echo h($record['comment']) ?></p>
      <?php if (isset($record['image_path'])) : ?>
        <img src="<?php echo $record['image_path'] ?>"><br>
      <?php endif ?>
      <p><?php echo h($record['created_at']) ?></p>
      <a href="<?php echo $previous_page_url ?>">前のページへ戻る</a>
    <?php elseif (!$is_correct_password) : ?>
      <p>パスワードが間違っています。もう一度入力してください</p>
      <p><?php echo h($record['title']) ?></p>
      <p><?php echo h($record['comment']) ?></p>
      <?php if (isset($record['image_path'])) : ?>
        <img src="<?php echo $record['image_path'] ?>"><br>
      <?php endif ?>
      <p><?php echo h($record['created_at']) ?></p>
      <form method="post" action="">
        Pass
        <input type="password" name="password">
        <input type="hidden" name="id" value="<?php echo h($_POST['id']) ?>">
        <input type="hidden" name="previous_page" value="<?php echo h($previous_page) ?>">
        <input type="submit" value="Edit">
      </form>
    <?php else : ?>
      <?php if (!empty($error_messages)) : ?>
        <?php foreach ($error_messages as $message) : ?>
          <?php echo $message ?>
          <br>
        <?php endforeach ?>
      <?php endif ?>
      <form method="POST" action="" enctype="multipart/form-data">
        <label for="title">Title</label><br>
        <input id="title" type="text" name="title" value="<?php echo isset($inputs['title']) ? h($inputs['title']) : h($record['title']) ?>"><br>
        <label for="comment">Body</label><br>
        <textarea id="comment" name="comment"><?php echo isset($inputs['comment']) ? h($inputs['comment']) : h($record['comment']) ?></textarea><br>
        <?php if (isset($record['image_path'])) : ?>
          <img src="<?php echo $record['image_path'] ?>"><br>
          <input type="checkbox" name="delete_image">Delete Imaege<br>
        <?php endif ?>
        <input type="file" name="image"><br>
        <br>
        <input type="hidden" name="id" value="<?php echo h($_POST['id']) ?>">
        <input type="hidden" name="previous_page" value="<?php echo h($previous_page) ?>">
        <input type="hidden" name="password" value="<?php echo h($_POST['password']) ?>">
        <input type="submit" name="do_edit" value="Submit">
        <input type="button" value="Cancel" onclick="location.href='<?php echo h($previous_page_url) ?>'">
      </form>
    <?php endif ?>
  </body>
</html>