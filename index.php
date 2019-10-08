<?php

require_once('dbconnect.php');
require_once('functions.php');
require_once('validations.php');

$error_massages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $trimmed_inputs = [];
    foreach ($_POST as $attribute_name => $input) {
        $trimmed_inputs[$attribute_name] = mb_trim($input);
    }

    $error_massages = validate($validation_settings, $trimmed_inputs);

    if (empty($error_massages)) {
        $title   = $mysqli->real_escape_string($trimmed_inputs['title']);
        $comment = $mysqli->real_escape_string($trimmed_inputs['comment']);
        $mysqli->query("INSERT INTO posts (title, comment) VALUES ('$title', '$comment')");

        header("Location: {$_SERVER['SCRIPT_NAME']}");
        exit;
    } else {
        session_start();
        $_SESSION['title']   = $trimmed_inputs['title'];
        $_SESSION['comment'] = $trimmed_inputs['comment'];
    }
}

$results = $mysqli->query("SELECT * FROM posts ORDER BY id DESC");
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