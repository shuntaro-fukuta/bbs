<?php

require_once('dbconnect.php');
require_once('functions.php');
require_once('db_connect.php');
require_once('Validator.php');
require_once('Paginator.php');
require_once('Posts.php');

try {
    $mysqli = connect_mysqli();
} catch (Exception $e) {
    echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
    exit;
}

$posts  = new Posts($mysqli);

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
        if ($inputs['password'] !== '') {
            $inputs['password'] = password_hash($inputs['password'], PASSWORD_BCRYPT);
        }

        try {
            $posts->insert([
                'title'    => $inputs['title'],
                'comment'  => $inputs['comment'],
                'password' => $inputs['password'],
            ]);
        } catch (Exception $e) {
            echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
            exit;
        }

        header("Location: {$_SERVER['SCRIPT_NAME']}");
        exit;
    }
}

try {
    $total_record_count = $posts->count();

    $paginator = new Paginator($total_record_count);

    $current_page = (int) filter_input(INPUT_GET, $paginator->getPaginationParamName());
    $paginator->setCurrentPage($current_page);

    $page_numbers = $paginator->getPageNumbers();

    $records = $posts->selectRecords(['*'], [
        'order_by' => 'id DESC',
        'limit'    => $paginator->getPageItemCount(),
        'offset'   => $paginator->getRecordOffset(),
    ]);
} catch (Exception $e) {
    echo "{$e->getMessage()} ({$e->getFile()} : {$e->getLine()})";
    exit;
}


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
    <?php if (!empty($records)) : ?>
      <?php foreach ($records as $record) : ?>
        <hr>
        <?php echo h($record['title']) ?>
        <br>
        <?php echo nl2br(h($record['comment'])) ?>
        <br>
        <form method="post">
	      Pass
          <input type="password" name="password">
          <input type="hidden" name="id" value="<?php echo h($record['id']) ?>">
          <input type="hidden" name="previous_page" value="<?php echo h($paginator->getCurrentPage()) ?>">
          <input type="submit" formaction="delete.php" value="Del">
          <input type="submit" formaction="edit.php" value="Edit">
        </form>
        <?php echo h($record['created_at']) ?>
      <?php endforeach ?>
    <?php endif ?>
    <hr>
    <div>
      <?php if (isset($page_numbers)) : ?>
        <?php if (!($paginator->isFirstPage())) : ?>
          <a href="<?php echo $paginator->getPreviousPageUrl('page') ?>">&lt;</a>
        <?php endif ?>

        <?php foreach ($page_numbers as $page_number) : ?>
          <?php if (!($paginator->isCurrentPage($page_number))) : ?>
            <a href="<?php echo $paginator->buildPageUrl('page', $page_number) ?>"><?php echo $page_number ?></a>
          <?php else : ?>
            <?php echo $page_number ?>
          <?php endif ?>
        <?php endforeach ?>

        <?php if (!($paginator->isLastPage())) : ?>
          <a href="<?php echo $paginator->getNextPageUrl('page') ?>">&gt;</a>
        <?php endif ?>
      <?php endif ?>
    </div>
  </body>
</html>
