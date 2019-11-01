<?php

require_once('functions.php');
require_once('Posts.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['password']) || !isset($_POST['id'])) {
    echo '不正なリクエストです';
    exit;
}

$posts = new Posts();

try {
    $record = $posts->selectRecord(['*'], ['where' => ['id', '=', $_POST['id']]]);
} catch (Exception $e) {
    echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
    exit;
}

$previous_page     = $_POST['previous_page'] ?? 1;
$previous_page_url = "index.php?page={$previous_page}";

$exists_password     = false;
$is_correct_password = false;

if (!is_null($record['password'])) {
    $exists_password = true;

    if (password_verify($_POST['password'], $record['password'])) {
        $is_correct_password = true;
    }
}

if ($is_correct_password && isset($_POST['do_delete'])) {
    try {
        $posts->delete(['where' => ['id', '=', $_POST['id']]]);
    } catch (Exception $e) {
        echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
        exit;
    }

    header("Location: {$previous_page_url}");
    exit;
}

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
        <input type="submit" value="Del">
      </form>
    <?php else : ?>
    <p><?php echo h($record['title']) ?></p>
    <p><?php echo h($record['comment']) ?></p>
    <p><?php echo h($record['created_at']) ?></p>
      <p>削除してよろしいですか？</p>
      <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo h($_POST['id']) ?>">
        <input type="hidden" name="previous_page" value="<?php echo h($previous_page) ?>">
        <input type="hidden" name="password" value="<?php echo h($_POST['password']) ?>">
        <input type="submit" name="do_delete" value="Yes">
        <input type="button" value="Cancel" onclick="location.href='<?php echo h($previous_page_url) ?>'">
      </form>
    <?php endif ?>
  </body>
</html>