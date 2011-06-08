    <?php if(!$Loggedin): ;?>
        <?=$LoginForm;?>
    <?php else: ?>
    
    <div id="TopBar">

        <div class="Title">
            <h1><?=$PageTitle;?></h1>
        </div>

        <div class="Controls">

            <div>
                <a href="#" class="control prev">prev</a>
                <a href="#" class="control next">next</a>
            </div>

        </div>

        <div class="Navigation">
            <ul>
                <li>
                    <a href="#" id="LinkInfo">Show Info</a>
                </li>
                <li>
                    <a href="#" id="LinkComments">Comments</a>
                </li>
                <li>
                    <a href="#" id="LinkOptions">Options</a>
                </li>
                <li>
                    <a href="#" id="LinkHelp">Help</a>
                </li>
                <li>
                    <a href="#" id="LinkLogout">Logout</a>
                </li>
            </ul>
        </div>
    </div>
    
    <div id="Image"></div>
    
    <div id="Thumbnails">
        
        <a href="#" class="control play" id="Play" title="<?=$Language['gallery_title_play_button'];?>"></a>
        
		<div class="thumbnail-wrapper">
			<ul>
			<?php foreach($Photos as $photo): ?>
				<li>
					<a href="<?=$ImageFolder.$photo['Filename_Large'];?>" style="background-image:url(<?=$ImageFolder.$photo['Filename_Thumbnail'];?>)" rel="<?=$ImageFolder.$photo['Filename_Thumbnail'];?>"></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>

		<a href="#" class="control slide" id="Slide"></a>

    </div>
    
    <?php endif; ?>