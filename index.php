<?php

require_once('functions.php');
require_once('validations.php');
require_once('paginations.php');

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

$pagination_settings = [
    'db_instance'       => $mysqli,
    'table_name'        => 'posts',
    'page_record_count' => 10,
    'max_pager_count'   => 5,
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

$posts = paginate($pagination_settings);

$mysqli->close();

?>

<html>
  <head>
    <title>challnege3</title>
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
    <?php if (isset($posts['records'])) : ?>
      <?php foreach ($posts['records'] as $post) : ?>
        <hr>
        <?php echo h($post['title']) ?>
        <br>
        <?php echo nl2br(h($post['comment'])) ?>
        <?php echo h($post['created_at']) ?>
      <?php endforeach ?>
    <?php endif ?>
    <hr>
    <div>
      <?php if (isset($posts['pagers'])) : ?>
        <?php foreach ($posts['pagers'] as $pager) : ?>
          <?php echo $pager ?>
        <?php endforeach ?>
      <?php endif ?>
    </div>
  </body>
</html>