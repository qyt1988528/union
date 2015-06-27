<?php
/**
 * Created by PhpStorm.
 * User: mortyu
 * Date: 14-8-25
 * Time: 上午10:37
 */

class ChannelController extends AdminController {

    public function getModelClass() {
        return 'Channel';
    }

    public function getTableName() {
        return "渠道列表";
    }

    /**
     * 返回界面上需要显示的字段名
     *
     * @return array 字段列表
     */
    public function getAdminAttributes() {
        return array('id', 'name', 'adv_number','contactor_email','contactor_phone','contactor');
    }

    /**
     *
     * 返回组成表单所需要的字段
     *
     */
    public function getFormAttributes(){
        $attrs = array();
        foreach(Channel::model()->attributeLabels() as $field => $label) {
            if(!in_array($field, array('id', 'mtime', 'ctime', 'status'))) {
                $attrs[$field] = array('type' => 'input');
            }
        }
        unset($attrs['adv_number']);
        return $attrs;
    }
    /*
     *表单提交数据处理
     * */
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
            //channel直接给用户一个帐号 ，密码默认为123456
            $user = new User();
            $data = $_POST['Attr'];
            $user->insertOneData($data['name'],$data['contactor_email'], 123456, $data['contactor_phone'], $model->id);

            return;
        }

        $this->renderPartial('/admin/form', array(
            'fields' => $this->getFormAttributes(),
            'title' => $model->getIsNewRecord() ? '新建' : '更新 #' . $model->id,
            'model' => $model
        ));
    }



    /**
     * @param $model
     * @param $field
     * @return mixed
     *
     * 表格中$field的显示内容
     */
    protected  function renderTableField($model, $field) {
        if($field == 'adv_number') {
            return "<a href='/admin/advertiseChannel/?f[channel_id]={$model->id}'>{$model->getAttribute($field)}</a>";
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

    /**
     * @return bool
     *
     * 是否允许选中，发送邮件
     * 默认不允许
     * 修改为允许
     */
    public function allowSelect(){
        return false;
    }
}
