<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<h2>Membership Register</h2>
<form method="post" action="register.php">
  <label for="name">Name</label><br>
  <input type="text" name="name"><br>
  <label for="email">E-mail</label><br>
  <input type="text" name="email"><br>
  <label for="password">Password</label><br>
  <input type="password" name="password"><br>
  <input type="submit" name="confirm" value="Confirm">
  <input type="button" value="Back">
</form>