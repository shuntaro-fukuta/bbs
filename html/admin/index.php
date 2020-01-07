<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<table border="1">
	<tr>
		<th><input type="checkbox"></th>
		<?php foreach ($display_columns as $column) : ?>
			<th><?php echo h($column) ?></th>
		<?php endforeach ?>
		<th></th>
	</tr>
	<?php foreach($records as $record) : ?>
		<tr>
			<td><input type="checkbox"></td>
			<?php foreach ($record as $column => $value) : ?>
				<?php if ($column === 'image_path' && !is_null($value)) : ?>
					<td>
						<form method="post" action="delete.php">
							<img src="<?php echo h($value) ?>" width="200" height="100">
							<input type="submit" name="delete_image" value="DEL">
							<input type="hidden" name="post_id" value="<?php echo h($record['id']) ?>">
							<input type="hidden" name="page" value="<?php echo h($paginator->getCurrentPage()) ?>">
						</form>
					</td>
				<?php else : ?>
					<td><?php echo h($value) ?></td>
				<?php endif ?>
				<input type="hidden" name="post_id" value="<?php echo h($record['id']) ?>">
			<?php endforeach ?>
			<td>
				<form method="post" action="delete.php">
					<input type="submit" name="delete_post" value="DEL">
					<input type="hidden" name="post_id" value="<?php echo h($record['id']) ?>">
					<input type="hidden" name="page" value="<?php echo h($paginator->getCurrentPage()) ?>">
				</form>
			</td>
		</tr>
	<?php endforeach ?>
</table>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php') ?>