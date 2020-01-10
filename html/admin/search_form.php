<form method="get" action="index.php">
  <label for="search_conditions[title]">Title : </label>
  <input type="text" name="search_conditions[title]" value="<?php echo h($search_conditions['title']) ?? '' ?>">
  <br>
  <label for="search_conditions[comment]">Comment : </label>
  <input type="text" name="search_conditions[comment]" value="<?php echo h($search_conditions['comment']) ?? '' ?>">
  <br>
  <label for="search_conditions[image]">Image : </label>
  <input type="radio" name="search_conditions[image]" value="with" <?php if (isset($search_conditions['image']) && $search_conditions['image'] === 'with') echo 'checked' ?>>with
  <input type="radio" name="search_conditions[image]" value="without" <?php if (isset($search_conditions['image']) && $search_conditions['image'] === 'without') echo 'checked' ?>>without
  <input type="radio" name="search_conditions[image]" value="unspecified" <?php if (!isset($search_conditions['image']) || $search_conditions['image'] === 'unspecified') echo 'checked' ?>>unspecified
  <br>
  <label for="search_conditions[post]">Post : </label>
  <input type="radio" name="search_conditions[post]" value="on" <?php if (isset($search_conditions['post']) && $search_conditions['post'] === 'on') echo 'checked' ?>>on
  <input type="radio" name="search_conditions[post]" value="delete" <?php if (isset($search_conditions['post']) && $search_conditions['post'] === 'delete') echo 'checked' ?>>delete
  <input type="radio" name="search_conditions[post]" value="unspecified" <?php if (!isset($search_conditions['post']) || $search_conditions['post'] === 'unspecified') echo 'checked' ?>>unspecified
  <br>
  <input type="submit" value="Search">
</form>