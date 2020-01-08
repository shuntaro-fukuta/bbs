<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>


<table border="1">
  <tr>
    <th><input id="all_check_box" onclick="allCheck()" type="checkbox"></th>
    <?php foreach ($display_columns as $column) : ?>
      <th><?php echo h($column) ?></th>
    <?php endforeach ?>
    <th></th>
  </tr>

  <?php foreach($records as $record) : ?>
    <?php if ($record['is_deleted'] === 0) : ?>
      <tr>
        <td>
          <input class="checkboxes" type="checkbox" name="delete_ids[]" value="<?php echo h($record['id']) ?>" form="delete_posts">
        </td>
    <?php else : ?>
      <tr style="background: gray;">
        <td></td>
    <?php endif ?>

      <?php foreach ($record as $column => $value) : ?>
        <?php if (in_array($column, $display_columns)) : ?>
          <td>
            <?php if ($column === 'image_path' && !is_null($value)) : ?>
              <form id="image<?php echo h($record['id']) ?>" method="post" action="delete_image.php">
                <img src="<?php echo h($value) ?>" width="150" height="100">
                <input type="button" onclick="deleteImage(<?php echo h($record['id']) ?>)" value="DEL">
                <input type="hidden" name="post_id" value="<?php echo h($record['id']) ?>">
                <input type="hidden" name="page" value="<?php echo h($paginator->getCurrentPage()) ?>">
              </form>
            <?php else : ?>
              <?php echo h($value) ?>
            <?php endif ?>
          </td>
        <?php endif ?>
      <?php endforeach ?>

      <td>
        <?php if ($record['is_deleted'] === 0) : ?>
          <form id="post<?php echo $record['id'] ?>" method="post" action="delete_posts.php">
            <input type="button" onclick="deletePost(<?php echo h($record['id']) ?>)" value="DEL">
            <input type="hidden" name="delete_ids[]" value="<?php echo h($record['id']) ?>">
        <?php else : ?>
          <form method="post" action="recover.php">
            <input type="submit" value="REC">
            <input type="hidden" name="post_id" value="<?php echo h($record['id']) ?>">
        <?php endif ?>
          <input type="hidden" name="page" value="<?php echo h($paginator->getCurrentPage()) ?>">
        </form>
      </td>

    </tr>
  <?php endforeach ?>
</table>

<form id="delete_posts" method="post" action="delete_posts.php">
  <input type="button" onclick="deletePosts('delete_posts')" value="Delete Checked Items">
  <input type="hidden" name="page" value="<?php echo h($paginator->getCurrentPage()) ?>">
</form>

<script>
  var deleteImage = function(post_id) {
    var do_delete = window.confirm(`Are you sure to delete the image of post ${post_id}?`);
    if (do_delete) {
      var form = document.getElementById('image' + post_id);
      form.submit();
    }
  }

  var deletePost = function(post_id) {
    var do_delete = window.confirm(`Are you sure to delete ${post_id}?`);
    if (do_delete) {
      var form = document.getElementById('post' + post_id);
      form.submit();
    }
  }

  var deletePosts = function(form_id) {
    var do_delete = window.confirm(`Are you sure to delete checked items?`);
    if (do_delete) {
      var form = document.getElementById(form_id);
      form.submit();
    }
  }

  var allCheck = function() {
    var isChecked = document.getElementById('all_check_box').checked;

    var check_boxes = document.getElementsByClassName('checkboxes');
    var boxes_count = check_boxes.length;

    for (var i = 0; i < boxes_count; i++) {
      check_boxes[i].checked = isChecked;
    }
  }
</script>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php') ?>