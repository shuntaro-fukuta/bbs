  <?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

  <?php include(HTML_FILES_DIR . DIR_SEP . 'post' . DIR_SEP . 'form.php') ?>

  <?php if (!empty($records)) : ?>
    <?php foreach ($records as $record) : ?>
    <hr>
    <?php if (isset($record['name'])) : ?>
      <?php echo $record['name'] ?>
    <?php else : ?>
      No Name
    <?php endif ?>
    <?php if (isset($record['member_id'])) : ?>
      [ ID : <?php echo $record['member_id'] ?> ]
    <?php endif ?>
    <br>
    <?php echo h($record['title']) ?>
    <br>
    <?php echo nl2br(h($record['comment'])) ?>
    <br>
    <?php if (isset($record['image_path'])) : ?>
      <img src="<?php echo $record['image_path'] ?>">
    <?php endif ?>

    <?php if ($is_logged_in) : ?>
      <?php if (isset($record['member_id']) && $record['member_id'] === $member_id)  : ?>
        <form method="post">
          <input type="hidden" name="post_id" value="<?php echo h($record['id']) ?>">
          <input type="hidden" name="previous_page" value="<?php echo h($paginator->getCurrentPage()) ?>">
          <input type="submit" formaction="delete.php" value="Del">
          <input type="submit" formaction="edit.php" value="Edit">
        </form>
      <?php endif ?>
    <?php else : ?>
      <?php if (!isset($record['member_id'])) : ?>
        <form method="post">
          Pass <input type="password" name="password">
          <input type="hidden" name="post_id" value="<?php echo h($record['id']) ?>">
          <input type="hidden" name="previous_page" value="<?php echo h($paginator->getCurrentPage()) ?>">
          <input type="submit" formaction="delete.php" value="Del">
          <input type="submit" formaction="edit.php" value="Edit">
        </form>
      <?php endif ?>
    <?php endif ?>
    <br>
    <?php echo h($record['created_at']) ?>
    <?php endforeach ?>
  <?php endif ?>
  <hr>

  <?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

  <?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php');