<?php

require_once('functions.php');
require_once('db_setting.php');

$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->set_charset($db_encoding);

$invalid_request_message = '不正なリクエストです';
$error_message           = null;
$no_password             = false;
$wrong_password          = false;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo $invalid_request_message;
    exit;
}

if (!isset($_POST['delete_password']) || !isset($_POST['id']) || !isset($_POST['previous_page_url'])) {
    echo $invalid_request_message;
    exit;
}

$id                = $_POST['id'];
$previous_page_url = $_POST['previous_page_url'];

$post = $mysqli->query("SELECT * FROM posts WHERE id={$id}")->fetch_assoc();

if (empty($post['password'])) {
    $error_message = 'この投稿にはパスワードが設定されていないため、削除できません。';
    $no_password   = true;
} elseif (!password_verify($_POST['delete_password'], $post['password'])) {
    $error_message  = 'パスワードが間違っています。もう一度入力してください';
    $wrong_password = true;
}

if (!isset($error_message) && isset($_POST['confirm'])) {
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
    <?php if ($no_password) : ?>
      <a href="<?php echo $previous_page_url ?>">前のページへ戻る</a>
    <?php elseif ($wrong_password) : ?>
      <form method="post" action="">
        Pass
        <input type="password" name="delete_password">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="previous_page_url" value="<?php echo $previous_page_url ?>">
        <input type="submit" value="Del">
      </form>
    <?php else : ?>
      <p>削除してよろしいですか？</p>
      <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="previous_page_url" value="<?php echo $previous_page_url ?>">
        <input type="hidden" name="delete_password" value="<?php echo $_POST['delete_password'] ?>">
        <input type="submit" name="confirm" value="Yes">
        <input type="button" value="Cancel" onclick="location.href='<?php echo $previous_page_url ?>'">
      </form>
    <?php endif ?>
  </body>
</html>