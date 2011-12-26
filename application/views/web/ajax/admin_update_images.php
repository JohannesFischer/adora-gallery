<ul class="zebra">
    <?php if(count($Files) > 0): ?>
        <?php foreach($Files as $file): ?>
            <li>
                <a href="<?=$file['filename'];?>" class="icon image"><?=$file['filename'];?></a>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No images found</p>
    <?php endif; ?>
</ul>