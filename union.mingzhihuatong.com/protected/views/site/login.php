<div class="wrap">
    <div class="banner-show" id="js_ban_content">
        <div class="cell bns-01">
            <div class="con"> </div>
        </div>
        <div class="cell bns-02" style="display:none;">
            <div class="con"> <a href="#" target="_blank" class="banner-link"> <i></i></a> </div>
        </div>
        <div class="cell bns-03" style="display:none;">
            <div class="con"> <a href="#" target="_blank" class="banner-link"> <i></i></a> </div>
        </div>
    </div>
    <div class="banner-control" id="js_ban_button_box"> <a href="javascript:;" class="left">左</a> <a href="javascript:;" class="right">右</a> </div>
    <div class="container">
        <div class="register-box">
            <div class="tabs2">
                <div id="IndexTabs2" class="IndexTabs2">
                    <ul class="TabTitle2">
                        <li class="IndexTabs-0 IndexTabs_visited Alogin"><a href="#">登录</a></li>
                    </ul>
                    <div id="IndexTabs-0" class="AloginCont">
                        <form id="loginForm" method="post">
                            <input type="hidden" name="type" value="login" />
                            <div class="TabTitle2_top1"><span class="span1"></span>
                                <input name="LoginForm[email]" type="text" id="key"  placeholder="邮箱" class="input1"  value="<?php echo $userinput['email'];?>"/>
                                <div class="clear"></div>
                            </div>
                            <div class="TabTitle2_top1"><span class="span2"></span>
                                <input name="LoginForm[password]" type="password" id="key"  placeholder="密码" class="input1" />
                                <div class="clear"></div>
                            </div>
                            <div class="input2" ><span class=" input2_span1">
                                <input name="" type="checkbox" placeholder="" />
                                <span>记住密码</span><span class=" input2_span2"><a href="#" target="_blank">忘记密码</a></span></div>
                            <div class="clear"></div>
                            <div class="Login"><a href="javascript:;" class="btn submit">登录</a></div>
                        </form>
                    </div>
                    <div id="IndexTabs2" class="ZhucCont hide">
                        <form id="regForm" method="post" action="/site/register">
                            <input type="hidden" name="type" value="reg" />
                            <div class="TabTitle2_top1"><span class="span1"></span>
                                <input name="RegForm[email]" type="text" id="key"  placeholder="邮箱"  class="input1" />
                                <div class="clear"></div>
                            </div>
                            <div class="TabTitle2_top1"><span class="span2"></span>
                                <input name="RegForm[password]" type="password" id="key"  placeholder="密码" class="input1" />
                                <div class="clear"></div>
                            </div>
                            <div class="TabTitle2_top1"><span class="span3"></span>
                                <input name="RegForm[phone]" type="text" id="key"  placeholder="联系电话*" class="input1" />
                                <div class="clear"></div>
                            </div>
                            <div class="TabTitle2_top1"><span class="span4"></span>
                                <input name="RegForm[name]" type="text" id="key"  placeholder="姓名" class="input1" />
                                <div class="clear"></div>
                            </div>
                            <div class="TabTitle2_top1"><span class="span5"></span>
                                <input name="RegForm[qq]" type="text" id="key"  placeholder="QQ号" class="input1" />
                                <div class="clear"></div>
                            </div>
                            <div class="Registration"><a href="javascript:;" class="submit">快速注册</a></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="main">
    <div class="con"><span class="span1">合作<strong>伙伴</strong></span><span class="span2"></span>
        <div class="clear"></div>
        <div class="logo">
            <ul>
                <li><span class="span3"></span></li>
                <li><span class="span4"></span></li>
                <li><span class="span5"></span></li>
                <li><span class="span6"></span></li>
                <li><span class="span7"></span></li>
                <li><span class="span8"></span></li>
                <li><span class="span9"></span></li>
                <li><span class="span10"></span></li>
                <li><span class="span11"></span></li>
                <li><span class="span12"></span></li>
                <li><span class="span13"></span></li>
                <li><span class="span14"></span></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
</div>

<?php
Yii::app()->clientScript->registerScriptFile('/js/placeholders.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/home.js', CClientScript::POS_END);
?>
