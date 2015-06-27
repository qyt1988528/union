$(document).ready(function ($) {
    // Workaround for bug in mouse item selection
    cp_search();
    adv_search();
    channel_search();
    tag_search();
    /*
    $("#cp_name").focus(function(){
        cp_search();
    });
    $("#adv_name").focus(function(){
        adv_search();
    });
    $("#channel_name").focus(function(){
        channel_search();
    });
    $("#tag_name").focus(function(){
        tag_search();
    });
    */

    function cp_search(){

        $.fn.typeahead.Constructor.prototype.blur = function () {
            var that = this;
            setTimeout(function () { that.hide() }, 250);
        };

        var url='/admin/report/allcp';
        $.ajax({
            url:url,
            type:"GET",
            cache:false,//防缓存
            dateType:"json",
            success:function(data){
                var data_cp = JSON.parse(data);
                var that = this ;
                var cp_input = $('#cp_name').typeahead({
                    showHintOnFocus: false,
                    items:10,
                    source: function (query, process) {
                        var results = _.map(data_cp, function (product) {
                            return product.cpname + "";
                        });
                        process(results);
                    },

                    matcher: function (item) {
                        return true;
                    },
                    //搜索时输入框显示的内容
                    highlighter: function (cpname) {
                        var product = _.find(data_cp, function (p) {
                            return p.cpname == cpname;
                        });
                        return product.cpname;
                    },
                    //最后输入框显示的内容
                    updater: function (cpname) {
                        var product = _.find(data_cp, function (p) {

                            return p.cpname == cpname;
                        });
                        that.setSelectedCP(product);
                        return product.cpname;
                    }

                });

                this.setSelectedCP = function (product) {
                    $("#cp_id").attr({value:product.cpid});
                }
            }
        });
    }
    function adv_search(){

        $.fn.typeahead.Constructor.prototype.blur = function () {
            var that = this;
            setTimeout(function () { that.hide() }, 250);
        };

        var cp_id = $("#cp_id").val();
        if(cp_id==""){
            var url='/admin/report/cpadv';
        }else{
            var url='/admin/report/cpadv?cp_id='+cp_id;
        }
        $.ajax({
            url:url,
            type:"GET",
            cache:false,//防缓存
            dateType:"json",
            success:function(data){
                var data_adv = JSON.parse(data);
                var that = this ;

                var adv_input = $('#adv_name').typeahead({
                    showHintOnFocus: false,
                    items:10,
                    source: function (query, process) {
                        var results = _.map(data_adv, function (product) {
                            return product.advname + "";
                        });
                        process(results);
                    },

                    matcher: function (item) {
                        return true;
                    },
                    //搜索时输入框显示的内容
                    highlighter: function (advname) {
                        var product = _.find(data_adv, function (p) {
                            return p.advname == advname;
                        });
                        return product.advname;
                    },
                    //最后输入框显示的内容
                    updater: function (advname) {
                        var product = _.find(data_adv, function (p) {

                            return p.advname == advname;
                        });

                        that.setSelectedAdv(product);
                        return product.advname;
                    }

                });


                this.setSelectedAdv = function (product) {
                    $("#adv_id").attr({value:product.advid});
                }
            }
        });
    }
    function channel_search(){

        $.fn.typeahead.Constructor.prototype.blur = function () {
            var that = this;
            setTimeout(function () { that.hide() }, 250);
        };

        var adv_id = $("#adv_id").val();
        if(adv_id==""){
            var url='/admin/report/advchan';
        }else{
            var url='/admin/report/advchan?adv_id='+adv_id;
        }
        $.ajax({
            url:url,
            type:"GET",
            cache:false,//防缓存
            dateType:"json",
            success:function(data){
                var data_channel = JSON.parse(data);
                var that = this ;

                var channel_input = $('#channel_name').typeahead({
                    showHintOnFocus: false,
                    items:15,
                    source: function (query, process) {
                        var results = _.map(data_channel, function (product) {
                            return product.channame + "";
                        });
                        process(results);
                    },

                    matcher: function (item) {
                        return true;
                    },
                    //搜索时输入框显示的内容
                    highlighter: function (channame) {
                        var product = _.find(data_channel, function (p) {
                            return p.channame == channame;
                        });
                        return product.channame;
                    },
                    //最后输入框显示的内容
                    updater: function (channame) {
                        var product = _.find(data_channel, function (p) {

                            return p.channame == channame;
                        });
                        that.setSelectedChan(product);
                        return product.channame;
                    }

                });
                this.setSelectedChan = function (product) {
                    $("#channel_id").attr({value:product.chanid});
                }
            }
        });
    }
    function tag_search(){
        $.fn.typeahead.Constructor.prototype.blur = function () {
            var that = this;
            setTimeout(function () { that.hide() }, 250);
        };

        var channel_id = $("#channel_id").val();
        if(channel_id==""){
            var url='/admin/report/searchTagByChannel';
        }else{
            var url='/admin/report/searchTagByChannel?channel_id='+channel_id;
        }
        $.ajax({
            url:url,
            type:"GET",
            cache:false,//防缓存
            dateType:"json",
            success:function(data){
                var data_tag = JSON.parse(data);
                var that = this ;

                var tag_input = $('#tag_name').typeahead({
                    showHintOnFocus: false,
                    items:15,
                    source: function (query, process) {
                        var results = _.map(data_tag, function (product) {
                            return product.tagname + "";
                        });
                        process(results);
                    },

                    matcher: function (item) {
                        return true;
                    },
                    //搜索时输入框显示的内容
                    highlighter: function (tagname) {
                        var product = _.find(data_tag, function (p) {
                            return p.tagname == tagname;
                        });
                        return product.tagname;
                    },
                    //最后输入框显示的内容
                    updater: function (tagname) {
                        var product = _.find(data_tag, function (p) {

                            return p.tagname == tagname;
                        });
                        that.setSelectedtag(product);
                        return product.tagname;
                    }

                });
                this.setSelectedtag = function (product) {
                    $("#adv_channel_id").attr({value:product.tagid});
                }
            }
        });
    }

    $('#return_top').click(function(){$('html,body').animate({scrollTop: '0px'}, 800);return false;});

})