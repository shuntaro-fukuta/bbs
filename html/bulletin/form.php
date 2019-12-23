<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'error.php') ?>

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