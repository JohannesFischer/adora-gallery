	<?php if(!$isAdmin): ;?>
		<p>You must be admin to use the admin area</p>
    <?php endif; ?>

	<div id="Wrapper">

		<?=anchor(base_url(), $GalleryLinkText, array('class' => 'back-to-gallery'));?>

		<ul class="tabs">
			<? foreach($Tabs as $tab): ;?>
				<li<?php if($currentPage == $tab){ echo ' class="active"'; } ?>>
					<?=anchor('admin/'.$tab, $tab, array('class' => 'tab'));$link;?>
				</li>
			<?php endforeach; ?>
		</ul>	