<div class="wrapper" style="width:960px; margin: 0 auto;">
    <form class="form-inline" role="form" action="/site/index">
        <div class="form-group">
            <label class="control-label">起始时间</label>
            <input name='startTime' id="datepicker-start"  type="text" class="span2  form-control">
        </div>
        <div class="form-group">
            <label class="control-label">结束时间</label>
            <input name='endTime' type="text" id="datepicker-end"  class="span2  form-control" >
        </div>
        <div class="form-group" >
            <label class="control-label">业务</label>
            <input name="adv_name"  class="form-control">
        </div>
        <div class="form-group">
            <label class="control-label">包名</label>
            <input name="tag" class="form-control" >
        </div>
        <button class="btn btn-primary" >查询</button>
    </form>
   <br/>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTable no-footer"
                               id="dataTable" aria-describedby="dataTables-example_info">
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
                                       <?php if($attribute == 'star_level'){
                                           $star_level = $model->getAttribute($attribute);
                                           if($star_level == 1){
                                       ?>
                                       <td class="star_level"><div>&nbsp;&nbsp;&nbsp;</div></td>
                                       <?php }elseif($star_level == 2){?>
                                       <td class="star_level" ><div style="background-position:0 -23px;">&nbsp;&nbsp;&nbsp;</div></td>
                                       <?php }elseif($star_level == 3){?>
                                       <td class="star_level" ><div style="background-position:0 -47px;">&nbsp;&nbsp;&nbsp;</div></td>
                                       <?php }elseif($star_level == 4){?>
                                       <td class="star_level" ><div style="background-position:0 -70px;">&nbsp;&nbsp;&nbsp;</div></td>
                                       <?php }elseif($star_level == 5){?>
                                       <td class="star_level" ><div style="background-position:0 -93px;">&nbsp;&nbsp;&nbsp;</div></td>
                                       <?php }else{?>
                                       <td align="center">--</td>
                                       <?php }?>
                                        <?php }else{?>
                                        <td><?php echo $model->getAttribute( $attribute);?></td>
                                    <?php } }?>
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
                                    <div>总共<?php echo $dataProvider->getTotalItemCount();?>条数据</div>
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
    </div>
</div>
<?php
Yii::app()->clientScript->registerCssFile('/css/bootstrap.min.css');
Yii::app()->clientScript->registerCssFile('/css/home.css');
Yii::app()->clientScript->registerCssFile('/css/bootstrap-datetimepicker.css');
Yii::app()->clientScript->registerCssFile('/css/bootstrap-datetimepicker.min.css');
Yii::app()->clientScript->registerScriptFile('/js/bootstrap-datepicker.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/bootstrap-datetimepicker.min.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/home.js', CClientScript::POS_END);
?>

<?php $this->beginClip('topbar-rightside') ?>
<?php if(Yii::app()->user->isAdmin()) { ?>
    <div class="pull-right">
        <form class="form-inline form-horizontal" role="form">
            <div class="form-group" >
                <label class="control-label">渠道用户视图</label>
                <select class="form-control" id="channelSwitch">
                    <option value="">不限</option>
                    <?php foreach($channels as $channel) { ?>
                        <option value="<?php echo $channel->id;?>"<?php if($channel->id == $current_channel_id) echo ' selected';?>><?php echo $channel->name;?></option>
                    <?php } ?>
                </select>
            </div>
        </form>
        <span><?php echo Yii::app()->user->name . (Yii::app()->user->isAdmin() ? '(管理员)' : '');?></span>
        <a href="/site/logout/">退出</a>
    </div>
<?php }else{ ?>
    <div style="height:40px;float:right; margin-top:25px; font-size:16px;"><a href="/site/logout/">退出</a></div>
<?php }?>
<?php $this->endClip() ?>

