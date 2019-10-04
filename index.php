<?php

    function mb_trim($string) {
        // ebine
        // これだと文中の全角スペースも置換されちゃうよ
        $string = mb_convert_kana($string, 's');

        // ebine
        // return 文の上には空行をいれましょう
        return trim($string);
    }

    function h($string) {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    $host     = 'localhost';
    $username = 'root';
    $password = 'root';
    $db_name  = 'bbs';

    $min_comment_length = 10;
    $max_comment_length = 200;

    // ebine
    // new classname の後ろにスペースは必要ないです
    $mysqli = new mysqli ($host, $username, $password, $db_name);

    if ($mysqli->connect_error) {
        // ebine
        // exit() に文字列の引数を渡すのは一般的ではない
        // echo $mysqli->connect_error;
        // exit;
        exit($mysqli->$connect_error);
    }

    // ebine
    // なんでこれだけ変数化されてないの？
    $mysqli->set_charset('utf8');

    // ebine
    // $error_massage = null;
    // と、定義しておくべき

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ebine
        // 処理の順番が不自然
        // trim した結果をDBに入れたいんだから、
        // trim をしてからそれをエスケープするのが自然だよね
        $comment        = $mysqli->real_escape_string($_POST['comment']);
        $comment        = mb_trim($comment);
        $comment_length = mb_strlen($comment);

        if ($comment_length === 0) {
            $error_massage = '何か入力してください。';
        } elseif ($comment_length < $min_comment_length || $comment_length > $max_comment_length) {
            $error_massage = "入力は{$min_comment_length}文字以上{$max_comment_length}文字以内にして下さい。";
        } else {
            $mysqli->query("INSERT INTO posts (comment) VALUES ('$comment')");
            header("Location: {$_SERVER['SCRIPT_NAME']}");

            // ebine
            // マニュアルをちゃんと読みましょう
            // exit; しないと下の処理が継続されます
        }
    }

    // ebine
    // 原則、select 文には id を含める
    $results = $mysqli->query("SELECT id, comment, created_at FROM posts ORDER BY created_at DESC");
    $posts   = $results->fetch_all(MYSQLI_ASSOC);

    $mysqli->close();

?>

<html>
  <head>
    <title>challnege1</title>
  </head>
  <body>
    <?php if (isset($error_massage)) : ?>
      <?php echo $error_massage ?>
    <?php endif ?>
    <form method="post" action="">
      <textarea name="comment"></textarea>
      <br>
      <input type="submit" value="Submit">
    </form>
    <?php foreach ($posts as $post) : ?>
      <hr>
      <?php echo nl2br(h($post['comment'])) ?>
      <!-- ebine 全部、エスケープすること！ -->
      <?php echo h($post['created_at']) ?>
    <?php endforeach ?>
  </body>
</html>