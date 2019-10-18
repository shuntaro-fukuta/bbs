<?php

require_once('functions.php');
require_once('validations.php');
require_once('pagination.php');

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

$pagination_param_name = 'page';

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

$results          = $mysqli->query('SELECT COUNT(*) AS count FROM posts')->fetch_assoc();
$total_post_count = (int) $results['count'];

$pagination = new Pagination($total_post_count);

$current_page = (int) filter_input(INPUT_GET, $pagination_param_name);
$pagination->setCurrentPage($current_page);

$page_numbers = $pagination->getPageNumbers();

$posts = $mysqli->query("
    SELECT *
    FROM posts
    ORDER BY id DESC
    LIMIT {$pagination->getPageItemCount()}
    OFFSET {$pagination->getRecordOffset()}
")->fetch_all(MYSQLI_ASSOC);

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
    <?php if (!empty($posts)) : ?>
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
      <?php if (isset($page_numbers)) : ?>
        <?php if (!($pagination->isFirstPage())) : ?>
<<<<<<< HEAD
          <a href="<?php echo $_SERVER['SCRIPT_NAME'] ?>?page=<?php echo $pagination->getPreviousPage() ?>">&lt;</a>
=======
          <a href="<?php echo $pagination->getPreviousPageUrl($pagination_param_name) ?>">&lt;</a>
>>>>>>> 35dc710... レビューの指摘箇所の修正
        <?php endif ?>

        <?php foreach ($page_numbers as $page_number) : ?>
          <?php if (!($pagination->isCurrentPage($page_number))) : ?>
            <a href="<?php echo $pagination->buildPageUrl($pagination_param_name, $page_number) ?>"><?php echo $page_number ?></a>
          <?php else : ?>
            <?php echo $page_number ?>
          <?php endif ?>
        <?php endforeach ?>

        <?php if (!($pagination->isLastPage())) : ?>
          <a href="<?php echo $pagination->getNextPageUrl($pagination_param_name) ?>">&gt;</a>
        <?php endif ?>
      <?php endif ?>
    </div>
  </body>
</html>