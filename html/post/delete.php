  <?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>
    <?php if (!$is_logged_in && !$exists_password) : ?>
      <p>この投稿にはパスワードが設定されていないため、削除できません。</p>
      <p>
        <?php if (isset($record['name'])) : ?>
          <?php echo h($record['name']) ?>
        <?php else : ?>
          No Name
        <?php endif ?>
        <?php if (isset($record['member_id'])) : ?>
          [ ID : <?php echo h($record['member_id']) ?> ]
        <?php endif ?>
      </p>
      <p><?php echo h($record['title']) ?></p>
      <p><?php echo h($record['comment']) ?></p>
      <p><?php echo h($record['created_at']) ?></p>
      <a href="<?php echo $previous_page_url ?>">前のページへ戻る</a>
    <?php elseif (!$is_logged_in && !$is_correct_password) : ?>
      <p>パスワードが間違っています。もう一度入力してください</p>
      <p>
        <?php if (isset($record['name'])) : ?>
          <?php echo h($record['name']) ?>
        <?php else : ?>
          No Name
        <?php endif ?>
        <?php if (isset($record['member_id'])) : ?>
          [ ID : <?php echo h($record['member_id']) ?> ]
        <?php endif ?>
      </p>
      <p><?php echo h($record['title']) ?></p>
      <p><?php echo h($record['comment']) ?></p>
      <p><?php echo h($record['created_at']) ?></p>
      <form method="post" action="">
        Pass
        <input type="password" name="password">
        <input type="hidden" name="post_id" value="<?php echo h($post_id) ?>">
        <input type="hidden" name="previous_page" value="<?php echo h($previous_page) ?>">
        <input type="submit" value="Del">
      </form>
    <?php else : ?>
        <p>
        <?php if (isset($record['name'])) : ?>
          <?php echo h($record['name']) ?>
        <?php else : ?>
          No Name
        <?php endif ?>
        <?php if (isset($record['member_id'])) : ?>
          [ ID : <?php echo h($record['member_id']) ?> ]
        <?php endif ?>
      </p>
    <p><?php echo h($record['title']) ?></p>
    <p><?php echo h($record['comment']) ?></p>
    <p><?php echo h($record['created_at']) ?></p>
      <p>削除してよろしいですか？</p>
      <form method="post" action="">
        <input type="hidden" name="post_id" value="<?php echo h($post_id) ?>">
        <input type="hidden" name="previous_page" value="<?php echo h($previous_page) ?>">
        <input type="hidden" name="password" value="<?php echo h($password) ?>">
        <input type="submit" name="do_delete" value="Yes">
        <input type="button" value="Cancel" onclick="location.href='<?php echo h($previous_page_url) ?>'">
      </form>
    <?php endif ?>

    <?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php') ?>