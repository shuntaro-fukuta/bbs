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
    || !isset($_POST['previous_page_url'])  // ebine これは必須じゃないと思うよ
    ) {
    echo '不正なリクエストです';
    exit;
}

// エスケープ
$id                = $_POST['id'];
$previous_page_url = $_POST['previous_page_url'];

$post = $mysqli->query("SELECT * FROM posts WHERE id = {$id}")->fetch_assoc();

if (empty($post['password'])) {
    // ebine
    // フラグだけあれば、HTMLの方でエラーメッセージかけるよね
    $error_message = 'この投稿にはパスワードが設定されていないため、削除できません。';
    $is_no_password   = true;
} elseif (!password_verify($_POST['password'], $post['password'])) {
    $error_message  = 'パスワードが間違っています。もう一度入力してください';
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
    <?php if (isset($error_message)) : ?>
      <?php echo $error_message ?>
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
        <input type="hidden" name="previous_page_url" value="<?php echo $previous_page_url ?>">
        <input type="submit" value="Del">
      </form>
    <?php else : ?>
      <p>削除してよろしいですか？</p>
      <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="previous_page_url" value="<?php echo $previous_page_url ?>">
        <input type="hidden" name="password" value="<?php echo $_POST['password'] ?>">
        <input type="submit" name="do_delete" value="Yes">
        <input type="button" value="Cancel" onclick="location.href='<?php echo $previous_page_url ?>'">
      </form>
    <?php endif ?>
  </body>
</html>