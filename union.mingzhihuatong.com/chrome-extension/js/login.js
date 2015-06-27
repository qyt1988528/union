function login(username, password) {
    function checkForm() {
        return $('form.quc-form').size() != 0;
    }
    setTimeout(function(){
        if(checkForm()){
            $('input[name=account]').val(username);
            $('input[name=password]').val(password);
            if($('img.quc-captcha-img').attr('src') != '#') {
                chrome.runtime.sendMessage(null, JSON.stringify({
                    func: 'show_message',
                    params: '登录失败!需要输入验证码'
                }), null, function(response){
                });
                //需要验证码
                return;
            }
            $('input[type=submit]').click();
        } else {
            setTimeout(arguments.callee, 200)
        }
    }, 200);
}

chrome.runtime.sendMessage(null, JSON.stringify({
    func: 'get_logininfo'
}), null, function(response){
    if(response.username && response.password) {
        login(response.username, response.password)
    } else {
        chrome.runtime.sendMessage(null, JSON.stringify({
            func: 'need_login'
        }));
    }
});

