(function(){
    var loadScript = function(src, onload) {
        var ele = document.createElement('script');
        ele.src = src;
        ele.onload = function( ){
            if(typeof(onload) === 'function') {onload()}
        };
        document.getElementsByTagName('head')[0].appendChild(ele);
    };

    loadScript('http://dev.mingzhihuatong.com/js/jquery.min.js', main);
})();