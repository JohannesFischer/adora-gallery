	<?php foreach($JS as $file): ?>
	<script type="text/javascript" src="<?=$file;?>"></script>
	<?php endforeach; ?>
    <script type="text/javascript">
        var AjaxURL = '<?=site_url('ajax');?>/';
    </script>
    
</body>
</html>