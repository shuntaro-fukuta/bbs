<?php

require_once('dbconnect.php');
require_once('functions.php');
require_once('validations.php');
require_once('pagination.php');
require_once('db_setting.php');

$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->set_charset($db_encoding);

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
    'password' => [
        'required' => false,
        'digit'    => 4,
    ],
];

$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs = [];
    foreach ($_POST as $attribute_name => $input) {
        $inputs[$attribute_name] = mb_trim($input);
    }

    $title   = $inputs['title'];
    $comment = $inputs['comment'];

    if (empty($error_messages)) {
        if (empty($inputs['password'])) {
            $password = null;
        } else {
            $password = password_hash($inputs['password'], PASSWORD_BCRYPT);
        }

        $title    = $mysqli->real_escape_string($inputs['title']) ;
        $comment  = $mysqli->real_escape_string($inputs['comment']);
        $password = $mysqli->real_escape_string($password);

        $mysqli->query("INSERT INTO posts (title, comment, password) VALUES ('{$title}', '{$comment}', '{$password}')");

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
    <title>challnege4</title>
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
      <label for="password">Password</label>
      <input id="password" type="password" name="password"><br>
      <input type="submit" value="Submit">
    </form>
    <?php if (!empty($posts)) : ?>
      <?php foreach ($posts as $post) : ?>
        <hr>
        <?php echo h($post['title']) ?>
        <br>
        <?php echo nl2br(h($post['comment'])) ?>
        <br>
        <form method="post" action="delete.php">
	      Pass
          <input type="password" name="delete_password">
          <input type="hidden" name="id" value="<?php echo $post['id'] ?>">
          <input type="hidden" name="previous_page_url" value="<?php echo $pagination->buildPageUrl($pagination->getCurrentPage()) ?>">
	      <input type="submit" value="Del">
        </form>
        <?php echo h($post['created_at']) ?>
      <?php endforeach ?>
    <?php endif ?>
    <hr>
    <div>
      <?php if (isset($page_numbers)) : ?>
        <?php if (!($pagination->isFirstPage())) : ?>
          <a href="<?php echo $pagination->getPreviousPageUrl() ?>">&lt;</a>
        <?php endif ?>

        <?php foreach ($page_numbers as $page_number) : ?>
          <?php if (!($pagination->isCurrentPage($page_number))) : ?>
            <a href="<?php echo $pagination->buildPageUrl($page_number) ?>"><?php echo $page_number ?></a>
          <?php else : ?>
            <?php echo $page_number ?>
          <?php endif ?>
        <?php endforeach ?>

        <?php if (!($pagination->isLastPage())) : ?>
          <a href="<?php echo $pagination->getNextPageUrl() ?>">&gt;</a>
        <?php endif ?>
      <?php endif ?>
    </div>
  </body>
</html>
