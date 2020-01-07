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
		<?php if ($record['is_deleted'] === 0) : ?>
			<tr>
				<td><input type="checkbox"></td>
		<?php else : ?>
			<tr style="background: gray;">
				<td></td>
		<?php endif ?>

			<?php foreach ($record as $column => $value) : ?>
				<?php if (in_array($column, $display_columns)) : ?>
					<td>
						<?php if ($column === 'image_path' && !is_null($value)) : ?>
							<form method="post" action="delete.php">
								<img src="<?php echo h($value) ?>" width="200" height="100">
								<input type="button" onclick="deleteConfirm(this.parentNode)" value="DEL">
								<input type="hidden" name="delete_image" value="1">
								<input type="hidden" name="post_id" value="<?php echo h($record['id']) ?>">
								<input type="hidden" name="page" value="<?php echo h($paginator->getCurrentPage()) ?>">
							</form>
						<?php else : ?>
							<?php echo h($value) ?>
						<?php endif ?>
					</td>
				<?php endif ?>
			<?php endforeach ?>

			<td>
				<?php if ($record['is_deleted'] === 0) : ?>
					<form method="post" action="delete.php">
						<input type="button" onclick="deleteConfirm(this.parentNode)" value="DEL">
						<input type="hidden" name="delete_post" value="1">
				<?php else : ?>
					<form method="post" action="recover.php">
						<input type="button" value="REC">
				<?php endif ?>
					<input type="hidden" name="post_id" value="<?php echo h($record['id']) ?>">
					<input type="hidden" name="page" value="<?php echo h($paginator->getCurrentPage()) ?>">
				</form>
			</td>

		</tr>
	<?php endforeach ?>

		<div id="confirm_window" style="display: none;">
			Are you sure?
			<button id="do_delete">OK</button>
			<button id="do_cancel">Cancel</button>
		</div>
</table>

<script>
	var deleteConfirm = function(form) {
		document.getElementById('confirm_window').style.display = 'block';

		document.getElementById('do_delete').onclick = function () {
			form.submit();
		}

		document.getElementById('do_cancel').onclick = function () {
			document.getElementById('confirm_window').style.display = 'none';
		}
	};
</script>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'pager.php') ?>

<?php include(HTML_FILES_DIR . DIR_SEP . 'common' . DIR_SEP . 'footer.php') ?>