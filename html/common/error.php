<?php if (!empty($error_messages)) : ?>
  <?php foreach ($error_messages as $error_message) : ?>
  <?php echo $error_message ?>
  <br>
  <?php endforeach ?>
<?php endif ?>