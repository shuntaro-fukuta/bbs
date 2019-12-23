<html>
  <body>
    <?php if (!$exists_password) : ?>
      <p>この投稿にはパスワードが設定されていないため、削除できません。</p>
      <p><?php echo h($record['title']) ?></p>
      <p><?php echo h($record['comment']) ?></p>
      <?php if (isset($record['image_path'])) : ?>
        <img src="<?php echo $record['image_path'] ?>"><br>
      <?php endif ?>
      <p><?php echo h($record['created_at']) ?></p>
      <a href="<?php echo $previous_page_url ?>">前のページへ戻る</a>
    <?php elseif (!$is_correct_password) : ?>
      <p>パスワードが間違っています。もう一度入力してください</p>
      <p><?php echo h($record['title']) ?></p>
      <p><?php echo h($record['comment']) ?></p>
      <?php if (isset($record['image_path'])) : ?>
        <img src="<?php echo $record['image_path'] ?>"><br>
      <?php endif ?>
      <p><?php echo h($record['created_at']) ?></p>
      <form method="post" action="">
        Pass
        <input type="password" name="password">
        <input type="hidden" name="id" value="<?php echo h($_POST['id']) ?>">
        <input type="hidden" name="previous_page" value="<?php echo h($previous_page) ?>">
        <input type="submit" value="Edit">
      </form>
    <?php else : ?>

      <?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'error.php') ?>

      <form method="POST" action="" enctype="multipart/form-data">
        <label for="title">Title</label><br>
        <input id="title" type="text" name="title" value="<?php echo isset($inputs['title']) ? h($inputs['title']) : h($record['title']) ?>"><br>
        <label for="comment">Body</label><br>
        <textarea id="comment" name="comment"><?php echo isset($inputs['comment']) ? h($inputs['comment']) : h($record['comment']) ?></textarea><br>
        <?php if (isset($record['image_path'])) : ?>
          <img src="<?php echo $record['image_path'] ?>"><br>
          <input type="checkbox" name="delete_image">Delete Imaege<br>
        <?php endif ?>
        <input type="file" name="image"><br>
        <br>
        <input type="hidden" name="id" value="<?php echo h($_POST['id']) ?>">
        <input type="hidden" name="previous_page" value="<?php echo h($previous_page) ?>">
        <input type="hidden" name="password" value="<?php echo h($_POST['password']) ?>">
        <input type="submit" name="do_edit" value="Submit">
        <input type="button" value="Cancel" onclick="location.href='<?php echo h($previous_page_url) ?>'">
      </form>
    <?php endif ?>
  </body>
</html>