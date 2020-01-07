<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'header.php') ?>

<form method="post">
	<table border="1">
		<tr>
			<th><input type="checkbox"></th>
			<?php foreach ($display_columns as $column) : ?>
				<th><?php echo $column ?></th>
			<?php endforeach ?>
			<th></th>
		</tr>
		<?php foreach($records as $record) : ?>
			<tr>
				<td><input type="checkbox"></td>
				<?php foreach ($record as $column => $value) : ?>
					<?php if ($column === 'image_path' && !is_null($value)) : ?>
						<td>
							<img src="<?php echo $value ?>" width="200" height="100">
							<input type="submit" value="DEL"></input>
						</td>
					<?php else : ?>
						<td><?php echo $value ?></td>
					<?php endif ?>
				<?php endforeach ?>
				<td><input type="submit" value="DEL"></input></td>
			</tr>
		<?php endforeach ?>
	</table>
	<input type="hidden" value="<?php echo h($paginator->getCurrentPage()) ?>">
</form>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php') ?>