<?php
/**
 * Created by PhpStorm.
 * User: mortyu
 * Date: 14-8-22
 * Time: 上午9:49
 */

class CpController extends AdminController {

    public function getModelClass() {
        return 'CP';
    }

    public function getRowAdminOperations($model) {
        return array_merge(parent::getRowAdminOperations($model), array(
            'view-adv' => array(
                'label' => '查看业务',
                'ajax' => false,
                'href' => '/admin/advertise/?f[cp_id]='. $model->id
            )
        ));
    }

    /**
     * 返回新建的Url
     */
    public function getCreateOperation() {
        return '/admin/'.$this->getId().'/create/';
    }

    /**
     * @return bool
     *
     * 是否需要“新建”按钮
     */
    public function allowAddRow(){
        return true;
    }

    /**
     * @return string
     *
     * 返回表的名字
     */
    public function getTableName() {
        return "CP列表";
    }

    /**
     *
     * 返回组成表单所需要的字段
     *
     */
    public function getFormAttributes(){
		return array(
			'name',
            'fullname',
			'contact_name',
			'contact_phone',
            'contact_email',
		);
	}

    /**
     * @return bool
     * 是否需要搜索
     * 默认不需要
     * 修改为需要
     */
    public function allowsearch(){
        return true;
    }
}
