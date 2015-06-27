<div class="form-group">
    <button id="createHistoryData"  class="btn btn-primary">查看历史数据</button>
</div>


<form class="form" id="sendDataForm">
    <div>
        <table class="table table-bordered" >                                                        
            <tr>                                                                                 
                <th><label>表头格式</label></th>
                <td>日期</td>
                <td>包名</td>
                <td>下载量</td>
                <td>信用等级</td>
            </tr>                                                                                
        </table>                                                                                     
    </div>        
    <fieldset>
                <?php foreach($filters as $key => $value) { ?>
                <input type="hidden" id="history_<?php echo $key;?>"  name="<?php echo $key;?>" value="<?php echo $value;?>" />
        <?php } ?>
            <div class="form-group">
                <label>从Excel中粘贴到下框(不包含表头), 或者手动输入用空格分隔字段</label>
                <textarea class="form-control" rows="12" name="data"></textarea>
            </div>
            <div class="form-group">
                <button id="sendDataPreviewBtn" class="btn btn-primary">上传数据(对内)</button>
                <button id="sendPublicDataPreviewBtn" class="btn btn-primary">上传数据(对外)</button>
            </div>
    </fieldset>
</form>

<div id='showResultDialog' class="modal row" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title" id="myModalLabel">预览(对内数据, 灰色数据为之前已导入) <button class="btn btn-default action-toggleduplicated">显示已导入数据</button></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="sendDataBtn">确认无误,上传</button>
                <button class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<div id='showPublicResultDialog' class="modal row" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title">预览(对外数据, 灰色数据为之前已导入) <button class="btn btn-default action-toggleduplicated">显示已导入数据</button></h4></h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="sendPublicDataBtn">确认无误,上传</button>
                <button class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<?php
Yii::app()->clientScript->registerScriptFile('/js/report_schema.js', CClientScript::POS_END);

?>
