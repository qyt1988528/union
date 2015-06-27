<div style="font-weight:bold;font-size:18px; margin-top:40px; margin-bottom:40px;">
    历史数据  
    <div><input type="button" id="reback" class="btn btn-default" value="返回上传页面"/></div>
    <input type="hidden" value="<?php echo $adv_id;?>" id="adv_id"/>
</div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer"
        id="dataTable" aria-describedby="dataTables-example_info" >
        <thead>
            <tr>
                <?php foreach($attributes as $index => $attribute) {?>
                <th><?php echo $dataProvider->model->getAttributeLabel(is_integer($index) ? $attribute : $index);?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
          <?php
            foreach($dataProvider->getData() as $model) { ?>
            <tr class="gradeA modelContainer" model-id="<?php echo $model->id?>">
                <?php foreach($attributes as $attribute) { ?>
                <td><?php echo $this->renderTableField($model, $attribute); ?></td>
                <?php } ?>
            </tr>
            <?php }?>
        </tbody>
    </table>

    <div class="row">
        <div class="col-sm-12">
            <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                <div id="showDialog">
                <?php
                $this->widget('BootstrapLinkPager', array(
                    'pages' => $dataProvider->getPagination()
                ));
                 ?>
                </div>
             <div>总共<?php echo $dataProvider->getTotalItemCount();?>条数据</div>
           </div>
        </div>
     </div>


 </div>
<?php
Yii::app()->clientScript->registerScriptFile('/js/report_schema.js', CClientScript::POS_END);
?>
