$(function(){

    var $dataTable = $('#dataTable');
    if($dataTable.size() == 0) {
        //没有数据表
        return;
    }

    function showFormError($form, errors){
        $form.find('div.form-group').each(function(index, formgroup) {
            var field = $(this).attr('for-field');
            if(!field || !errors[field]) {
                return;
            }
            $(formgroup).addClass('has-error');
            var $helperBlock = $(formgroup).find('.help-block');
            var oldTip = $helperBlock.html();
            $helperBlock.html(errors[field].join('<br/>'));
            $(formgroup).find('input, select, textarea').one('focus', function() {
                $(formgroup).removeClass('has-error');
                $helperBlock.html(oldTip);
            });
        });
    }

    function Form() {

        this.$dialog = null;
        this.$form = null;
        this.current_remote_url = '';
        var form_id = 'form-modal-' + parseInt(Math.random() * 100000);

        this.show = function(remote_url) {
            if(this.current_remote_url == remote_url) {
                this.$dialog.modal({show:true});
                return;
            }
            this.current_remote_url = remote_url;
            //清除上次数据
            this.$dialog.removeData('bs.modal');
            this.$dialog.modal({remote:this.current_remote_url}).one(
                'loaded.bs.modal', this.onload.bind(this)
            );
        };

        this.onload = function(){
            var _this = this;
            this.$form = this.$dialog.find('form');
            this.$form.submit(function(evt) {
                evt.preventDefault();
                _this.submit();
            });
            this.$form.delegate('input, select', 'focus', function() {
            })
        };

        this.submit = function() {
            var $form = this.$form;
            $form.find('fieldset').attr('disabled');
            var action = $form.attr('action') || '';
            var type = $form.attr('method') || 'GET';
            var data = $form.serialize();
            $form.find('.form-group').removeClass('has-error');
            $form.find('.help-block').html('');
            $form.find('fieldset').attr('disabled', true);
            $form.find('.btn-submitform').button("loading");
            $.ajax(action, {
                data: data,
                type : type,
                timeout: 3000,
                dataType: 'json'
            }).done(function(data, textStatus, jqXHR) {
                if(data && (data.code == 0)) {
                    $form.notify("保存成功", "success", {position:"center"});
                    setTimeout(function(){
                        location.reload();
                    }, 200);
                } else {
                    showFormError($form, data.data);
                    $form.notify(data.message, "error", {position:"center"});
                    $form.find('.btn-submitform').button("reset");
                    $form.find('fieldset').attr('disabled', false);
                }
            }).fail(function(jqXHR, textStatus, errorThrown){
                var message = "保存失败,服务器错误";
                if(textStatus == 'timeout') {
                    message = "保存失败,服务器超时，请稍后重试";
                }
                $form.notify(message, "error", {position:"center", autoHideDelay:2000});
                $form.find('.btn-submitform').button("reset");
                $form.find('fieldset').attr('disabled', false);
            })
        };

        //constructor
        this.$dialog = $('<div class="modal"><div class="modal-dialog"><div class="modal-content"></div></div>').attr('id', form_id);
        this.$dialog.appendTo($(document.body));
    }

    function doAction(action, model_id, target) {
        switch (action) {
            case 'update':
                form.show(target + '?id='+model_id);
                break;
            case 'delete':
                $.post(target, {id: model_id}, function(data) {
                    if(data.code == 0) {
                        bootbox.alert(data.message, function() {
                            location.reload();
                        });
                    } else {
                        bootbox.alert(data.message);
                    }
                }, 'json');
                break;
        }
    }

    var form = new Form();
    var form_action = $dataTable.attr('form-action');

    $dataTable.delegate('tr.modelContainer button', 'click', function(evt) {
        evt.preventDefault();
        var tr = $(this).parents('tr.modelContainer');
        if(!tr) {
            return;
        }
        var model_id = tr.attr('model-id');
        var confirm = $(this).attr('ajax-confirm');
        var action = $(this).attr('ajax-action');
        var target = $(this).attr('ajax-target');
        if(confirm == "true") {
            bootbox.confirm("确实要" + $(this).text() + "这条记录吗?" , function(result) {
                if(!result) {
                    return;
                }
                doAction(action, model_id, target);
            })
        } else {
            doAction(action, model_id, target);
        }
    });

    $('#btn-createNew').click(function() {
        form.show(form_action);
    })

    /*start 11-11*/
    $('#return_top').click(function(){$('html,body').animate({scrollTop: '0px'}, 800);return false;});
    /*end*/
});