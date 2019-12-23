<html>
  <head>
    <title>challnege8</title>
  </head>
  <body>

  <?php include(HTML_FILES_DIR . DIR_SEP . 'bulletin' . DIR_SEP . 'form.php') ?>

  <?php if (!empty($records)) : ?>
    <?php foreach ($records as $record) : ?>
    <hr>
    <?php echo h($record['title']) ?>
    <br>
    <?php echo nl2br(h($record['comment'])) ?>
    <br>
    <?php if (isset($record['image_path'])) : ?>
      <img src="<?php echo $record['image_path'] ?>">
    <?php endif ?>
    <form method="post">
      Pass
      <input type="password" name="password">
      <input type="hidden" name="id" value="<?php echo h($record['id']) ?>">
      <input type="hidden" name="previous_page" value="<?php echo h($paginator->getCurrentPage()) ?>">
      <input type="submit" formaction="delete.php" value="Del">
      <input type="submit" formaction="edit.php" value="Edit">
    </form>
    <?php echo h($record['created_at']) ?>
    <?php endforeach ?>
  <?php endif ?>
  <hr>

  <?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

  </body>
</html>