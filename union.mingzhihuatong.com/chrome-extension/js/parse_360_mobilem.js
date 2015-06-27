function parser360() {
    var result = [];
    $('table tbody tr').each(function() {
        var data = {
            date : $(this).find('td').eq(0).text(),
            tag : $(this).find('td').eq(1).text(),
            number : $(this).find('td').eq(2).text()
        }
        if(!/^[\d\-]+$/.test(data.date)) {
            //日期格式不对
            return;
        }
        if(data.number == 0){
            return;
        }
        result.push(data);
    })
    return result;
}

var cur = $('select option:selected').val();
var nexturl = '';

if(location.search == '') {
    if(cur) {
        nexturl = 'http://stats.np.mobilem.360.cn/?from=2014-09-01&channel_id='+cur;
    }

    chrome.runtime.sendMessage(null, JSON.stringify({
        func: 'receive_data',
        params: {
            data: [],
            nexturl : nexturl
        }
    }), null, function(){});
} else {
    var data = parser360();

    var active = $('li.active');
    if(active.size() != 0) {
        var nextpage = active.next('li').find('a');
        if(nextpage.size() != 0 && nextpage.attr('href')) {
            nexturl =  'http://stats.np.mobilem.360.cn/?from=2014-09-01&channel_id='+cur+'&page=' + nextpage.text()
        }
    }

    if(!nexturl){
        var next = $('select option:selected').next('option');
        if(next.size() != 0) {
            nexturl = 'http://stats.np.mobilem.360.cn/?from=2014-09-01&channel_id='+next.val();
        }
    }

    chrome.runtime.sendMessage(null, JSON.stringify({
        func: 'receive_data',
        params: {
            data: data,
            nexturl : nexturl
        }
    }), null, function(response){
    });
}

