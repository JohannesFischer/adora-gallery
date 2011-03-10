<div id="Content">

	<div>

		<h1><?=$PageTitle;?></h1>
	
		<div id="AddImages">            
			<?php
				$i = 0;
			?>
			<ul>
			<?php foreach($Files as $file): ?>
				<li>
					<a href="<?=$file['filename'];?>"><?=$file['filename'];?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	
	</div>
	
</div>