chrome.runtime.sendMessage(null, JSON.stringify({
    func: 'get_alldata'
}), null, function(data){
    data = JSON.parse(data);
    var row = [];
    for(var i = 0, n = data.length; i < n; ++i) {
        row.push([data[i].date, data[i].tag, data[i].number].join('\t'));
    }
    $('textarea').val(row.join('\n'));
});


