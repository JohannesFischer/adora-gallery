<p><strong>Albums</strong></p>

<?php foreach($Albums as $Album): ?>
	<div class="album">
        <a href="#" class="album-link" id="Album_<?=$Album['ID'];?>">
            <span class="album-thumbnail" style="background-image: url(<?=$ImageFolder.$Album['Filename_Thumbnail'];?>)" title="<?=$Album['Title'];?>"></span>
        </a>
        <span class="title"><?=$Album['Title'];?></span>
		<span class="hidden"><?=$Album['Photos'];?> Photo<?php if($Album['Photos'] > 1): ?>s<?php endif; ?></span>
	</div>	
<?php endforeach; ?>