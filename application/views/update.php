<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title><?=$PageTitle;?></title>
    <META http-equiv="Content-type" content="text/html; charset=utf8">
    <?=$CSS;?>
    <script type="text/javascript" src="resources/js/mootools-core-1.3-full-nocompat-yc.js"></script>
    <script type="text/javascript" src="resources/js/Photos.js"></script>
    <script type="text/javascript">
        var AjaxURL = '<?=site_url('ajax');?>/';
    </script>
</head>
<body>
    
    <?php if(!$Loggedin): ;?>
        <?=$LoginForm;die('</body></html>')?>
    <?php endif; ?>
    
    <div id="Content">
        <div id="AddImages">
            <h1><?=$PageTitle;?></h1>
            <?php
                $i = 0;
            ?>
            <?php foreach($Files as $file): ?>
                <div class="new-image">
                    <?=img(array(
                        'src' => $ImageFolder.$file['filename'],
                        'width' => 250
                    ));?>
                    <form method="post" action="#">
                        <fieldset>
                            <label>Title</label>
                            <input type="text" name="Title" placeholder="Title" />
                            <label>Description</label>
                            <textarea name="Description" cols="45" rows="5"></textarea>
                            <input type="hidden" name="Filename" value="<?=$file['filename'];?>" />
                            <input type="hidden" name="FileDateTime" value="<?=$file['exif']['FileDateTime'];?>" />
                            <input type="submit" name="save_image_<?=$i;?>" value="save" />
                        </fieldset>
                    </form>
                </div>
                <?php
                    $i++;
                ?>
            <?php endforeach; ?>
            
        </div>
    </div>    
    
</body>
</html>
