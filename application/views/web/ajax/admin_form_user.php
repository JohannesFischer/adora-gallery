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
		<input type="text" name="Username" value="<?php if($Username){ echo $Username;} ?>" placeholder="Username" />
		<input type="text" name="Email" value="<?=$Email;?>" placeholder="Email" />
		<input type="text" name="Role" value="<?=$Role;?>" placeholder="Role" />
		<input type="hidden" name="Icon" value="<?=$Icon;?>" />
		<input type="submit" name="submit-user" value="save" />
	</fieldset>
	
</form>