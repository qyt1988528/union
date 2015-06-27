<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */
$this->pageTitle=Yii::app()->name . ' - 注册';
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">注册</h3>
                </div>
                <div class="panel-body">
                    <form role="form" action="" Method="post">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="用户名/邮箱" name="email" type="input" autofocus="">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="密码" name="password" type="password" value="">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="联系电话" name="phone" type="phone" value="">
                            </div>
                            <div class="form-group">
                                <p class="text-danger">
                                    <?php echo $msg; ?>
                                </p>
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <input type="submit" value="注册" class="btn btn-lg btn-success btn-block">
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
