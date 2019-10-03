<?php

    $host     = 'localhost';
    $username = 'root';
    $password = 'root';
    $db_name  = 'bbs';

    $mysqli = new mysqli ($host, $username, $password, $db_name);

    if ($mysqli->connect_error) {
        exit($mysqli->$connect_error);
    }

    $mysqli->set_charset('utf8');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $comment = $mysqli->real_escape_string($_POST['comment']);
        $mysqli->query("INSERT INTO posts (comment) VALUES ('$comment')");
    }

    $mysqli->close();

?>

<html>
  <head>
    <title>challnege1</title>
  </head>
  <body>
    <form method="post" action="">
      <textarea name="comment"></textarea>
      <br>
      <input type="submit" value="Submit">
    </form>
  </body>
</html>