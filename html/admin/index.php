<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<form method="get" action="index.php">
  <label for="search_conditions[title]">Title : </label>
  <input type="text" name="search_conditions[title]" value="<?php echo h($search_conditions['title']) ?? '' ?>">
  <br>
  <label for="search_conditions[comment]">Comment : </label>
  <input type="text" name="search_conditions[comment]" value="<?php echo h($search_conditions['comment']) ?? '' ?>">
  <br>
  <label for="search_conditions[image]">Image : </label>
  <input type="radio" name="search_conditions[image]" value="with" <?php if (isset($search_conditions['image']) && $search_conditions['image'] === 'with') echo 'checked' ?>>with
  <input type="radio" name="search_conditions[image]" value="without" <?php if (isset($search_conditions['image']) && $search_conditions['image'] === 'without') echo 'checked' ?>>without
  <input type="radio" name="search_conditions[image]" value="unspecified" <?php if (!isset($search_conditions['image']) || $search_conditions['image'] === 'unspecified') echo 'checked' ?>>unspecified
  <br>
  <label for="post">Post : </label>
  <input type="radio" name="search_conditions[post]" value="on" <?php if (isset($search_conditions['post']) && $search_conditions['post'] === 'on') echo 'checked' ?>>on
  <input type="radio" name="search_conditions[post]" value="delete" <?php if (isset($search_conditions['post']) && $search_conditions['post'] === 'delete') echo 'checked' ?>>delete
  <input type="radio" name="search_conditions[post]" value="unspecified" <?php if (!isset($search_conditions['post']) || $search_conditions['post'] === 'unspecified') echo 'checked' ?>>unspecified
  <br>
  <input type="submit" value="Search">
</form>

<?php if (empty($records)) : ?>
  <p>Not found.</p>
<?php else : ?>
  <form id="form" method="post">
    <table border="1">
      <tr>
        <th><input id="all_check_box" onclick="check_all()" type="checkbox"></th>
        <?php foreach ($display_columns as $column) : ?>
          <th><?php echo h($column) ?></th>
        <?php endforeach ?>
        <th></th>
      </tr>

      <?php foreach($records as $record) : ?>
        <?php if ($record['is_deleted'] === 0) : ?>
          <tr>
            <td>
              <input class="checkboxes" type="checkbox" name="delete_ids[]" value="<?php echo h($record['id']) ?>">
            </td>
        <?php else : ?>
          <tr style="background: gray;">
            <td></td>
        <?php endif ?>

          <?php foreach ($record as $column => $value) : ?>
            <?php if (in_array($column, $display_columns)) : ?>
              <td>
                <?php if ($column === 'image_path' && !is_null($value)) : ?>
                  <img src="<?php echo h($value) ?>" width="150" height="100">
                  <input type="button" onclick="delete_image(<?php echo h($record['id']) ?>)" value="DEL">
                <?php else : ?>
                  <?php echo h($value) ?>
                <?php endif ?>
              </td>
            <?php endif ?>
          <?php endforeach ?>

          <td>
            <?php if ($record['is_deleted'] === 0) : ?>
              <input type="button" onclick="delete_post(<?php echo h($record['id']) ?>)" value="DEL">
            <?php else : ?>
              <input type="button" onclick="recover_post(<?php echo h($record['id']) ?>)" value="REC">
            <?php endif ?>
          </td>

        </tr>
      <?php endforeach ?>
    </table>

    <input type="button" onclick="delete_posts('delete_posts')" value="Delete Checked Items">

    <input type="hidden" name="page" value="<?php echo h($paginator->getCurrentPage()) ?>">
    <?php if (isset($search_conditions)) : ?>
      <input type="hidden" name="search_conditions[title]"   value="<?php echo h($search_conditions['title'])   ?? null ?>">
      <input type="hidden" name="search_conditions[comment]" value="<?php echo h($search_conditions['comment']) ?? null ?>">
      <input type="hidden" name="search_conditions[image]"   value="<?php echo h($search_conditions['image'])   ?? null ?>">
      <input type="hidden" name="search_conditions[post]"    value="<?php echo h($search_conditions['post'])    ?? null ?>">
    <?php endif ?>
  </form>
<?php endif ?>


<script>
  var delete_image = function(post_id) {
    var do_delete = window.confirm(`Are you sure to delete the image of post ${post_id}?`);
    if (do_delete) {
      var form = document.getElementById('form');
      form.setAttribute('action', 'delete_image.php');

      var input = document.createElement('input');
      input.setAttribute('name', 'post_id');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', post_id);
      form.appendChild(input);

      form.submit();
    }
  }

  var delete_post = function(post_id) {
    var do_delete = window.confirm(`Are you sure to delete the post ${post_id}?`);
    if (do_delete) {
      var form = document.getElementById('form');
      form.setAttribute('action', 'delete_posts.php');

      var input = document.createElement('input');
      input.setAttribute('name', 'delete_ids[]');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', post_id);
      form.appendChild(input);

      form.submit();
    }
  }

  var delete_posts = function() {
    var do_delete = window.confirm(`Are you sure to delete checked items?`);
    if (do_delete) {
      var form = document.getElementById('form');
      form.setAttribute('action', 'delete_posts.php');

      form.submit();
    }
  }

  var recover_post = function(post_id) {
      var form = form = document.getElementById('form');
      form.setAttribute('action', 'recover.php');

      var input = document.createElement('input');
      input.setAttribute('name', 'post_id');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', post_id);
      form.appendChild(input);

      form.submit();
  }

  var check_all = function() {
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