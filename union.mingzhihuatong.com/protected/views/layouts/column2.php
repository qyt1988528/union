<?php $this->beginContent('//layouts/main');?>
<div class="nav">
    <div class="con clearfix">
        <h1></h1>
        <?php if(isset($this->clips['topbar-rightside'])) {
            echo $this->clips['topbar-rightside'];
        } ?>
    </div>
</div>
<?php echo $content; ?>
<div class="footer">© Copyright 2014 铭智华通联盟 Inc.All Rights Reserved. 京ICP备13039797号-6</div>
<?php $this->endContent(); ?>
