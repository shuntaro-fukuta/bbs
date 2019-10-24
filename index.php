<?php

require_once('dbconnect.php');
require_once('functions.php');
require_once('db_setting.php');
require_once('Validator.php');
require_once('Paginator.php');

/*

$posts = new Posts();

$posts->insert([
    'title' => $title,
    'comment' => $comment,
]);

$posts->delete($where);
$posts->deleteById($id);

$posts->update($where, [

]);

$rows = $posts->select(...);

*/

// ebine
// 下のコードはよく使うコード。なので毎回書きたくない。
// なので関数にする。
/*
引数は全部オプショナル。もしくは配列でもいいかも。
渡されなかったものは、定数を使えばいいよね。
function connect_mysqli($host, $username, $password, $dbname, $charset) {

}

$mysqli = connect_mysql();
*/
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->set_charset(DB_ENCODING);

$bbs_post_validation_rules = [
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

    $validator = new Validator();
    $validator->setAttributeValidationRules($bbs_post_validation_rules);
    $error_messages = $validator->validate($inputs);

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

$results          = $mysqli->query('SELECT COUNT(*) AS count FROM posts')->fetch_assoc();
$total_post_count = (int) $results['count'];

$paginator = new Paginator($total_post_count);

$current_page = (int) filter_input(INPUT_GET, $paginator->getPaginationParamName());
$paginator->setCurrentPage($current_page);

$page_numbers = $paginator->getPageNumbers();

$posts = $mysqli->query("
    SELECT *
    FROM posts
    ORDER BY id DESC
    LIMIT {$paginator->getPageItemCount()}
    OFFSET {$paginator->getRecordOffset()}
")->fetch_all(MYSQLI_ASSOC);

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
          <input type="password" name="password">
          <input type="hidden" name="id" value="<?php echo $post['id'] ?>">
          <input type="hidden" name="previous_page" value="<?php echo $paginator->getCurrentPage() ?>">
	      <input type="submit" value="Del">
        </form>
        <?php echo h($post['created_at']) ?>
      <?php endforeach ?>
    <?php endif ?>
    <hr>
    <div>
      <?php if (isset($page_numbers)) : ?>
        <?php if (!($paginator->isFirstPage())) : ?>
          <a href="<?php echo $paginator->getPreviousPageUrl() ?>">&lt;</a>
        <?php endif ?>

        <?php foreach ($page_numbers as $page_number) : ?>
          <?php if (!($paginator->isCurrentPage($page_number))) : ?>
            <a href="<?php echo $paginator->buildPageUrl($page_number) ?>"><?php echo $page_number ?></a>
          <?php else : ?>
            <?php echo $page_number ?>
          <?php endif ?>
        <?php endforeach ?>

        <?php if (!($paginator->isLastPage())) : ?>
          <a href="<?php echo $paginator->getNextPageUrl() ?>">&gt;</a>
        <?php endif ?>
      <?php endif ?>
    </div>
  </body>
</html>
