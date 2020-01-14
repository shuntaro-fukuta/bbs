<html>
  <head>
    <title>Challenge9 Admin</title>
  </head>
  <body>
    <?php if ($this->isAdmin()) : ?>
      <input type="button" value="logout" onclick="location.href='logout.php'">
    <?php endif ?>