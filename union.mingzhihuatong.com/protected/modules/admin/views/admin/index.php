<div class="row" xmlns="http://www.w3.org/1999/html">
    <div class="col-lg-12">
        <h4 class="page-header"><?php echo $this->getTableName();?>
            <?php if($this->allowAddRow()) { ?>
                <button class="btn btn-primary" id="btn-createNew">添加</button>
            <?php } ?>
        </h4>
        <?php if($this->allowSearch()){ ?>
        <form action="" method="get" >
        <table style="width: 45%;">
            <tr>
                <td>
                    <span style="font-size: 15px;font-weight: bold">关键词搜索:
                </td>
                <td>
                    <span>
                        <?php if($this->getTableName()=="CP列表"){
                            echo "名称或公司全称:";
                        }elseif($this->getTableName()=="渠道列表"){
                            echo "名称：";
                        }else{
                            echo "CP或名称：";
                        }
                        ?>
                        </span>
                    <input type="text" name="kw_search" id="kw_search"/>
                </td>
                <td>
                    <input type="submit" class="btn btn-primary"  value="搜索"/>
                </td>
            </tr>
        </table>
        </form>
        <?php }?>
    </div>
    <!-- /.col-lg-12 -->
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
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($dataProvider->getData() as $model) { ?>
                        <tr class="gradeA modelContainer" model-id="<?php echo $model->id?>">
                            <?php foreach($attributes as $attribute) { ?>
                                <td><?php echo $this->renderTableField($model, $attribute);?></td>
                            <?php } ?>
                            <td class="row-operations">
                                <?php  foreach($this->getRowAdminOperations($model) as $action) {?>
                                    <?php if($action['ajax']) { ?>
                                        <button class="btn btn-default operation" ajax-confirm=<?php echo (isset($action['ajax-confirm']) && $action['ajax-confirm']) ? "true" : "false"; ?>  ajax-action="<?php echo $action['ajax-action'];?>" ajax-target="<?php echo $action['ajax-target']; ?>"><?php echo $action['label'];?></button>
                                    <?php } else { ?>
                                        <a class="btn btn-default operation" href="<?php echo $action['href'];?>"><?php echo $action['label'];?></a>
                                    <?php } ?>
                                <?php }?>
                            </td>
                        </tr>
                    <?pHp } ?>
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
    <a id="return_top" style="position: fixed;bottom: 50px;right: 20px;width: 20px;line-height: 20px"
       href="javascript:;">回到顶部</a>
</div>
<?php
Yii::app()->clientScript->registerScriptFile('/js/loadform.js', CClientScript::POS_END);
?>

