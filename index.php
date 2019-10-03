<?php

    $host     = 'localhost';
    $username = 'root';
    $password = 'root';
    $db_name  = 'bbs';

    $max_comment_length = 200;
    $min_comment_length = 10;

    $mysqli = new mysqli ($host, $username, $password, $db_name);

    if ($mysqli->connect_error) {
        exit($mysqli->$connect_error);
    }

    $mysqli->set_charset('utf8');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $comment        = $mysqli->real_escape_string($_POST['comment']);
        $comment_length = mb_strlen($comment);

        if ($comment_length === 0) {
            $error_massage = '何か入力してください。';
        } elseif ($comment_length < $min_comment_length || $comment_length > $max_comment_length) {
            $error_massage = "入力は{$min_comment_length}文字以上{$max_comment_length}文字以内にして下さい。";
        } else {
            $mysqli->query("INSERT INTO posts (comment) VALUES ('$comment')");
            header("Location: {$_SERVER['SCRIPT_NAME']}");
        }
    }

    $results = $mysqli->query("SELECT comment, created_at FROM posts ORDER BY created_at DESC");
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
      <?php echo nl2br(htmlspecialchars($post['comment'], ENT_QUOTES)) ?>
      <?php echo $post['created_at'] ?>
    <?php endforeach ?>
  </body>
</html>