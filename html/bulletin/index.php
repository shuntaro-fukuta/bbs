<html>
  <head>
    <title>challnege8</title>
  </head>
  <body>

    <?php if (!empty($error_messages)) : ?>
      <?php foreach ($error_messages as $error_message) : ?>
        <?php echo $error_message ?>
        <br>
      <?php endforeach ?>
    <?php endif ?>

    <form method="post" action="" enctype="multipart/form-data">
      <label for="title">Title</label><br>
      <input id="title" type="text" name="title" value="<?php echo isset($title) ? h($title) : '' ?>"><br>
      <label for="comment">Body</label><br>
      <textarea id="comment" name="comment"><?php echo isset($comment) ? h($comment) : '' ?></textarea><br>
      <input type="file" name="image">
      <br>
      <label for="password">Password</label>
      <input id="password" type="password" name="password"><br>
      <input type="submit" value="Submit">
    </form>

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

    <div>
      <?php if (isset($page_numbers)) : ?>
        <?php if (!($paginator->isFirstPage())) : ?>
          <a href="<?php echo $paginator->getPreviousPageUrl() ?>">&lt;</a>
        <?php endif ?>

        <?php foreach ($page_numbers as $page_number) : ?>
          <?php if (!($paginator->isCurrentPage($page_number))) : ?>
            <a href="<?php echo $paginator->buildPageUrl($page_number) ?>"><?php echo $page_number ?></a>
          <?php else : ?>
            <?php echo $page_number ?>
          <?php endif ?>
        <?php endforeach ?>

        <?php if (!($paginator->isLastPage())) : ?>
          <a href="<?php echo $paginator->getNextPageUrl() ?>">&gt;</a>
        <?php endif ?>
      <?php endif ?>
    </div>
  </body>
</html>