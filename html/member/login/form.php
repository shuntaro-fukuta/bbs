<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<h2>ログイン</h2>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'error.php') ?>

<form method="post" action="login.php">
  <label for="email">Email</label><br>
  <input type="text" name="email"><br>
  <label for="password">Password</label><br>
  <input type="password" name="password"><br>
  <input type="submit" value="ログイン">
</form>