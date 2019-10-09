<?php

require_once('functions.php');
require_once('validations.php');

$host     = 'localhost';
$username = 'root';
$password = 'root';
$db_name  = 'bbs';
$encoding = 'UTF-8';

$mysqli = new mysqli($host, $username, $password, $db_name);

if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->set_charset($encoding);

$bbs_post_validation_settings = [
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

$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs = [];
    foreach ($_POST as $attribute_name => $input) {
        $inputs[$attribute_name] = mb_trim($input);
    }

    $error_messages = execute_validations($bbs_post_validation_settings, $inputs);

    if (empty($error_messages)) {
        $title   = $mysqli->real_escape_string($inputs['title']);
        $comment = $mysqli->real_escape_string($inputs['comment']);

        $mysqli->query("INSERT INTO posts (title, comment) VALUES ('{$title}', '{$comment}')");

        header("Location: {$_SERVER['SCRIPT_NAME']}");
        exit;
    } else {
        if (isset($inputs['title'])) {
            $title = $inputs['title'];
        }
        if (isset($inputs['comment'])) {
            $comment = $inputs['comment'];
        }
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
    <?php if (!empty($error_messages)) : ?>
      <?php foreach ($error_messages as $error_message) : ?>
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