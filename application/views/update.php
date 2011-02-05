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
