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

  function delete_image(post_id) {
    var do_delete = window.confirm(`Are you sure to delete the image of post ${post_id}?`);
    if (do_delete) {
      submit_single_post_form('delete_image.php', post_id);
    }
  }

  function delete_post(post_id) {
    var do_delete = window.confirm(`Are you sure to delete the post ${post_id}?`);
    if (do_delete) {
      submit_single_post_form('delete_posts.php', post_id);
    }
  }

  function recover_post(post_id) {
    submit_single_post_form('recover.php', post_id);
  }

  function submit_single_post_form(action, post_id) {
    document.getElementById('post_id').value = post_id;

    document.js_admin_form.action = action;
    document.js_admin_form.method = 'post';
    document.js_admin_form.submit();
  }

  function delete_checked_posts() {
    var do_delete = window.confirm(`Are you sure to delete checked items?`);
    if (do_delete) {
      var form = document.getElementById('js-checkbox_form');
      form.submit();
    }
  }
</script>

<?php include(HTML_FILES_DIR . DIR_SEP . 'admin' . DIR_SEP . 'search_form.php') ?>

<?php if (empty($records)) : ?>
  <p>Not found.</p>
<?php else : ?>
  <form name="js_admin_form">
    <input type="hidden" id="post_id" name="post_id" value="">

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
              <input class="js-checkboxes" type="checkbox" name="delete_ids[]" value="<?php echo h($record['id']) ?>" form="js-checkbox_form">
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
              <button onclick="delete_image(<?php echo h($record['id']) ?>)">DEL</button>
            <?php endif ?>
          </td>
          <td><?php echo h($record['created_at']) ?></td>

          <td>
            <?php if ($record['is_deleted'] === 0) : ?>
              <button onclick="delete_post(<?php echo h($record['id']) ?>)">DEL</button>
            <?php else : ?>
              <button onclick="recover_post(<?php echo h($record['id']) ?>)">REC</button>
            <?php endif ?>
          </td>

        </tr>
      <?php endforeach ?>
    </table>

    <button onclick="delete_checked_posts('delete_posts');">Delete Checked Items</button>
  </form>

<?php endif ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php') ?>