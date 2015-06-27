<?php $this->beginContent('/layouts/main'); ?>
<!-- Header -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
<div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><?php echo CHtml::encode($this->pageTitle); ?></a>
</div>
<!-- /.navbar-header -->
<div class="navbar-collapse collapse">
    <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
        <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="javascript:;"><i class="glyphicon glyphicon-user"></i> <?php echo Yii::app()->user->name; ?> <span class="caret"></span></a>
        <ul id="g-account-menu" class="dropdown-menu" role="menu">
            <li><a href="">My Profile</a></li>
        </ul>
        </li>
        <li><a href="/site/logout"><i class="glyphicon glyphicon-lock"></i> 退出</a></li>
    </ul>
</div>
<!-- /.navbar-top-links -->
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <?php foreach($this->getNavMenuConfig() as $key => $menu) { ?>
            <li>
            <a href="<?php echo $menu['url'];?>" <?php if($key == $this->getCurrentMenuKey()){?> class="active"<?php } ?> ><i></i><?php echo $menu['label'];?></a>
            </li>
            <?php } ?>
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->
</nav>
<div id='page-wrapper'>
<?php echo $content; ?>
</div>
<!-- page -->
<?php
$this->endContent();
Yii::app()->clientScript->registerScriptFile('/js/notify.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/bootbox.min.js', CClientScript::POS_END);
?>
