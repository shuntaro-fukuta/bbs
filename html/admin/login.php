<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'error.php') ?>

<form method="post" action="login.php">
  <label for="id">ID : </label>
  <input type="text" name="id"><br>
  <label for="password">PASSWORD : </label>
  <input type="password" name="password"><br>
  <input type="submit" value="login">
</form>