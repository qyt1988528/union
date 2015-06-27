$(function(){
    var defaultInd = 0;
    var list = $('#js_ban_content').children();
    var count = 0;
    var change = function(newInd, callback){
        if(count) return;
        count = 2;
        $(list[defaultInd]).fadeOut(400, function(){
            count--;
            if(count <= 0){
                if(start.timer) window.clearTimeout(start.timer);
                callback && callback();
            }
        });
        $(list[newInd]).fadeIn(400, function(){
            defaultInd = newInd;
            count--;
            if(count <= 0){
                if(start.timer) window.clearTimeout(start.timer);
                callback && callback();
            }
        });
    }

    var next = function(callback){
        var newInd = defaultInd + 1;
        if(newInd >= list.length){
            newInd = 0;
        }
        change(newInd, callback);
    }

    var start = function(){
        if(start.timer) window.clearTimeout(start.timer);
        start.timer = window.setTimeout(function(){
            next(function(){
                start();
            });
        }, 8000);
    }

    start();

    $('#js_ban_button_box').on('click', 'a', function(){
        var btn = $(this);
        if(btn.hasClass('right')){
            //next
            next(function(){
                start();
            });
        }
        else{
            //prev
            var newInd = defaultInd - 1;
            if(newInd < 0){
                newInd = list.length - 1;
            }
            change(newInd, function(){
                start();
            });
        }
        return false;
    });

});

$(document).ready(function(){
    $(".AloginCont").show();
    $(".ZhucCont").hide();
    $(".Alogin").click(function(){
        $(".AloginCont").show();
        $(".ZhucCont").hide();
    })
    $(".Zhuc").click(function(){
        $(".AloginCont").hide();
        $(".ZhucCont").show();
    })
});

$(function() {
    $('form').each(function(){
        var $form = $(this);
        $form.delegate('input', 'keypress', function(evt) {
            if(evt.keyCode == 13) {
                $form.submit();
            }
        });
        $form.find('.submit').click(function() {
            if($form.attr('id') == 'regForm') {
                var isEmail = testValue(/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/, '12323435@qq.com');
                if(!isEmail) {
                    echoError('邮箱格式错误');
                    return;
                }
                var isPassword = testValue(/.{6,}/, '1234566');
                if(!isPassword) {
                    echoError('密码长度应不小于6');
                    return;
                }
                var isPhone = testValue(/^((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8}$/, '13121629336');
                if(!isPhone) {
                    echoError('手机号码格式错误');
                    return;
                }
                var isQQ = testValue(/^[1-9]{1}\d{5,9}$/, '1312162336');
                if(!isQQ) {
                    echoError('QQ号码格式错误');
                    return;
                }
                var isName = testValue(/^[\u4e00-\u9fa5]|[a-zA-Z]+$/, '33');
                if(!isName) {
                    echoError('姓名格式错误');
                    return;
                }
            } 
            $form.submit();
        });
    });

    function testValue(pattern, val) {
        return pattern.test(val); 
    }
    function echoError(msg) {
        alert(msg);
    }
    $('#channelSwitch').on('change', function(evt) {
        location.href = '/site/jump?channel_id=' + $(this).val();
    })

    $("#datepicker-start").datetimepicker({
        format:'yyyy-mm-dd',
        minView:2,
        autoclose:true
    });

    $("#datepicker-end").datetimepicker({
        format:'yyyy-mm-dd',
        minView:2,
        autoclose:true
    });


})


