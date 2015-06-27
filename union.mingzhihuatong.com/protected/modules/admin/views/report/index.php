<div class="row">
    <div class="col-lg-12">
        <h4 class="page-header"><?php echo $this->getTableName();?></h4>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
    <div class="col-lg-12 well">
        <form class="form-inline" role="form" action="/admin/<?php echo $this->getId();?>/">
            <?php foreach($filter as $key=>$value) { if($key != 'begin_date' && $key != 'end_date') { ?>
                <input type="hidden" name="f[<?php echo $key;?>]" value="<?php echo $value;?>" />
            <?php }} ?>
            <div class="form-group">
                <label class="control-label">起始时间</label>
                <input name='f[begin_date]' type="text" class="span2 datepicker form-control" value="<?php echo $filter['begin_date']?$filter['begin_date']:'';?>">
            </div>
            <div class="form-group">
                <label class="control-label">结束时间</label>
                <input name='f[end_date]' type="text" class="span2 datepicker form-control" value="<?php echo $filter['end_date']?$filter['end_date']:'';?>">
            </div>
            <!--2014-10-27 增加CP搜索-->
            <div class="form-group">
                <label class="control-label">CP</label>
                <input name="cp_name" id="cp_name" autocomplete="off" class="form-control" data-provide="typeahead">
                <input type="hidden" name="f[cp_id]" class="form-control" id="cp_id">
                <!--
                <select name="f[cp_id]" class="form-control" id="cp_after">
                    <option value=''>不限</option>
                    < ?php foreach($cp as $cp) { ?>
                        <option value="< ?php echo $cp->id;?>"< ?php if($filter['cp_id']==$cp->id) echo ' selected'; ?> >< ?php echo $cp->name;?></option>
                    < ?php } ?>
                </select>
                -->
            </div>
            <!--添加end-->
            <div class="form-group" >
                <label class="control-label">业务</label>
                <input name="adv_name" id="adv_name" autocomplete="off" class="form-control" data-provide="typeahead">
                <input type="hidden" name="f[adv_id]" class="form-control" id="adv_id">
                <!--
                <select name="f[adv_id]" class="form-control" id="adv_after">
                    <option value=''>不限</option>
                    < ?php foreach($advertises as $adv) { ?>
                        <option value="< ?php echo $adv->id;?>"< ?php if($filter['adv_id']==$adv->id) echo ' selected'; ?> >< ?php echo $adv->name;?></option>
                    < ?php } ?>
                </select>
                -->
            </div>
            <div class="form-group" >
                <label class="control-label">渠道</label>
                <input name="channel_name" id="channel_name" autocomplete="off" class="form-control" data-provide="typeahead">
                <input type="hidden" name="f[channel_id]" class="form-control" id="channel_id">
                <!--
                <select name="f[channel_id]" class="form-control" id="chan_after">
                    <option value=''>不限</option>
                    < ?php foreach($channels as $channel) { ?>
                        <option value="< ?php echo $channel->id;?>"< ?php if($filter['channel_id']==$channel->id) echo ' selected'; ?> >< ?php echo $channel->name;?></option>
                    < ?php } ?>
                </select>
                -->
            </div>
            <div class="form-group" >
                <label class="control-label">包名</label>
                <input name="tag_name" id="tag_name" autocomplete="off" class="form-control" data-provide="typeahead">
                <input type="hidden" name="f[adv_channel_id]" class="form-control" id="adv_channel_id">
            </div>
            <button class="btn btn-primary" >查询</button>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer"
                           id="dataTable" aria-describedby="dataTables-example_info" form-action="<?php echo $this->getFormUrl();?>">
                        <thead>
                        <tr>
                            <?php foreach($attributes as $index => $attribute) {?>
                            <th><?php echo $dataProvider->model->getAttributeLabel(is_integer($index) ? $attribute : $index);?></th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($dataProvider->getData() as $model) { ?>
                        <tr class="gradeA modelContainer" model-id="<?php echo $model->id?>">
                            <?php foreach($attributes as $attribute) { ?>
                                <td><?php echo $this->renderTableField($model, $attribute); ?></td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                <?php
                                $this->widget('BootstrapLinkPager', array(
                                    'pages' => $dataProvider->getPagination()
                                ));
                                ?>
                               <div>总共<?php echo $dataProvider->getTotalItemCount();?>条数据 下载量合计:<?php echo $downloadSum; ?>个, 费用合计:<?php echo $totalPriceSum; ?></div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
    <a id="return_top" style="position: fixed;bottom: 50px;right: 20px;width: 20px;line-height: 20px"
       href="javascript:;">回到顶部</a>
</div>
<?php
Yii::app()->clientScript->registerScriptFile('/js/loadform.js', CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile('/css/datepicker3.css');
Yii::app()->clientScript->registerScriptFile('/js/bootstrap-datepicker.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/locales/bootstrap-datepicker.zh-CN.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/bootstrap3-typeahead.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/underscore.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/code-search.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('datepicker', <<<JS
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        language: "zh-CN"
    });
JS
);
?>