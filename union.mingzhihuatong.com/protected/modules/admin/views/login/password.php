<?php
$this->pageTitle=Yii::app()->name . ' - 设置密码';
?>
<div class="container">
    <?php if(isset($ok)) { ?>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php if($ok) { echo "设置密码成功";} else {echo "设置密码失败";} ?></h3>
                </div>
                <div class="panel-body" style="text-align: center;">
                    <?php if($ok) { ?>
                        <a class="btn btn-primary" href="/admin/login/">去登录</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">激活成功，请设置密码</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" action="" Method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="密码" name="password" type="password" value="">
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <input type="submit" value="设置密码" class="btn btn-lg btn-success btn-block">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
