<?php

function mb_trim($string) {
    return preg_replace('/\A[\p{Z}]+|[\p{Z}]+\z/u', '', $string);
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES);
}

$host     = 'localhost';
$username = 'root';
$password = 'root';
$db_name  = 'bbs';
$encoding = 'utf8';

$mysqli = new mysqli($host, $username, $password, $db_name);

$validation_length = [
    'min' => [
        'title'   => 10,
        'comment' => 10,
    ],
    'max' => [
        'title'   => 32,
        'comment' => 200,
    ],
];

if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->set_charset($encoding);

$error_massages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title'])) {
        $title        = mb_trim($_POST['title']);
        $title_length = mb_strlen($title);

        if ($title_length === 0) {
            $error_massages[] = 'タイトルを入力してください';
        } elseif ($title_length < $validation_length['min']['title'] || $title_length > $validation_length['max']['title']) {
            $error_massages[] = "タイトルは{$validation_length['min']['title']}文字以上{$validation_length['max']['title']}以内で入力してください";
        }
    }

    if (isset($_POST['comment'])) {
        $comment        = mb_trim($_POST['comment']);
        $comment_length = mb_strlen($comment);

        if ($comment_length === 0) {
            $error_massages[] = 'メッセージを入力してください。';
        } elseif ($comment_length < $validation_length['min']['comment'] || $comment_length > $validation_length['max']['comment']) {
            $error_massages[] = "メッセージは{$validation_length['min']['comment']}文字以上{$validation_length['max']['comment']}文字以内で入力してください";
        }
    }

    if (empty($error_massages)) {
        $title   = $mysqli->real_escape_string($title);
        $comment = $mysqli->real_escape_string($comment);
        $mysqli->query("INSERT INTO posts (title, comment) VALUES ('$title', '$comment')");
        header("Location: {$_SERVER['SCRIPT_NAME']}");
        exit;
    } else {
        session_start();
        $_SESSION['title']   = $title;
        $_SESSION['comment'] = $comment;
    }
}

$results = $mysqli->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts   = $results->fetch_all(MYSQLI_ASSOC);

$mysqli->close();

?>

<html>
  <head>
    <title>challnege2</title>
  </head>
  <body>
    <?php if (!empty($error_massages)) : ?>
      <?php foreach ($error_massages as $error_message) : ?>
        <?php echo $error_message ?>
        <br>
      <?php endforeach ?>
    <?php endif ?>
    <form method="post" action="">
      <label for="title">Title</label><br>
      <input id="title" type="text" name="title" value="<?php echo isset($_SESSION['title']) ? $_SESSION['title'] : '' ?>"><br>
      <label for="comment">Body</label><br>
      <textarea id="comment" name="comment"><?php echo isset($_SESSION['comment']) ? $_SESSION['comment'] : '' ?></textarea><br>
      <input type="submit" value="Submit">
    </form>
    <?php foreach ($posts as $post) : ?>
      <hr>
      <?php echo h($post['title']) ?>
      <br>
      <?php echo nl2br(h($post['comment'])) ?>
      <?php echo h($post['created_at']) ?>
    <?php endforeach ?>
  </body>
  <?php session_unset() ?>
  <?php session_destroy() ?>
</html>