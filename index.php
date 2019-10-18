<?php

require_once('dbconnect.php');
require_once('functions.php');
require_once('validations.php');

$error_massages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs = [];
    foreach ($_POST as $attribute_name => $input) {
        $inputs[$attribute_name] = mb_trim($input);
    }

    $title   = $inputs['title'];
    $comment = $inputs['comment'];

    $error_massages = execute_validation($validations, $inputs);

    if (empty($error_massages)) {
        $title   = $mysqli->real_escape_string($title);
        $comment = $mysqli->real_escape_string($comment);

        $mysqli->query("INSERT INTO posts (title, comment) VALUES ('{$title}', '{$comment}')");

        header("Location: {$_SERVER['SCRIPT_NAME']}");
        exit;
    }
}

$results = $mysqli->query('SELECT * FROM posts ORDER BY id DESC');
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
      <input id="title" type="text" name="title" value="<?php echo isset($title) ? h($title) : '' ?>"><br>
      <label for="comment">Body</label><br>
      <textarea id="comment" name="comment"><?php echo isset($comment) ? h($comment) : '' ?></textarea><br>
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
</html>
