<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<script>
  var check_all = function() {
    var isChecked = document.getElementById('all_check_box').checked;

    var check_boxes = document.getElementsByClassName('checkboxes');
    var boxes_count = check_boxes.length;

    for (var i = 0; i < boxes_count; i++) {
      check_boxes[i].checked = isChecked;
    }
  }

  var delete_image = function(post_id) {
    var do_delete = window.confirm(`Are you sure to delete the image of post ${post_id}?`);
    if (do_delete) {
      var form = build_single_post_form('delete_image.php', {'post_id' : post_id});
      form.submit();
    }
  }

  var delete_post = function(post_id) {
    var do_delete = window.confirm(`Are you sure to delete the post ${post_id}?`);
    if (do_delete) {
      var form = build_single_post_form('delete_posts.php', {'delete_ids[]' : post_id});
      form.submit();
    }
  }

  var recover_post = function(post_id) {
    var form = build_single_post_form('recover.php', {'post_id' : post_id});
    form.submit();
  }

  var build_single_post_form = function(action, post_values) {
    var form = document.createElement('form');
    form.setAttribute('method', 'post');
    form.setAttribute('action', action);

    var input = document.createElement('input');
    for (var name in post_values) {
      input.setAttribute('name', name);
      input.setAttribute('value', post_values[name]);
      input.setAttribute('type', 'hidden');

      form.appendChild(input)
    }

    document.body.appendChild(form);

    return form;
  }

  var delete_checked_posts = function() {
    var do_delete = window.confirm(`Are you sure to delete checked items?`);
    if (do_delete) {
      var form = document.getElementById('checkbox_form');
      form.submit();
    }
  }
</script>

<?php include(HTML_FILES_DIR . DIR_SEP . 'admin' . DIR_SEP . 'search_form.php') ?>

<?php if (empty($records)) : ?>
  <p>Not found.</p>
<?php else : ?>
  <table border="1">
    <tr>
      <th><input id="all_check_box" onclick="check_all()" type="checkbox"></th>
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
            <input class="checkboxes" type="checkbox" name="delete_ids[]" value="<?php echo h($record['id']) ?>" form="checkbox_form">
          </td>
      <?php else : ?>
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

  <form id="checkbox_form" method="post" action="delete_posts.php">
    <button onclick="delete_checked_posts('delete_posts')">Delete Checked Items</button>
  </form>
<?php endif ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php') ?>