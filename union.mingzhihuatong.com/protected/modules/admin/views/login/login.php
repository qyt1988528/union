<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */
$this->pageTitle=Yii::app()->name . ' - 登录';
?>
<div class="container" >
    <div id="red"></div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">登录</h3>
                </div>
                <div class="panel-body">
                    <form role="form" action="" Method="post">
                        <fieldset>
                            <div class="form-group">
                                <label>邮箱</label>
                                <input class="form-control" name="email" id="email"  value="<?php echo $model->email;?>" type="input" autofocus="">
                            </div>
                            <div class="form-group<?php if($model->hasErrors('password')){ ?> has-error<?php }?>" id="passwordDiv">
                                <label>密码</label>
                                <input class="form-control" name="password" type="password" value="">
                                <?php if($model->hasErrors('password')) { ?>
                                <p class="help-block"><?php echo $model->getError('password');?></p>
                                <?php } ?>
                            </div>
                            <div id="verifyFlag" class="form-group<?php if($model->hasErrors('verify')){ ?> has-error<?php }?>">
                                <?php if($model->hasErrors('showVerify')){?>
                                <label>验证码</label>
                                <div style="height:35px;">
                                    <input class="form-control" name="verify" type="text" value="" style="width:230px; float:left;">
                                    <img src="/admin/Login/imgcode" alt="看不清，换一张" title="看不清，换一张" style="float:left; margin-top:5px; cursor:pointer;" align="absmiddle" id="img"/>
                                </div>
                                <?php if($model->hasErrors('verify')) { ?>
                                <p class="help-block"><?php echo $model->getError('verify');?></p>
                                <?php } ?>
                            <?php }?>
                            </div>
                            <div class="form-group" id="showVerify"></div>
                            <div class="form-group">
                                <!-- Change this to a button or input when using this as a form -->
                                <input type="submit" class="btn btn-primary" value="登录"></input>
                                <a href="/admin/login/findPassword/" class="btn btn-primary">找回密码</a>
                            </div>

                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
Yii::app()->clientScript->registerScriptFile('/js/adminlogin.js', CClientScript::POS_END);
?>
