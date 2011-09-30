<?=img(array(
	'src' => $file
));?>
<form method="post" action="#">
	<fieldset>
		<label>Title</label>
		<input type="text" name="Title" placeholder="Title" />
		<label>Album</label>
		<select name="Album" size="1">
				<option value="0">Select a album</option>
				<option value="0" class="icon new-album">New Album</option>
			<?php foreach($Albums as $album): ?>
				<option value="<?=$album['ID'];?>"><?=$album['Title'];?></option>
			<?php endforeach; ?>
		</select>
		<label>Description</label>
		<textarea name="Description" placeholder="Description" cols="45" rows="5"></textarea>
		<input type="hidden" name="Filename" value="<?=$source_file;?>" />
		<?php if($exif['DateTimeOrigina']): ?>
		<input type="hidden" name="FileDateTime" value="<?=$exif['DateTimeOrigina'];?>" />
		<?php endif; ?>
		<input type="hidden" name="Orientation" value="<?=$exif['Orientation'];?>" />
		<input type="submit" name="save_image" value="save" />
	</fieldset>
</form>

<a href="#" class="delete icon">delete image</a>