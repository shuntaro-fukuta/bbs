<?php

require_once('functions.php');
require_once('db_setting.php');

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->set_charset(DB_ENCODING);

$error_message     = null;
$is_no_password    = false;
$is_wrong_password = false;

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

$previous_page     = isset($_POST['previous_page']) ? $_POST['previous_page'] : 1;
$previous_page_url = "index.php?page={$previous_page}";

echo $previous_page_url;
if (empty($post['password'])) {
    $is_no_password   = true;
} elseif (!password_verify($_POST['password'], $post['password'])) {
    $is_wrong_password = true;
}

if (!isset($error_message) && isset($_POST['do_delete'])) {
    $mysqli->query("DELETE FROM posts WHERE id = {$id}");

    header("Location: {$previous_page_url}");
    exit;
}

$mysqli->close();

?>

<html>
  <body>
    <?php if ($is_no_password) : ?>
      <p>この投稿にはパスワードが設定されていないため、削除できません。</p>
    <?php elseif ($is_wrong_password) : ?>
      <p>パスワードが間違っています。もう一度入力してください</p>
    <?php endif ?>
    <p><?php echo h($post['title']) ?></p>
    <p><?php echo h($post['comment']) ?></p>
    <p><?php echo h($post['created_at']) ?></p>
    <?php if ($is_no_password) : ?>
      <a href="<?php echo $previous_page_url ?>">前のページへ戻る</a>
    <?php elseif ($is_wrong_password) : ?>
      <form method="post" action="">
        Pass
        <input type="password" name="password">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="previous_page" value="<?php echo $previous_page ?>">
        <input type="submit" value="Del">
      </form>
    <?php else : ?>
      <p>削除してよろしいですか？</p>
      <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="previous_page" value="<?php echo $previous_page ?>">
        <input type="hidden" name="password" value="<?php echo $_POST['password'] ?>">
        <input type="submit" name="do_delete" value="Yes">
        <input type="button" value="Cancel" onclick="location.href='<?php echo $previous_page_url ?>'">
      </form>
    <?php endif ?>
  </body>
</html>