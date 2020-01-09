<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<form method="post" action="search.php">
  <label for="title_search_string">Title : </label>
  <input type="text" name="title_search_string">
  <br>
  <label for="comment_search_string">Comment : </label>
  <input type="text" name="comment_search_string">
  <br>
  <label for="image_status">Image : </label>
  <input type="radio" name="image_status" value="with">with
  <input type="radio" name="image_status" value="without">without
  <input type="radio" name="image_status" value="unspecified" checked>unspecified
  <br>
  <label for="post_status">Status : </label>
  <input type="radio" name="post_status" value="on">on
  <input type="radio" name="post_status" value="delete">delete
  <input type="radio" name="post_status" value="unspecified" checked>unspecified
  <br>
  <input type="submit" value="Search">
</form>

<?php if (empty($records)) : ?>
  <p>Not found.</p>
<?php else : ?>
<?php endif ?>

