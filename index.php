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

    $min_comment_length = 10;
    $max_comment_length = 200;

    $mysqli = new mysqli($host, $username, $password, $db_name);

    if ($mysqli->connect_error) {
        echo $mysqli->connect_error;
        exit;
    }

    $mysqli->set_charset($encoding);

    $error_massage = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $comment        = mb_trim($_POST['comment']);
        $comment_length = mb_strlen($comment);

        if ($comment_length === 0) {
            $error_massage = '何か入力してください。';
        } elseif ($comment_length < $min_comment_length || $comment_length > $max_comment_length) {
            $error_massage = "入力は{$min_comment_length}文字以上{$max_comment_length}文字以内にして下さい。";
        } else {
            $comment = $mysqli->real_escape_string($comment);
            $mysqli->query("INSERT INTO posts (comment) VALUES ('$comment')");
            header("Location: {$_SERVER['SCRIPT_NAME']}");
            exit;
        }
    }

    $results = $mysqli->query("SELECT id, comment, created_at FROM posts ORDER BY created_at DESC");
    $posts   = $results->fetch_all(MYSQLI_ASSOC);

    $mysqli->close();

?>

<html>
  <head>
    <title>challnege1</title>
  </head>
  <body>
    <?php if (isset($error_massage)) : ?>
      <?php echo $error_massage ?>
    <?php endif ?>
    <form method="post" action="">
      <textarea name="comment"></textarea>
      <br>
      <input type="submit" value="Submit">
    </form>
    <?php foreach ($posts as $post) : ?>
      <hr>
      <?php echo nl2br(h($post['comment'])) ?>
      <?php echo h($post['created_at']) ?>
    <?php endforeach ?>
  </body>
</html>