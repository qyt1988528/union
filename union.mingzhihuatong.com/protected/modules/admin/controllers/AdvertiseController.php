<?php

class AdvertiseController extends AdminController {

    public function actionIndex($kw_search="") {
        $dataProvider = new CActiveDataProvider($this->getModelClass(), array(
                'criteria' => $this->getIndexCriteria($kw_search),
                'pagination' => array(
                    'pageSize' => $this->getPagesize()
                )
            )
        );
        $this->render('/admin/index', array(
            'dataProvider' => $dataProvider,
            'attributes' => $this->getAdminAttributes()
        ));
    }
   
    public function allowAddRow(){
        return true;
    }

    public function actionUpload() {
        $filter = $this->getFilterCondition();
        if(isset($filter['adv_channel_id'])) {
            $advertiseChannel = AdvertiseChannel::model()->findByPk($filter['adv_channel_id']);
            $advertise = $advertiseChannel->advertise;
        } else if(isset($filter['adv_id'])){
            $advertise = Advertise::model()->findByPk($filter['adv_id']);
        }
        
               

        //$historyList = $adv_data->getDayDataForIn('',$startTime, $endTime, false);
        $this->render('upload', array(
            'filters' => $this->getFilterCondition(),
            'advertise' => $advertise,
            'adv_channel_id' => $filter['adv_channel_id']
        ));
    }

    public function actionHistory(){
        $getParams = $this->getFilterCondition();
        $adv_id = $getParams['adv_id'];
        $cp_id = $getParams['cp_id'];

        $criteria = $this->getIndexCondition($cp_id);
        $dataProvider = new CActiveDataProvider('AdvData', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $this->getPagesize(),
                'pageVar' => 'page'
            )
           )
       );

        $this->render('history',array(
            'dataProvider'=>$dataProvider,
            'attributes'=>$this->getAdvDataAttributes(),
            'adv_id'=>$adv_id
        ));

    }
    public function getAdvDataAttributes() {
        return array(
			'date',
            'cp.name',
            'adv.name',
            'channel.name',
            'tag',
            /*
			'install_number',
            'active_number',
            'new_user',
            'left_user_2days',
            'left_user_7days',
            'left_user_14days',
            'convert_ratio',
            */
            'download_number',
            'income_price',//接入价
            'price',
            'profit_margin',//利润率
            'running_account',//流水
            'total_price',
            'profit',//利润
            'uploader.name',
            'ctime',
            'mtime'
		);
    }

    public function getIndexCondition($cp_id){
        $criteria = new CDbCriteria();
        $criteria->order = 'date desc';
        $criteria->compare('upload_user_id', Yii::app()->user->id);
        $criteria->compare('cp_id', $cp_id);

        return $criteria;

    }
    public function getModelClass() {
        return 'Advertise';
    }

    public function getTableName() {
        $filters = $this->getFilterCondition();
        if(isset($filters['cp_id'])) {
            $cp = CP::model()->findByPk($filters['cp_id']);
            if($cp) {
                return $cp->name.'-业务列表';
            }
        }
        return '业务列表';
    }

    public function getAdminAttributes() {
        return array('id', 'cp.name', 'name', 'income_price', 'outcome_price', 'description', 'cp_admin_url', 'ctime', 'mtime');
    }

    public function getFormAttributes(){
        return array(
            'name' => array(
                'type' => 'input'
            ),
            'cp_id' => array(
                'type' => 'select',
                'options' => $this->getCpOptions()
            ),
            'income_price' => array(
                'type' => 'input'
            ),
            'outcome_price' => array(
                'type' => 'input'
            ),
            'description' => array(
                'type' => 'textarea'
            )
        );
    }

    private function getCpOptions() {
        $options = array();
        foreach(CP::model()->findAll() as $cp) {
            $options[$cp->id] = $cp->name;
        }
        return $options;
    }

    public function getRowAdminOperations($model) {
        return array_merge(parent::getRowAdminOperations($model), array(
            'view-adv' => array(
                'label' => '业务包管理',
                'ajax' => false,
                'href' => '/admin/advertiseChannel/?f[adv_id]='. $model->id
            ),
            'upload-data' => array(
                'label' => '上传业务数据',
                'ajax' => false,
                'href' => '/admin/advertise/upload/?f[adv_id]='. $model->id."&f[cp_id]=".$model->cp_id
            )
        ));
    }

    public function getSchemaFields() {
        $model = AdvData::model();
        $fields = array(
            'date',
            'tag',
            'download_number',
            'star_level',
            'commands',
            'active_number',
            'new_user',
            'left_user_2days',
            'left_user_7days',
            'left_user_14days'
        );
        $options = array();
        foreach($fields as $field) {
            $options[$field] = $model->getAttributeLabel($field);
        }
        return $options;
    }

    /**
     * @param $model
     * @param $field
     * @return mixed
     *
     * 表格中$field的显示内容
     */
    protected  function renderTableField($model, $field) {
        if($field == 'description') {
           return '<div style="text-align:left;">'.nl2br($model->getAttribute($field)).'</div>';
        }
        return $model->getAttribute($field);
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
