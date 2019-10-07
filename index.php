<?php

function mb_trim($string) {
    return preg_replace('/\A[\p{Z}]+|[\p{Z}]+\z/u', '', $string);
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES);
}

function validate($validations, $inputs) {
    $error_massages = [];

    foreach ($inputs as $attribute_name => $input) {
        $validation_lists = $validations[$attribute_name];

        foreach ($validation_lists as $validation_type => $condition) {
            $error_massage = null;

            switch ($validation_type) {
                case 'required':
                    if ($condition === true && empty($input)) {
                        $error_massage = "{$attribute_name}を入力してください";
                    }
                    break;
                case 'min_length':
                    if (mb_strlen($input) < $condition) {
                        $error_massage = "{$attribute_name}は{$condition}文字以上入力してください";
                    }
                    break;
                case 'max_length':
                    if (mb_strlen($input) > $condition) {
                        $error_massage = "{$attribute_name}は{$condition}文字以内で入力してください";
                    }
                    break;
            }

            if (isset($error_massage)) {
                $error_massages[] = $error_massage;
                break;
            }
        }
    }

    return $error_massages;
}

$host     = 'localhost';
$username = 'root';
$password = 'root';
$db_name  = 'bbs';
$encoding = 'utf8';

$mysqli = new mysqli($host, $username, $password, $db_name);

$validations = [
    'title' => [
        'required'   => true,
        'min_length' => 10,
        'max_length' => 32,
    ],
    'comment' => [
        'required'   => true,
        'min_length' => 10,
        'max_length' => 200,
    ],
];

if ($mysqli->connect_error) {
    echo $mysqli->connect_error;
    exit;
}

$mysqli->set_charset($encoding);

$error_massages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $trimmed_inputs = [];
    foreach ($_POST as $attribute_name => $input) {
        $trimmed_inputs[$attribute_name] = mb_trim($input);
    }

    $error_massages = validate($validations, $trimmed_inputs);

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

$results = $mysqli->query("SELECT * FROM posts ORDER BY created_at DESC");
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