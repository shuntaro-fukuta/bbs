<form method="get" action="index.php">
  Title :
  <input type="text" name="search_conditions[title]" value="<?php echo h($search_conditions['title']) ?? '' ?>">
  <br>
  Comment :
  <input type="text" name="search_conditions[comment]" value="<?php echo h($search_conditions['comment']) ?? '' ?>">
  <br>
  Image :
  <input id="with" type="radio" name="search_conditions[image]" value="with" <?php if (isset($search_conditions['image']) && $search_conditions['image'] === 'with') echo 'checked' ?>>
  <label for="with">with</label>
  <input id="without" type="radio" name="search_conditions[image]" value="without" <?php if (isset($search_conditions['image']) && $search_conditions['image'] === 'without') echo 'checked' ?>>
  <label for="without">without</label>
  <input id="image_unspecified" type="radio" name="search_conditions[image]" value="unspecified" <?php if (!isset($search_conditions['image']) || $search_conditions['image'] === 'unspecified') echo 'checked' ?>>
  <label for="image_unspecified">unspecified</label>
  <br>
  Post :
  <input id="on" type="radio" name="search_conditions[post]" value="on" <?php if (isset($search_conditions['post']) && $search_conditions['post'] === 'on') echo 'checked' ?>>
  <label for="on">on</label>
  <input id="delete" type="radio" name="search_conditions[post]" value="delete" <?php if (isset($search_conditions['post']) && $search_conditions['post'] === 'delete') echo 'checked' ?>>
  <label for="delete">delete</label>
  <input id="radio_unspecified" type="radio" name="search_conditions[post]" value="unspecified" <?php if (!isset($search_conditions['post']) || $search_conditions['post'] === 'unspecified') echo 'checked' ?>>
  <label for="radio_unspecified">unspecified</label>
  <br>
  <input type="submit" value="Search">
</form>