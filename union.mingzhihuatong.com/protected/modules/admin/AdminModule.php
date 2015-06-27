<?php

class AdminModule extends CWebModule {
    public $defaultController = 'report';
    public function init() {
        Yii::app()->user->loginUrl = '/admin/login/';
    }
}

class AdminController extends CController {
    public $layout = "logged";
    public $pageTitle = '铭智联盟后台管理';


    public function filters() {
        return array(
            'accessControl - login'
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'users' => array('@'),
                'roles' => array('admin')
            ),
            array('deny')
        );
    }

    public function actionIndex($kw_search="") {
        $dataProvider = new CActiveDataProvider($this->getModelClass(), array(
                'criteria' => $this->getIndexCriteria($kw_search),
                'pagination' => array(
                    'pageSize' => $this->getPagesize(),
                    'pageVar' => 'page',
                )
            )
        );

        $this->render('/admin/index', array(
            'dataProvider' => $dataProvider,
            'attributes' => $this->getAdminAttributes()
        ));
    }


    public function actionDelete() {
        if(!Yii::app()->request->getIsPostrequest()) {
            header("http/1.1 400 bad request");
            return;
        }
        if(!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            header("http/1.1 400 bad request");
            return;
        }
        $id = intval($_POST['id']);
        $modelClass = $this->getModelClass();
        $model = new $modelClass();
        $model = $model->findByPk($id);
        $result = array();
        if(!$model) {
            $result['code'] = -1;
            $result['message'] = '删除失败: 数据不存在';
        } else if(!$model->delete()){
            $result['code'] = -1;
            $result['message'] = '删除失败: 数据不存在';
            $result['data'] = $model->getErrors();
        } else {
            $result['code'] = 0;
            $result['message'] = '删除成功';
        }
        echo json_encode($result);
    }

    public function actionForm($id=0) {
        $id = intval($id);
        $model_class = $this->getModelClass();
        if($id == 0) {
            $model = new $model_class;

            foreach($this->getFormFilters() as $field => $value) {
                $model->setAttribute($field, $value);
            }
        } else {
            $model = $model_class::model()->findByPk($id);
            if($model == null) {
                echo "数据不存在，可能已被删除";
                return;
            }
        }

        if(Yii::app()->request->getIsPostRequest()) {
            
            $this->handleForm($model, $_POST['Attr']);
            return;
        }

        $this->renderPartial('/admin/form', array(
            'fields' => $this->getFormAttributes(),
            'title' => $model->getIsNewRecord() ? '新建' : '更新 #' . $model->id,
            'model' => $model
        ));
    }

    /**
     *
     * 因为我们的form是ajax拉取的，因此当前页面实际是Referer的值 取出当前的过滤器，新建数据的时候，设置默认值
     */
    protected function getFormFilters() {
        $url = $_SERVER['HTTP_REFERER'];
        $filters = array();
        if(!$url) {
            return $filters;
        }
        $query = parse_url($url, PHP_URL_QUERY);
        if(!$query) {
            return $filters;
        }
        parse_str($query, $get);
        if(isset($get['f'])) {
            return $get['f'];
        } else {
            return $filters;
        }
    }

    public function handleForm($model, $attr) {
        $response = array();

        $model->attributes = $attr;
        try{

            if($model->save()) {
                //只保存那些传上来的字段
                $response['code'] = 0;
                $response['message'] = '保存成功';
            }else {
                $response['code'] = -1;
                $response['message'] = '保存失败';
                $response['data'] = $model->getErrors();
            }

        }catch (Exception $e){
            if($e->getCode() ==23000){
                $model->addError('name', '该姓名已存在');
                $response['code'] = 23000;
                $response['message'] = '保存失败';
                $response['data'] = $model->getErrors();
            }else{
                return;
            }

        }
        echo json_encode($response);
    }

    public function getFormActionUrl() {
        return $_SERVER['REQUEST_URI'];
    }

    public function getFormUrl() {
        return '/admin/'. $this->getId(). '/form/';
    }

    public function getModelClass() {
        return ucfirst($this->getId());
    }

    /**
     *
     * 返回组成表单所需要的字段
     *
     */
    public function getFormAttributes(){
        $attrs = array();
        foreach($this->getAdminAttributes() as $field) {
            if(!in_array($field, array('id', 'mtime', 'ctime', 'status'))) {
                $attrs[$field] = array('type' => 'input');
            }
        }
        return $attrs;
    }

    /**
     * 返回界面上需要显示的字段名
     *
     * @return array 字段列表
     */
    public function getAdminAttributes() {
        $modelClass = $this->getModelClass();
        return array_keys($modelClass::model()->attributeLabels());
    }

    /**
     * 返回界面上每一行可以做的的操作按钮
     * @return array 配置项列表
     *
     * label 显示的操作名称
     * js-action
     */
    public function getRowAdminOperations($model) {
        return array(
            'update' => array(
                'label' => '更新',
                'ajax-action' => "update",
                'ajax-target' => '/admin/' . $this->getId(). '/form/',
                'ajax' => true,
            ),
            'delete' => array(
                'label' => '删除',
                'ajax-action' => "delete",
                'ajax-confirm' => true,
                'ajax-target' => '/admin/' . $this->getId(). '/delete/',
                'ajax' => true,
            )
        );
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
     * @return bool
     *
     * 是否需要搜索
     * 默认不需要
     */
    public function allowSearch(){
        return false;
    }
    /**
     * @return bool
     *
     * 是否允许上传
     * 默认不允许
     */
    public function allowUpload(){
        return false;
    }
    /**
     * @return bool
     *
     * 是否允许选中，发送邮件
     * 默认不允许
     */
    public function allowSelect(){
        return false;
    }
    /**
     * @return string
     *
     * 返回表的名字
     */
    public function getTableName() {
        return $this->getId()."列表";
    }

    public function getPagesize() {
        return 40;
    }

    protected function getIndexCriteria($kw_search="") {
        $criteria = new CDbCriteria();
        $criteria->order = 't.ctime desc';
        /* 添加搜索条件 10-29*/
        if($this->getModelClass()=='CP'){
            $criteria->addSearchCondition('name',$kw_search,true,'OR');
            $criteria->addSearchCondition('fullname',$kw_search,true,'OR');
        }elseif($this->getModelClass()=='Advertise'){
            $first = new CDbCriteria();
            $first->addSearchCondition('name',$kw_search);
            $cp=CP::model()->findAll($first);
            $i = 0;
            foreach($cp as $c){
                $arrcp[$i] = $c->id;
                $i++ ;
            }
            $criteria->addInCondition('cp_id',$arrcp,'OR');
            $criteria->addSearchCondition('name',$kw_search,true,'OR');
        }elseif($this->getModelClass()=='Channel'){
            $criteria->addSearchCondition('name',$kw_search);
        }else{

        }
        foreach($this->getFilterCondition() as $field => $value) {
            //不加t. ActiveRecord join的时候容易出现重名字段冲突
            $criteria->compare('t.'.$field, $value);
        }
        return $criteria;
    }

    /**
     * 读取传进来的过滤参数
     *
     */
    public function getFilterCondition() {
        $filters = array();
        if(isset($_GET['f']) && is_array($_GET['f'])) {
            foreach($_GET['f'] as $field => $value) {
                if($value !== '') {
                    $filters[$field] = $value;
                }
            }
        }
        return $filters;
    }

    public function getNavMenuConfig() {
        //左侧菜单项配置
        return array(
            
            'report' => array(
                'label' => '业务报表',
                'url' => '/admin/report/?f[begin_date]='.date('Y-m-01'). '&f[end_date]='. date('Y-m-d')
            ),
            /*
            'reportPublic' => array(
                'label' => '业务报表(渠道)',
                'url' => '/admin/reportPublic/?f[begin_date]='.date('Y-m-01'). '&f[end_date]='. date('Y-m-d')
            ),
             */
            'cp' => array(
                'label' => 'CP管理',
                'url' => '/admin/cp/'
            ),
            'advertise' => array(
                'label' => '业务管理',
                'url' => '/admin/advertise/'
            ),
            'channel' => array(
                'label' => '渠道管理',
                'url' => '/admin/channel/'
            ),
            'user' => array(
                'label' => '管理员账号管理',
                'url' => '/admin/user/?f[role]='. User::ROLE_ADMIN
            ),
             'channelUser' => array(
                'label' => '渠道账号管理',
                'url' => '/admin/user/?f[role]='. User::ROLE_CHANNEL
            ),
            'noticemanager'=>array(
                'label'=>'公告管理',
                'url'=>'/admin/notice/'
            ),

        );
    }

    public function getCurrentMenuKey() {
        return $this->id;
    }

    /**
     * @param $model
     * @param $field
     * @return mixed
     *
     * 表格中$field的显示内容
     */
    protected  function renderTableField($model, $field) {
        return $model->getAttribute($field);
    }

    

    public function  setMailContents($content){
        $result = <<<EOF
        {$content}
EOF;
        return $result;

    }

}


