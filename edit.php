<?php

require_once('functions.php');
require_once('db_connect.php');
require_once('Validator.php');

$mysqli = connect_mysqli();

$is_no_password    = false;
$is_wrong_password = false;

$input_keys = ['title', 'comment'];

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
];

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !isset($_POST['password'])
    || !isset($_POST['id'])
    ) {
    echo '不正なリクエストです';
    exit;
}

$id = $mysqli->real_escape_string($_POST['id']);

$post = $mysqli->query("SELECT * FROM posts WHERE id = {$id}")->fetch_assoc();

$previous_page     = $_POST['previous_page'] ?? 1;
$previous_page_url = "index.php?page={$previous_page}";

if (is_empty($post['password'])) {
    $is_no_password   = true;
} elseif (!password_verify($_POST['password'], $post['password'])) {
    $is_wrong_password = true;
}

$error_messages = [];

if (!$is_no_password && !$is_wrong_password && isset($_POST['do_edit'])) {
    $inputs = get_trimmed_inputs($input_keys, $_POST);

    $validator = new Validator();

    try {
        $validator->setAttributeValidationRules($post_edit_validation_rules);
        $error_messages = $validator->validate($inputs);
    } catch (Exception $e) {
        echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
        exit;
    }

    if (empty($error_messages)) {
        $title    = $mysqli->real_escape_string($inputs['title']) ;
        $comment  = $mysqli->real_escape_string($inputs['comment']);

        $mysqli->query("UPDATE posts SET title = '{$title}', comment = '{$comment}' WHERE id = {$id}");

        header("Location: {$previous_page_url}");
        exit;
    }
}

$mysqli->close();

?>

<html>
  <body>
    <?php if ($is_no_password) : ?>
      <p>この投稿にはパスワードが設定されていないため、削除できません。</p>
      <p><?php echo h($post['title']) ?></p>
      <p><?php echo h($post['comment']) ?></p>
      <p><?php echo h($post['created_at']) ?></p>
      <a href="<?php echo $previous_page_url ?>">前のページへ戻る</a>
    <?php elseif ($is_wrong_password) : ?>
      <p>パスワードが間違っています。もう一度入力してください</p>
      <form method="post" action="">
        Pass
        <input type="password" name="password">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="previous_page" value="<?php echo $previous_page ?>">
        <input type="submit" value="Del">
      </form>
    <?php else : ?>
      <?php if (!empty($error_messages)) : ?>
        <?php foreach ($error_messages as $message) : ?>
          <?php echo $message ?>
          <br>
        <?php endforeach ?>
      <?php endif ?>
      <form method="POST" action="">
        <label for="title">Title</label><br>
        <input id="title" type="text" name="title" value="<?php echo $inputs['title'] ?? $post['title']?>"><br>
        <label for="comment">Body</label><br>
        <textarea id="comment" name="comment"><?php echo $inputs['comment'] ?? $post['comment']?></textarea><br>
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="previous_page" value="<?php echo $previous_page ?>">
        <input type="hidden" name="password" value="<?php echo $_POST['password'] ?>">
        <input type="submit" name="do_edit" value="Submit">
        <input type="button" value="Cancel" onclick="location.href='<?php echo $previous_page_url ?>'">
      </form>
    <?php endif ?>
  </body>
</html>