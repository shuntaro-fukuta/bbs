<?php include(HTML_FILES_DIR . DIR_SEP . 'admin' . DIR_SEP . 'header.php') ?>

<script>
  function check_all() {
    var is_checked = document.getElementById('js-all_check_box').checked;

    var check_boxes = document.getElementsByClassName('js-checkboxes');
    var boxes_count = check_boxes.length;

    for (var i = 0; i < boxes_count; i++) {
      check_boxes[i].checked = is_checked;
    }
  }

  function submit_single_post_form() {
    var do_delete = window.confirm('Are you sure?');
    if (!do_delete) {
      return false;
    }

    // TODO:チェックボックスのチェックを外す

    return true;
  }
</script>

<?php include(HTML_FILES_DIR . DIR_SEP . 'admin' . DIR_SEP . 'search_form.php') ?>

<?php if (empty($records)) : ?>
  <p>Not found.</p>
<?php else : ?>
  <form method="post">
    <table border="1">
      <tr>
        <th><input id="js-all_check_box" onclick="check_all()" type="checkbox"></th>
        <th>id</th>
        <th>title</th>
        <th>comment</th>
        <th>image</th>
        <th>created_at</th>
        <th></th>
      </tr>

      <?php foreach($records as $record) : ?>
        <?php if ($record['is_deleted'] === 0) : ?>
          <tr>
            <td>
              <input class="js-checkboxes" type="checkbox" name="delete_ids[]" value="<?php echo h($record['id']) ?>">
            </td>
        <?php else : ?>
          <!-- doi: style="<?php if ($record['is_deleted']) echo 'background: gray;' ?>" って書けるよ。 -->
          <tr style="background: gray;">
            <td></td>
        <?php endif ?>

          <td><?php echo h($record['id']) ?></td>
          <td><?php echo h($record['title']) ?></td>
          <td><?php echo h($record['comment']) ?></td>
          <td>
            <?php if (!is_null($record['image_path'])) : ?>
              <img src="<?php echo h($record['image_path']) ?>" width="150" height="100">
              <button name="post_id" value="<?php echo h($record['id']) ?>" formaction="delete_image.php"  onclick="return submit_single_post_form()">DEL</button>
            <?php endif ?>
          </td>
          <td><?php echo h($record['created_at']) ?></td>

          <td>
            <?php if ($record['is_deleted'] === 0) : ?>
              <button name="delete_ids[]" value="<?php echo h($record['id']) ?>" formaction="delete_posts.php" onclick="return submit_single_post_form()">DEL</button>
            <?php else : ?>
              <button name="post_id" value="<?php echo h($record['id']) ?>" formaction="recover.php">REC</button>
            <?php endif ?>
          </td>

        </tr>
      <?php endforeach ?>
    </table>

    <input type="submit" value="Delete Checked Items." formaction="delete_posts.php" onclick="return window.confirm(`Are you sure to delete checked items?`)">
  </form>

<?php endif ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php') ?>