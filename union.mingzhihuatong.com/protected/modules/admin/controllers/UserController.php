<?php

class UserController extends AdminController {

    public function getModelClass() {
        return 'User';
    }

    /**
     *
     * 返回组成表单所需要的字段
     *
     */
    public function getFormAttributes($role){
        $attrs = array('name', 'email', 'phone');
        if($role == User::ROLE_ADMIN) {
            return $attrs;
        } else if($role == User::ROLE_CHANNEL) {
            $attrs['channel_id'] = array(
                'type' => 'select',
                'options' => $this->getChannelOptions()
            );
            return $attrs;
        } else {
            return array();
        }
    }

    private function getChannelOptions() {
        $options = array();
        $criteria = new CDbCriteria;
        $criteria->order = 'ctime desc';
        foreach(Channel::model()->findAll($criteria) as $channel) {
            $options[$channel->id] = $channel->name;
        }
        return $options;
    }

    /**
     * 返回界面上需要显示的字段名
     *
     * @return array 字段列表
     */
    public function getAdminAttributes() {
        return array('name', 'email', 'phone', 'role');
    }

    /**
     * @param $model
     * @param $field
     * @return mixed
     *
     * 表格中$field的显示内容
     */
    protected  function renderTableField($model, $field) {
        if($field == 'role') {
            if($model->isAdmin()) {
                return '管理员';
            } elseif($model->isChannel()) {
                return '渠道:'.($model->channel ? $model->channel->name : '');
            }
        }
        return $model->getAttribute($field);
    }

    public function handleForm($model, $attr) {
        $model->attributes = $attr;
        if($model->getIsNewRecord() && $model->role == User::ROLE_ADMIN) {
            $errors = User::createAdmin($attr);
        } else {
            $errors = $model->save() ? true: $model->getErrors();
        }
        $response = array();
        $response['code'] = $errors === true ? 0 : -1;
        $response['message'] = $errors === true ? '保存成功' : '保存失败';
        $response['data'] = $errors;
        echo json_encode($response);
    }

    public function getCurrentMenuKey() {
        return $this->isAdminUserMode() ? 'user' : 'channelUser';
    }

    /**
     * @return string
     *
     * 返回表的名字
     */
    public function getTableName() {
        return $this->isAdminUserMode() ? '管理员用户列表' : '渠道用户列表';
    }

    private function isAdminUserMode() {
        $filters = $this->getFilterCondition();
        if($filters && isset($filters['role']) && $filters['role'] == User::ROLE_ADMIN) {
            return true;
        }
        return false;
    }


    /**
     * @return bool
     *
     * 是否需要“新建”按钮
     */
    public function allowAddRow(){
        return $this->isAdminUserMode();
    }

    public function actionForm($id=0) {
        $id = intval($id);
        $model_class = $this->getModelClass();

        if($id == 0) {
            $model = new $model_class;
            $filters = $this->getFormFilters();
            foreach($filters as $field => $value) {
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
            'fields' => $this->getFormAttributes($model->role),
            'title' => $model->getIsNewRecord() ? '新建' : '更新 #' . $model->id,
            'model' => $model
        ));
    }

}
