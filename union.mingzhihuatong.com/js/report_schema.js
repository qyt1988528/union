$(function() {

    var trim = function(str) {
        if(!str) {
            return '';
        }
        return str.replace(/^\s+|\s+$/g, '');
    }

    var $dialog = $('#showResultDialog');
    $dialog.appendTo($(document.body));

    var $publicDataDialog = $('#showPublicResultDialog');
    $publicDataDialog.appendTo($(document.body));

    var Editcell = function($con) {
        var $cell = $con;
        var originValue = trim($cell.find('span').text());
        var currentValue = originValue;
        this.editable = false;
        this.toggle = function() {
            this.setEditMode(!this.editable);
        };

        this.setEditMode = function(editmode) {
            this.editable = editmode;
            var _this = this;
            if(editmode) {
                $con.find('span').remove();
                $con.append($('<input />').val(currentValue));
                $con.find('input').on('blur', function() {
                    _this.setEditMode(false);
                });
                $con.find('input').on('keypress', function(evt) {
                    var keycode = (event.keyCode ? event.keyCode : event.which);
                    if(keycode == '13'){
                        _this.setEditMode(false);
                    }
                });
            } else {
                currentValue = trim($con.find('input').val());
                $con.find('input').remove();
                if(currentValue != originValue) {
                    var $del = $con.find('del');
                    if(!$del.length) {
                        $con.append($('<del />'));
                    }
                    $con.find('del').html(originValue);
                    $con.addClass('modified');
                }
                $con.append($('<span></span>').html(currentValue));
            }
        }
    };

    $publicDataDialog.delegate('td input', 'click', function(evt){
        //不再向上冒泡
        evt.stopPropagation();
    })

    $publicDataDialog.delegate('td[data-edittype=input]', 'click', function(){
        var cell = $(this).data('cell');
        if(!cell) {
            cell = new Editcell($(this));
            $(this).data('cell', cell);
        }
        cell.toggle();
    });

    var $form = $('#sendDataForm');
    $('#sendDataPreviewBtn').click(function(evt) {
        evt.preventDefault();
        $.ajax('/admin/report/previewUpload/', {
            type:'post',
            data:$form.serialize()
        }).done(function(response) {
            $dialog.find('.modal-body').html(response);
            $dialog.modal('show');
        }).fail(function() {
            $form.notify('上传失败,服务器错误', {position:'bottom center'});
        }).always(function( ){
            $form.find('fieldset').removeAttr('disabled');
        });
        $form.find('fieldset').attr('disabled', true);
    });

    $('#sendDataBtn').click(function(evt) {
        evt.preventDefault()
        $.ajax('/admin/report/upload/', {
            type:'post',
            dataType: 'json',
            data:$form.serialize()
        }).done(function(response) {
            if(response.code == 0) {
                bootbox.alert(response.message, function() {
                    $dialog.modal('hide');
                    window.location.href = "/admin/advertise/";           
                });
            } else {
                bootbox.alert(response.message);
            }
        }).fail(function() {
            $form.notify('上传失败,服务器错误', {position:'bottom center'});
        });
    });

    $('#sendPublicDataPreviewBtn').click(function(evt) {
        evt.preventDefault();
        $.ajax('/admin/report/previewUpload/?type=public', {
            type:'post',
            data:$form.serialize()
        }).done(function(response) {
            $publicDataDialog.find('.modal-body').html(response);
            $publicDataDialog.modal('show');
        }).fail(function() {
            $form.notify('上传失败,服务器错误', {position:'bottom center'});
        }).always(function( ){
            $form.find('fieldset').removeAttr('disabled');
        });
        $form.find('fieldset').attr('disabled', true);
    });

    var getTableData = function($table) {
        var rows = [];
        $table.find('tbody tr').each(function(index){
            if($(this).hasClass('duplicated')) {
                return;
            }
            var row = [];
            $(this).find('td span').each(function(){
                row.push(trim($(this).text()));
            })
            rows.push(row);
        });
        return rows;
    };

    $('#sendPublicDataBtn').click(function(evt) {
        evt.preventDefault()
        var postArray = $form.serializeArray();
        var postJson = {};
        for(var i = 0; i < postArray.length; ++i) {
            postJson[postArray[i].name] = postArray[i].value;
        }

        postJson.data = getTableData($publicDataDialog.find('table'));

        $.ajax('/admin/report/uploadPublic/', {
            type:'post',
            data:postJson,
            dataType: 'json',
        }).done(function(response) {
            if(response.code == 0) {
                bootbox.alert(response.message, function() {
                    $publicDataDialog.modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 300);

                });
            } else {
                bootbox.alert(response.message);
                alert(2222);
                setTimeout(function() {
                    location.reload();
                }, 300);

            }
        }).always(function() {
            $form.find('fieldset').removeAttr('disabled');
        });
        $form.find('fieldset').attr('disabled', true);
    });

    $('.action-toggleduplicated').click(function() {
        $('tr.duplicated').show();
    });


    /*查看历史数据*/
     $("#createHistoryData").click(function(){
         var adv_id = $("#history_adv_id").val();
         var cp_id = $("#history_cp_id").val();
         window.location.href = "/admin/advertise/history?f[adv_id]="+adv_id+"&f[cp_id]="+cp_id;   
        
     });

     $("#reback").click(function(){
         var adv_id = $("#adv_id").val();
         window.location.href = "/admin/advertise/upload?f[adv_id]="+adv_id;
     });
    
})
