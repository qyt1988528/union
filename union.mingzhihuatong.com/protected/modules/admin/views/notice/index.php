<div class="notice_select">
    <input type="button" id="selectAll" value="全选" class="btn btn-primary"/>
    <input type="button" id="cancelAll" value="取消" class="btn btn-default"/>
    <a href="javascript:" id="email" >发送邮件</a>
    <a href="javascript:" id="mes">发送信息</a>
</div>
<div class="bs-docs-example" style="margin-left:15px; width:300px;">
   搜索渠道名称： <input type="text" id="typeahead" style="margin: 0 auto;" data-provide="typeahead">
</div>
<div class="notice_mes">
    <?php echo $message;?>
</div>
<form  name="frm" action=" " method="post">
    <textarea name="content" style="display: none;"></textarea>
    <input type="hidden" name="subject"/>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>选择</th>
            <th>渠道名称</th>
            <th>渠道电话</th>
            <th>渠道邮箱</th>
        </tr>
    </thead>
    <tbody id="show_data" style="display:none;">
    </tbody>
    <tbody id="all_data">
    <?php foreach($channelList as $val) {
        ?>
        <tr> 
            <td><input type="checkbox" name="id[]" value="<?php echo $val['id'];?>"/></td>
            <td><?php echo $val['name'];?></td>
            <td><?php echo $val['contactor_phone'];?></td>
            <td><?php echo $val['contactor_email'];?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</form>
        <!--static dialog-->
        <div id="dialog-email" title="发送邮件">
            <form action="" method="post" name="email_frm">
            <p>
              标题： <input type="text" id="subject"  class="form-control"/>
            </p>
            <p>
              内容：  <textarea  id="kindeditor"></textarea>
            </p>
            </form>
        </div>
        <!--end static dialog-->

<div id="dialog-mes" title="发送信息">
    <form action="" method="post">
    <p>
        <textarea placeholder = "请输入短信内容...." rows="10" cols="65" id="content"></textarea>
    </p>
    </form>
</div>
<a id="return_top" style="position: fixed;bottom: 50px;right: 20px;width: 20px;line-height: 20px" href="javascript:">回到顶部</a>
<?php
Yii::app()->clientScript->registerCssFile('/css/custom.css');
Yii::app()->clientScript->registerScriptFile('/js/kindeditor/kindeditor.js',CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/kindeditor/lang/zh_CN.js',CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile('/css/jquery-ui-1.9.2.custom.css');
Yii::app()->clientScript->registerScriptFile('/js/typeahead.js',CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/notice_select.js',CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.9.2.custom.min.js',CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/underscore.js',CClientScript::POS_END);
?>

