<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */
$this->pageTitle=Yii::app()->name . ' - 找回密码';
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">找回密码</h3>
                </div>
                <div class="panel-body">
                    <form role="form" action="" Method="post">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="邮箱" name="email" value="<?php echo $userinput['email']; ?>" type="input" autofocus="">
                                <?php if($message) { ?>
                                    <p class="help-block"><?php echo $message;?></p>
                                <?php } ?>
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <input type="submit" value="点击找回" class="btn btn-primary">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
