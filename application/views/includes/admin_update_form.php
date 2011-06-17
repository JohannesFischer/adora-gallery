<?=img(array(
	'src' => $file
));?>
<form method="post" action="#">
	<fieldset>
		<label>Title</label>
		<input type="text" name="Title" placeholder="Title" />
		<label>Description</label>
		<textarea name="Description" cols="45" rows="5"></textarea>
		<input type="hidden" name="Filename" value="<?=$source_file;?>" />
		<?php if($exif['FileDateTime']): ?>
		<input type="hidden" name="FileDateTime" value="<?=$exif['FileDateTime'];?>" />
		<input type="hidden" name="Orientation" value="<?=$exif['Orientation'];?>" />
		<?php endif; ?>
		<input type="submit" name="save_image" value="save" />
	</fieldset>
</form>