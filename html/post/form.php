<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'error.php') ?>

<?php $_action = (isset($is_edit_form)) ? 'edit.php' : 'post.php' ?>
<form method="post" action="<?php echo $_action ?>" enctype="multipart/form-data">
    <label for="name">Name</label><br>
  <?php if(isset($member_name)) : ?>
    <input type="text" name="name" value="<?php echo h($member_name) ?>"><br>
  <?php else : ?>
    <input type="text" name="name" value="<?php echo isset($name) ? h($name) : '' ?>"><br>
  <?php endif ?>
  <label for="title">Title</label><br>
  <input id="title" type="text" name="title" value="<?php echo isset($title) ? h($title) : '' ?>"><br>
  <label for="comment">Body</label><br>
  <textarea id="comment" name="comment"><?php echo isset($comment) ? h($comment) : '' ?></textarea><br>
  <input type="file" name="image"><br>
  <?php if (isset($is_edit_form)) : ?>
    <?php if (isset($record['image_path'])) : ?>
      <img src="<?php echo $record['image_path'] ?>"><br>
      <input type="checkbox" name="delete_image">Delete Imaege<br>
    <?php endif ?>
    <input type="hidden" name="id" value="<?php echo h($id) ?>">
    <input type="hidden" name="previous_page" value="<?php echo h($previous_page) ?>">
    <input type="hidden" name="password" value="<?php echo h($password) ?>">
    <input type="submit" name="do_edit" value="Submit">
    <input type="button" value="Cancel" onclick="location.href='<?php echo h($previous_page_url) ?>'">
  <?php else : ?>
    <?php if (!isset($member_id)) : ?>
      <label for="password">Password</label>
      <input id="password" type="password" name="password"><br>
    <?php endif ?>
    <input type="submit" value="Submit">
  <?php endif ?>
</form>
