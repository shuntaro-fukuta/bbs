<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<p>Name <?php echo h($name) ?? '' ?></p>
<p>Email <?php echo h($email) ?? '' ?></p>
<p>Password <?php echo h($hidden_pass) ?? '' ?></p>
<form method="post" action="register.php">
  <input type="hidden" name="name" value="<?php echo h($name) ?>">
  <input type="hidden" name="email" value="<?php echo h($email) ?>">
  <input type="hidden" name="password" value="<?php echo h($password) ?>">
  <input type="submit" name="do_register" value="Submit">
  <input type="submit" value="Back">
</form>