<?php

require_once('dbconnect.php');
require_once('functions.php');
require_once('db_connect.php');
require_once('Validator.php');
require_once('Paginator.php');
require_once('DatabaseOperator.php');

$mysqli      = connect_mysqli();
$db_operator = new DatabaseOperator($mysqli);

$input_keys = ['title', 'comment' , 'password'];

$post_insert_validation_rules = [
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
    $inputs = get_trimmed_inputs($input_keys, $_POST);

    $validator = new Validator();

    try {
        $validator->setAttributeValidationRules($post_insert_validation_rules);
        $error_messages = $validator->validate($inputs);
    } catch (Exception $e) {
        echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
        exit;
    }

    if (empty($error_messages)) {
        $password = $inputs['password'] === '' ? null : password_hash($inputs['password'], PASSWORD_BCRYPT);

        $db_operator->insert([
            'title'    => $inputs['title'],
            'comment'  => $inputs['comment'],
            'password' => $password,
        ]);

        header("Location: {$_SERVER['SCRIPT_NAME']}");
        exit;
    }
}

$results          = $db_operator->select(['column_name' => 'COUNT(*)'])->fetch_assoc();
$total_post_count = (int) $results['COUNT(*)'];

try {
    $paginator = new Paginator($total_post_count);

    $current_page = (int) filter_input(INPUT_GET, $paginator->getPaginationParamName());
    $paginator->setCurrentPage($current_page);

    $page_numbers = $paginator->getPageNumbers();
} catch (Exception $e) {
    echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
    exit;
}

$posts = $db_operator->select([
    'column_name' => '*',
    'order_by'    => 'id DESC',
    'limit'       => $paginator->getPageItemCount(),
    'offset'      => $paginator->getRecordOffset(),
])->fetch_all(MYSQLI_ASSOC);

$mysqli->close();

?>

<html>
  <head>
    <title>challnege5</title>
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
        <form method="post">
	      Pass
          <input type="password" name="password">
          <input type="hidden" name="id" value="<?php echo $post['id'] ?>">
          <input type="hidden" name="previous_page" value="<?php echo $paginator->getCurrentPage() ?>">
          <input type="submit" formaction="delete.php" value="Del">
          <input type="submit" formaction="edit.php" value="Edit">
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
            <a href="<?php echo $paginator->buildPageUrl('page', $page_number) ?>"><?php echo $page_number ?></a>
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
