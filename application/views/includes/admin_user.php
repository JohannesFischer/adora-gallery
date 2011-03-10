<div id="Content">
	
	<div>

		<h1><?=$PageTitle;?></h1>
	
		<div id="User">

			<a href="#"><?=$addUser;?></a>

			<ul>
			<?php foreach($User as $u): ?>
				<li>
					<div>
						<?=img(array(
							'class' => 'Icon',
							'height' => 48,
							'src' => $IconFolder.$u->Icon,
							'width' => 48
						));?>
						<div>
							<span class="ID"><?=$u->ID;?></span>
							<strong class="Username"><?=$u->Username;?></strong>
							<span class="Email"><?=$u->Email;?></span><br/>
							<span class="Role"><?=$u->Role;?></span><br/>
							<a href="#">edit <?=$u->Username;?></a>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>	

	</div>

</div>