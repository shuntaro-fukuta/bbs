<?php

require_once(__DIR__ . '/../functions/general.php');
require_once(__DIR__ . '/../classes/Validator.php');
require_once(__DIR__ . '/../classes/Posts.php');
require_once(__DIR__ . '/../classes/Uploader.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('HTTP/1.0 400 Bad Request');
    exit;
}

$posts = new Posts();

try {
    $record = $posts->selectRecord(['*'], [['id', '=', $_POST['id']]]);

    if (is_null($record)) {
        header('HTTP/1.0 404 Not Found');
        exit;
    }

    $previous_page     = $_POST['previous_page'] ?? 1;
    $previous_page_url = "index.php?page={$previous_page}";

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

    if ($is_correct_password && isset($_POST['do_edit'])) {
        $inputs               = trim_values(['title', 'comment'], $_POST);
        $inputs['image_file'] = get_file('image');

        $validator = new Validator();
        $validator->setAttributeValidationRules($posts->getValidationRule());
        $error_messages = $validator->validate($inputs);

        if (empty($error_messages)) {
            $update_values = [
                'title'   => $inputs['title'],
                'comment' => $inputs['comment'],
            ];

            if (isset($_POST['delete_image'])) {
                $uploader = new Uploader();
                $uploader->delete($record['image_path']);

                $update_values['image_path'] = null;
            } else {
                if (!is_null($inputs['image_file'])) {
                    $uploader = new Uploader();

                    $uploaded_path = $uploader->upload($inputs['image_file']);

                    $update_values['image_path'] = $uploaded_path;
                }
            }

            $posts->update($update_values, [['id', '=', $_POST['id']]]);

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