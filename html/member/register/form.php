<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<h2>Membership Register</h2>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'error.php') ?>

<form method="post" action="register.php">
  <label for="name">Name</label><br>
  <input type="text" name="name" value="<?php echo isset($name) ? h($name) : '' ?>"><br>
  <label for="email">E-mail</label><br>
  <input type="text" name="email" value="<?php echo isset($email) ? h($email) : '' ?>"><br>
  <label for="password">Password</label><br>
  <input type="password" name="password"><br>
  <input type="submit" name="do_confirm" value="Confirm">
  <input type="button" value="Back" onclick="location.href='index.php'">
</form>