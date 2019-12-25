<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<h2>Membership Register</h2>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'error.php') ?>

<form method="post" action="register.php">
  <label for="name">Name</label><br>
  <input type="text" name="name"><br>
  <label for="email">E-mail</label><br>
  <input type="text" name="email"><br>
  <label for="password">Password</label><br>
  <input type="password" name="password"><br>
  <input type="hidden" name="do_confirm" value="1">

  <input type="submit" value="Confirm">
  <input type="button" value="Back">
</form>