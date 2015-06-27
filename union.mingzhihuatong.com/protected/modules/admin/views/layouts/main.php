<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset='utf-8' />
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap.min.css" media="screen, projection" />
    <!--[if lt IE 9]>
<script src="/js/html5shiv.js"></script>
<script src="/js/respond.min.js"></script>
    <![endif]-->
    <link href="/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/admin.css" rel="stylesheet">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
    <div id="wrapper">
        <?php echo $content; ?>
    </div>
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/plugins/metisMenu/metisMenu.min.js"></script>
<script src="/js/admin.js"></script>
</body>
</html>
