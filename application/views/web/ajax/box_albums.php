<p><strong>Albums</strong></p>

<?php foreach($Albums as $Album): ?>
	<div class="album">
		<span class="album-thumbnail" style="background-image: url(<?=$ImageFolder.$Album['Filename_Thumbnail'];?>)"></span>
		<a href="#" class="album-link" id="Album_<?=$Album['ID'];?>"><?=$Album['Title'];?></a>
		<span><?=$Album['Photos'];?> Photos</span>
	</div>	
<?php endforeach; ?>