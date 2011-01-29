<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title><?=$PageTitle;?></title>
    <META http-equiv="Content-type" content="text/html; charset=utf8">
    <?=$CSS;?>
</head>
<body>
    
    <?php if(!$Loggedin): ;?>
        <?=$LoginForm;?>
    <?php else: ?>
    
    <div id="TopBar">
        <div>
            <h1><?=$PageTitle;?></h1>
        </div>
        <div>
            
        </div>
        <div>
            <ul>
                <li>
                    <a href="#">Show Info</a>
                </li>
                <li>
                    <a href="#">Comments</a>
                </li>
                <li>
                    <a href="#">Options</a>
                </li>
                <li>
                    <a href="#">Help</a>
                </li>
                <li>
                    <a href="#">Logout</a>
                </li>
            </ul>
        </div>
    </div>
    
    <div id="Image"></div>
    
    <div id="Thumbnails">
        
        <a href="#" class="play" id="Play"></a>
        
        <ul>
        <?php foreach($Photos as $photo): ?>
            <li>
                <a href="<?=$ImageFolder.$photo['Filename_Large'];?>" style="background-image:url(<?=$ImageFolder.$photo['Filename_Thumbnail'];?>)" rel="<?=$ImageFolder.$photo['Filename_Thumbnail'];?>"></a>
            </li>
        <?php endforeach; ?>
    </div>
    
    <?php endif;?>
    
    <script type="text/javascript" src="resources/js/mootools-core-1.3-full-nocompat-yc.js"></script>
    <script type="text/javascript" src="resources/js/mootools-more.js"></script>
    <script type="text/javascript" src="resources/js/Photos.js"></script>
    <script type="text/javascript" src="resources/js/infoBubble.js"></script>
    <script type="text/javascript">
        var AjaxURL = '<?=site_url('ajax');?>/';
    </script>
    
</body>
</html>