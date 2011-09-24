<!DOCTYPE HTML>
<html>
<head>
    <title><?=$PageTitle;?></title>
    <?php
    echo meta(array(
        array('name' => 'robots', 'content' => $Meta_robots),
        array('name' => 'Content-type', 'content' => 'text/html; charset=utf-8', 'type' => 'equiv')
    ));
    echo $CSS;
    ?>
</head>
<body>