<?php

require_once('functions.php');
require_once('db_connect.php');
require_once('Validator.php');
require_once('Posts.php');

try {
    $mysqli = connect_mysqli();
} catch (Exception $e) {
    echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
    exit;
}

$posts = new Posts($mysqli);

$exists_password     = false;
$is_correct_password = false;

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

// ebine
// これ一番最初に書くべきよね
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['password']) || !isset($_POST['id'])) {
    echo '不正なリクエストです';
    exit;
}

try {
    $record = $posts->selectRecord(['*'], ['where' => ['id', '=', $_POST['id']]]);
} catch (Exception $e) {
    echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
    exit;
}

$previous_page     = $_POST['previous_page'] ?? 1;
$previous_page_url = "index.php?page={$previous_page}";

if (!is_null($record['password'])) {
    $exists_password = true;
}

if (password_verify($_POST['password'], $record['password'])) {
    $is_correct_password = true;
}

$error_messages = [];

if ($exists_password && $is_correct_password && isset($_POST['do_edit'])) {
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
        try {
            $posts->update(
                [
                    'title'   => $inputs['title'],
                    'comment' => $inputs['comment'],
                ],
                ['where' => ['id', '=', $_POST['id']]]
            );
        } catch (Exception $e) {
            echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
            exit;
        }

        header("Location: {$previous_page_url}");
        exit;
    }
}

$mysqli->close();

?>

<html>
  <body>
    <?php if (!$exists_password) : ?>
      <p>この投稿にはパスワードが設定されていないため、削除できません。</p>
      <p><?php echo h($record['title']) ?></p>
      <p><?php echo h($record['comment']) ?></p>
      <p><?php echo h($record['created_at']) ?></p>
      <a href="<?php echo $previous_page_url ?>">前のページへ戻る</a>
    <?php elseif (!$is_correct_password) : ?>
      <p>パスワードが間違っています。もう一度入力してください</p>
      <p><?php echo h($record['title']) ?></p>
      <p><?php echo h($record['comment']) ?></p>
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
      <form method="POST" action="">
        <label for="title">Title</label><br>
        <input id="title" type="text" name="title" value="<?php echo isset($inputs['title']) ? h($inputs['title']) : h($record['title']) ?>"><br>
        <label for="comment">Body</label><br>
        <textarea id="comment" name="comment"><?php echo isset($inputs['comment']) ? h($inputs['comment']) : h($record['comment']) ?></textarea><br>
        <input type="hidden" name="id" value="<?php echo h($_POST['id']) ?>">
        <input type="hidden" name="previous_page" value="<?php echo h($previous_page) ?>">
        <input type="hidden" name="password" value="<?php echo h($_POST['password']) ?>">
        <input type="submit" name="do_edit" value="Submit">
        <input type="button" value="Cancel" onclick="location.href='<?php echo h($previous_page_url) ?>'">
      </form>
    <?php endif ?>
  </body>
</html>