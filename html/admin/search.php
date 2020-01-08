<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<form method="post" action="search.php">
  <label for="title">Title : </label>
  <input type="text" name="title">
  <br>
  <label for="comment">Comment : </label>
  <input type="text" name="comment">
  <br>
  <label for="image">Image : </label>
  <input type="radio" name="image" value="with">with
  <input type="radio" name="image" value="without">without
  <input type="radio" name="image" value="unspecified">unspecified
  <br>
  <label for="status">Status : </label>
  <input type="radio" name="status" value="on">on
  <input type="radio" name="status" value="delete">delete
  <input type="radio" name="status" value="unspecified">unspecified
  <br>
  <input type="submit" value="Search">
</form>