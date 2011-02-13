    <?php if(!$Loggedin): ;?>
        <?=$LoginForm;die('</body></html>')?>
    <?php endif; ?>

	<div id="Wrapper">

		<ul class="tabs">
			<? foreach($Tabs as $link): ;?>
				<li>
					<?=$link;?>
				</li>
			<?php endforeach; ?>
		</ul>