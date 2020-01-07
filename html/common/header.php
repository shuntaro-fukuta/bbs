<html>
  <head>
    <title>challnege9</title>
  </head>
  <body>
    <?php if (isset($_SESSION['member_id'])) : ?>
      <input type="button" value="logout" onclick="location.href='logout.php'">
    <?php else : ?>
      <input type="button" value="login" onclick="location.href='login.php'">
      <input type="button" value="register" onclick="location.href='register.php'">
    <?php endif ?>
    <br>