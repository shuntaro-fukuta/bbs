<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>


<table border="1">
  <tr>
    <th><input type="checkbox"></th>
    <?php foreach ($display_columns as $column) : ?>
      <th><?php echo h($column) ?></th>
    <?php endforeach ?>
    <th></th>
  </tr>

  <?php foreach($records as $record) : ?>
    <?php if ($record['is_deleted'] === 0) : ?>
      <tr>
        <td>
          <input type="checkbox" name="delete_ids[]" value="<?php echo h($record['id']) ?>" form="delete_multiple">
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
              <button>DEL</button>
            <?php else : ?>
              <?php echo h($value) ?>
            <?php endif ?>
          </td>
        <?php endif ?>
      <?php endforeach ?>

      <td>
        <?php if ($record['is_deleted'] === 0) : ?>
          <form id="<?php echo $record['id'] ?>" method="post" action="delete.php">
            <input type="button" onclick="deleteConfirm(<?php echo h($record['id']) ?>)" value="DEL">
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

<form id="delete_multiple" method="post" action="delete.php">
  <input type="button" onclick="deleteConfirm(this.parentNode.id)" value="Delete Checked Items">
</form>

<script>
  var deleteConfirm = function(form_id) {
    var do_delete = window.confirm('Are you sure?');
    if (do_delete) {
      var form = document.getElementById(form_id);
      form.submit();
    }
  }
</script>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php') ?>