<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<form method="get" action="search.php">
  <label for="title_search_string">Title : </label>
  <input type="text" name="title_search_string" value="<?php echo $title_search_string ?? '' ?>">
  <br>
  <label for="comment_search_string">Comment : </label>
  <input type="text" name="comment_search_string" value="<?php echo $comment_search_string ?? '' ?>">
  <br>
  <label for="image_status">Image : </label>
  <input type="radio" name="image_status" value="with" <?php if (isset($image_status) && $image_status === 'with') echo 'checked' ?>>with
  <input type="radio" name="image_status" value="without" <?php if (isset($image_status) && $image_status === 'without') echo 'checked' ?>>without
  <input type="radio" name="image_status" value="unspecified" <?php if(!isset($image_status) || $image_status === 'unspecified') echo 'checked' ?>>unspecified
  <br>
  <label for="post_status">Status : </label>
  <input type="radio" name="post_status" value="on" <?php if (isset($post_status) && $post_status === 'on') echo 'checked' ?>>on
  <input type="radio" name="post_status" value="delete" <?php if (isset($post_status) && $post_status === 'delete') echo 'checked' ?>>delete
  <input type="radio" name="post_status" value="unspecified" <?php if (!isset($post_status) || $post_status === 'unspecified') echo 'checked' ?>>unspecified
  <br>
  <input type="submit" value="Search">
</form>

<?php if (empty($records)) : ?>
  <p>Not found.</p>
<?php else : ?>
  <?php include(HTML_FILES_DIR . DIR_SEP . 'admin/index.php') ?>
<?php endif ?>