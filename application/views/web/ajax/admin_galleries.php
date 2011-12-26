<ul class="zebra">
<?php foreach($Galleries as $Gallery): ?>
	<li>
		<span><?=$Gallery['Title'];?></span>
        <span><?=$Gallery['Date'];?></span>
	</li>
<?php endforeach; ?>
</ul>