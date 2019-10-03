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

    $mysqli->close();

?>

<html>
  <head>
    <title>challnege1</title>
  </head>
  <body>
    <form>
      <textarea>
      </textarea>
      <br>
      <input type="submit" value="Submit">
    </form>
  </body>
</html>