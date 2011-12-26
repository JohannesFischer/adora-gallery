<div id="Content">

	<div>

		<h1><?=$PageTitle;?></h1>
	
		<p><?=$Text;?></p>
	
        <a href="#" id="ToggleUploadForm">Upload Images</a>
    
		<div id="AddImages">            
			<?php
				$i = 0;
			?>
			<ul class="zebra">
			<?php foreach($Files as $file): ?>
				<li>
					<a href="<?=$file['filename'];?>" class="icon image"><?=$file['filename'];?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	
	</div>
	
</div>