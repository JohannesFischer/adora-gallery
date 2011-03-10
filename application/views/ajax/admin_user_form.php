<ul class="user-icons">
	<?php foreach($Icons as $icon): ?>
	<li>
		<?=img(array(
			'height' => 48,
			'src' => $icon,
			'width' => 48
		));?>
	</li>
	<?php endforeach; ?>
</ul>

<form method="post" action="#">

	<fieldset>
		<input type="text" name="Username" value="<?=$Username;?>" />
		<input type="text" name="Email" value="<?=$Email;?>" />
		<input type="text" name="Role" value="<?=$Role;?>" />
		<input type="hidden" name="Icon" value="<?=$Icon;?>" />
		<input type="submit" name="submit-user" value="save" />
	</fieldset>
	
</form>