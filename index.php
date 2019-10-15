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

$page_message_count = 10;
$max_pager_count    = 5;

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

$total_message_count = get_total_record_count($mysqli, 'posts');

$posts  = null;
$pagers = null;
if ($total_message_count) {
    $last_page  = (int) ceil($total_message_count / $page_message_count);

    $current_page = get_current_page($last_page);

    $posts = get_page_records($current_page, $mysqli, 'posts', $page_message_count);

    if ($last_page > 1) {
        if ($last_page > $max_pager_count) {
            $pager_count = $max_pager_count;
        } else {
            $pager_count = $last_page;
        }

        $pagers = create_pagers($current_page, $pager_count, $last_page);
    }
}

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
    <?php if (isset($posts)) : ?>
      <?php foreach ($posts as $post) : ?>
        <hr>
        <?php echo h($post['title']) ?>
        <br>
        <?php echo nl2br(h($post['comment'])) ?>
        <?php echo h($post['created_at']) ?>
      <?php endforeach ?>
    <?php endif ?>
    <hr>
    <div>
      <?php if (isset($pagers)) : ?>
        <?php if ($current_page !== 1) : ?>
          <a href="<?php echo $_SERVER['SCRIPT_NAME'] ?>?page=<?php echo $current_page - 1 ?>">&lt;</a>
        <?php endif ?>

        <?php foreach ($pagers as $pager) : ?>
          <?php if ($pager !== $current_page) : ?>
            <a href="<?php echo $_SERVER['SCRIPT_NAME'] ?>?page=<?php echo $pager ?>"><?php echo $pager ?></a>
          <?php else: ?>
            <?php echo $pager ?>
          <?php endif ?>
        <?php endforeach ?>

        <?php if ($current_page !== $last_page) : ?>
          <a href="<?php echo $_SERVER['SCRIPT_NAME'] ?>?page=<?php echo $current_page + 1 ?>">&gt;</a>
        <?php endif ?>
      <?php endif ?>
    </div>
  </body>
</html>