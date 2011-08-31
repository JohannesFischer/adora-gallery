	<div id="Wrapper">

		<?=anchor(base_url(), $GalleryLinkText, array('class' => 'back-to-gallery'));?>

		<ul class="tabs">
			<? foreach($Tabs as $key => $value): ;?>
				<li<?php if($currentPage == $key){ echo ' class="active"'; } ?>>
					<?=anchor('admin/'.$key, $value, array('class' => 'tab'));?>
				</li>
			<?php endforeach; ?>
		</ul>	