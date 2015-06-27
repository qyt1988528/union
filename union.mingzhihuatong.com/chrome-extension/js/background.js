var dataset = {};

var sendNotification = (function(){
    var notification = null;
    var tag = 'mingzhi';
    var logs = [];
    return function(message) {
        if(notification != null){
            notification.close();
        }
        /*
        if(logs.length > 10) {
            logs.shift();
        }
        */
        logs.push(message);
        notification = new Notification("抓取插件", {body:logs.join('\n'), tag:tag});

        /*
        notification.onclose = function() {
            logs = [];
        }
        */
    };
})();

function Task (tab) {
    this.state = 'unlogin';
    this.tab = tab;
    this.data = [];
    this.addData = [];
}

Task.prototype.getId = function() {
    return this.tab.id;
};

Task.prototype.login = function(callback) {
    var tab = this.tab;
    chrome.tabs.executeScript(tab.id, {
        file: 'js/jquery.js',
        runAt: 'document_end'
    }, function(){
        chrome.tabs.executeScript(tab.id, {
            file: 'js/login.js',
            runAt: 'document_end'
        })
    });
};

Task.prototype.getLoginInfo = function() {
    return this.loginInfo;
};

Task.prototype.setLoginInfo = function(loginInfo) {
    this.loginInfo = loginInfo;
}

Task.prototype.onUrlChange = function(url){
    var type = this.getUrlType(url);
    switch(type){
        case 'content':
            this.startParseContent();
            break;
    }
};

Task.prototype.startParseContent= function() {
    var tab = this.tab;
    chrome.tabs.executeScript(tab.id, {
        file: 'js/jquery.js',
        runAt: 'document_end'
    }, function(){
        chrome.tabs.executeScript(tab.id, {
            file: 'js/parse_360_mobilem.js',
            runAt: 'document_end'
        }, function() {
        })
    })
};

Task.prototype.getUrlType = function(url) {
    if(/^http:\/\/stats\.np\.mobilem\.360\.cn\//.test(url)) {
        return 'content'
    }
    return '';
}

Task.prototype.onReceiveData = function(params) {
    if(params.data) {
        sendNotification('账号:'+this.loginInfo.username+' 成功抓取一页数据')
        this.data = this.data.concat(params.data);
    }
    if(params.nexturl) {
        //爬取下一页面
        sendNotification('账号:'+this.loginInfo.username+' 开始抓取下一页')
        chrome.tabs.update(this.tab.id, {url:params.nexturl})
    } else {
        sendNotification('账号:'+this.loginInfo.username+' 下渠道全部抓取完成')
        if(typeof(this.onFinished) == 'function') {
            this.onFinished();
        }
    }
};

Task.prototype.getData = function() {
    return this.data;
};


function TaskManager() {
    var map = {};
    this.addTask = function(task) {
        map[task.getId()] = task;
    }

    this.getTaskById = function(id) {
        return map[id]
    }
}

var manager = new TaskManager();

var rpc = {
    get_logininfo: function(params, sender, sendResponse) {
        if(!sender.tab || !sender.tab.id) {
            return;
        }
        var task = manager.getTaskById(sender.tab.id);
        sendResponse(task.getLoginInfo());
    },

    receive_data : function(params, sender, sendResponse) {
        if(!sender.tab || !sender.tab.id) {
            return;
        }
        var task = manager.getTaskById(sender.tab.id);
        task.onReceiveData(params);
    },

    get_alldata : function(params, sender, sendResponse) {
            if(!sender.tab || !sender.tab.id) {
            return;
        }
        sendResponse(JSON.stringify(dataset[sender.tab.id]));
    },

    show_message : function(params, sender, sendReponse) {
        sendNotification(params);
    },

    need_login : function(){
        chrome.tabs.create({url:'http://union.zhantai.com/admin/login/'})
    }
}

chrome.runtime.onMessage.addListener(function(message, sender, sendResponse){
    try{
        message = JSON.parse(message);
        if(typeof(rpc[message['func']]) == 'function') {
            rpc[message['func']](message['params'], sender, sendResponse);
        }
    }catch(e) {
        return;
    }
});

chrome.tabs.onUpdated.addListener(function(tabid, changeInfo, tab) {
    if(changeInfo.url) {
        var task = manager.getTaskById(tabid);
        if(task) {
            task.onUrlChange(changeInfo.url)
        }
    }
})

function crawl_one_adv(submit_url, loginInfoList, onFinish){
    var url = 'http://i.360.cn/login?destUrl=http%3A%2F%2Fstats.np.mobilem.360.cn%2F'
    var alldata = [];
    function crawl() {
        var loginInfo = {};
        if(loginInfoList.length == 0) {
            sendNotification('抓取完成');
            saveData(submit_url, alldata)
            if(typeof(onFinish) === 'function') {
                onFinish();
            }
            return;
        } else {
            loginInfo = loginInfoList.shift();
            sendNotification('开始抓取账户:'+loginInfo.username);
        }
        chrome.tabs.create({url:url}, function(tab){
            if(!tab) {
                alert('打开tab失败');
                return;
            }
            var task = new Task(tab);
            task.onFinished = function(){
                alldata = alldata.concat(this.getData());
                chrome.tabs.remove(tab.id);
                crawl();
                /*
                setTimeout(function(){
                    sendNotification('抓取完成, 两秒后抓取下一账户')
                }, 2000)
                */
            }
            task.setLoginInfo(loginInfo);
            manager.addTask(task)
            task.login();
        });
    }
    crawl();
}

function crawl_360wm(configs) {
    function crawl() {
        var config = configs.shift();
        if(config) {
            console.log('start crawl ' + config.submit_url);
            crawl_one_adv(config.submit_url, config.data, crawl);
        }
    }
    crawl();
}


function saveData(submit_url, alldata) {
    chrome.tabs.create({
        url:submit_url,
        selected: true
    }, function(tab){
        chrome.tabs.executeScript(tab.id, {
            file: 'js/jquery.js',
            runAt: 'document_end'
        }, function(){
            dataset[tab.id] = alldata;
            chrome.tabs.executeScript(tab.id, {
                file: 'js/showresult.js',
                runAt: 'document_end'
            })
        });
    })
}

function openLoginPage(){
    //chrome.tabs.create({url:'http://union.zhantai.com/admin/login/'})
}

