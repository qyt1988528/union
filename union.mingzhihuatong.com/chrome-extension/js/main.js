var background = chrome.extension.getBackgroundPage();
$('button.360wm').click(function(){
    $.get('http://union.zhantai.com/admin/autoFetch/getLoginInfo', function(data){
//    $.get('http://dev.mingzhihuatong.com/admin/autoFetch/getLoginInfo', function(data){
        background.sendNotification(data.message);
        if(data.code != 200) {
            background.openLoginPage();
            return;
        }
        /*
        var submit_url = data.submit_url;
        data = data.data;
        var message = [];
        message.push('用户账号数:'+data.length);
        for(var i = 0, n = data.length; i < n; ++i) {
            message.push((i+1) + '. ' + data[i].username);
        }
        background.sendNotification(message.join('\n'));
        */
        background.crawl_360wm(data.data);
    }, 'json')
    //background.crawl_360wm();
})