<a href="#" class="close"></a>

<p><strong>Albums</strong></p>

<?php foreach($Albums as $Album): ?>
	<div class="album">
		<a href="#"><?=$Album['Title'];?></a>
		<span><?=$Album['Photos'];?> Photos</span>
	</div>	
<?php endforeach; ?>