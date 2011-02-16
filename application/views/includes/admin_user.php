<div id="Content">

	<h1><?=$PageTitle;?></h1>

	<div id="User">
		<ul>
		<?php foreach($User as $u): ?>
			<li>
				<?=img(array(
					'src' => $IconFolder.$u->Icon
				));?>
				<strong><?=$u->Username;?></strong>
				<span><?=$u->Email;?></span>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>

</div>