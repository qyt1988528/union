<?php

Yii::import('application.modules.admin.controllers.ReportController');

class ReportPublicController extends ReportController {
    public function getModelClass() {
        return 'AdvDataForChannel';
    }

    public function getTableBaseName() {
        return '业务报表(渠道)';
    }
    /*
     * 给渠道看的数据，删除接入价 利润率 流水 利润
     * */
    public function getAdminAttributes() {
        return array(
            'date',
            'adv.name',
            'tag',
            'download_number',
            'price',
            'total_price',
        );
    }
}