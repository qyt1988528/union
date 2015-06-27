<?php
/**
 * Created by PhpStorm.
 * User: mortyu
 * Date: 14-8-26
 * Time: 上午7:30
 */

class ReportController extends AdminController{
    public function getModelClass() {
        return 'AdvData';
    }

    public function getAdminAttributes() {
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

    public function getEditType($field) {
        if(in_array($field, array(
            'download_number',
			'install_number',
            'active_number',
            'new_user',
            'left_user_2days',
            'left_user_7days',
            'left_user_14days',
            'convert_ratio',
        ))) {
            return 'input';
        } else {
            return '';
        }
    }

    public function actionIndex() {
        $criteria = $this->getIndexCriteria();
        $dataProvider = new CActiveDataProvider($this->getModelClass(), array(
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => $this->getPagesize(),
                    'pageVar' => 'page',
                )
            )
        );

        list($downloadSum, $totalPriceSum) = CActiveRecord::model($this->getModelClass())->sum(array('download_number', 'total_price'), $criteria);

        /*模糊搜索start*/
        $filter = $this->getFilterCondition();
        $cp_id  = $filter['cp_id'];
        $adv_id = $filter['adv_id'];

        /*获得满足$cp_id的Advertise 10-28*/
        $cpadv = new CDbCriteria();
        $cpadv->order = 'id ASC';
        $cpadv->compare('cp_id',$cp_id);
        $advertises = Advertise::model()->findAll($cpadv);
        /*获得满足$adv_id的Channel 10-28*/
        $criteria = new CDbCriteria();
        $criteria->select = "channel_id";
        $criteria->order = "channel_id asc";
        $criteria->compare('adv_id',$adv_id);
        $criteria->distinct = true ;
        $advtertises = AdvertiseChannel::model()->findAll($criteria);
        $i=0;
        foreach($advtertises as $a){
            $arr[$i]=$a->channel->id;
            $i++;
        }
        $criter = new CDbCriteria();
        $criter->addInCondition('id',$arr);
        $criter->order ="name desc";
        $criter->distinct = true ;
        $channels = Channel::model()->findAll($criter);
        /*10-28 end*/

        $this->render('/report/index', array(
            'downloadSum' => $downloadSum ? $downloadSum : '0',
            'totalPriceSum' => $totalPriceSum ? $totalPriceSum : '0.00',
            'dataProvider' => $dataProvider,
            'filter' => $filter,//$this->getFilterCondition()
            'cp' => CP::model()->findAll(),
            /*原有代码
            'advertises' => Advertise::model()->findAll(),
            'channels' => Channel::model()->findAll(),
            */
            'advertises' => $advertises,
            'channels' => $channels,
            'attributes' => $this->getAdminAttributes()
        ));
    }
    /*
     * 获取对应所有CP、Advertise、Channel 11-04
    * qyt
    * */
    public function actionAllcpadvchan(){
        $this->renderPartial('/report/allcpadvchan', array(
            'cp' => CP::model()->findAll(),
            'advertises' => Advertise::model()->findAll(),
            'channels' => Channel::model()->findAll(),
        ));
    }
    /*
    * 获取对应所有CP 11-04
    * qyt
    * */
    public function actionAllcp(){
        $this->renderPartial('/report/allcp', array(
            'cp' => CP::model()->findAll(),
        ));
    }
    /*
     * 获取对应cp_id的adv 10-27
     * qyt
     * */
    public function actionCpadv($cp_id=""){
        $criteria = new CDbCriteria();
        $criteria->order = 'id ASC';
        $criteria->compare('cp_id',$cp_id);      //根据条件查询
        $advertises = Advertise::model()->findAll($criteria);
        $this->renderPartial('/report/cpadv', array(
            'advertises' => $advertises,
        ));
    }
    /*
     * 获取对应adv_id的channel_id 10-27
     * qyt
     * */
    public function actionAdvchan($adv_id=""){

        $criteria = new CDbCriteria();
        $criteria->select = "channel_id";
        $criteria->order = "channel_id asc";
        $criteria->compare('adv_id',$adv_id);      //根据条件查询
        $criteria->distinct = true ;
        $advtertises = AdvertiseChannel::model()->findAll($criteria);
        //$advtertises = AdvertiseChannel::model()->with('channel')->findAll($criteria);
        $i=0;
        foreach($advtertises as $a){
            $arr[$i]=$a->channel->id;
            $i++;
        }
        $criter = new CDbCriteria();
        $criter->select="id,name";
        $criter->addInCondition('id',$arr);
        $criter->order ="name desc";
        $criter->distinct = true ;
        $channels = Channel::model()->findAll($criter);
        /*实现以下查询
        $sql="select distinct(channel.name) from {{advertise_channel}} join {{channel}}
              on advertise_channel.channel_id=channel.id where advertise_channel.adv_id="+$adv_id;
        */
        $this->renderPartial('/report/advchan', array(
            'channels' => $channels,
        ));
    }
    /*
     * */
    public function actionSearchTagByChannel($channel_id=""){
        $criteria = new CDbCriteria();
        $criteria->select = "id,tag";
        $criteria->compare('channel_id',$channel_id);
        $criteria->distinct = true;
        $adv_channels = AdvertiseChannel::model()->findAll($criteria);
        $this->renderPartial('/report/searchTagByChannel',array(
            'adv_channels' => $adv_channels,
        ));
    }
    /*
     * 11-05,添加删除功能
     *
     * */
    public function getRowOperations($model) {
        return array(
            'delete' => array(
                'label' => '删除',
                'ajax-action' => "delete",
                'ajax-confirm' => true,
                'ajax-target' => '/admin/report/delete/',
                'ajax' => true,
            )
        );
    }
    /*
     * 11-07,检查时间
     *
     * */
    public function checkExpireTime($time) {
        $ctime =strtotime($time);//获得数据创建时间
        $secondday =strtotime('+1 day',$ctime);
        $secondnight = date('Y-m-d 19:00:00',$secondday);
        $secondnight = strtotime($secondnight);//获得数据创建次日19点的时间
        if(time() < $secondnight ){
            return true;
        }else{
            return false;
        }
    }



    public function actionPreviewUpload($type='private') {
        $data = $this->convertTextTo2DArray($_POST['data']);
        if(isset($_POST['adv_id']) && $_POST['adv_id']) {
            //上传业务下多个渠道的数据
            $advertise = Advertise::model()->findByPk($_POST['adv_id']);
            $models = $advertise->generateAdvChannelData($data, $type == 'private');
        }

        $this->renderPartial('preview', array(
            'fields' => $this->getAdminAttributes(),
            'advertiseChannel' => $advertiseChannel,
            'models' => $models ? array_filter($models, function($model) {
                return $model->download_number != 0;
            }) : array()
            ));
    }

    public function actionUpload() {
        $data = $this->convertTextTo2DArray($_POST['data']);
        if(isset($_POST['adv_id']) && $_POST['adv_id']) {
            //上传业务下多个渠道的数据
            $advertise = Advertise::model()->findByPk($_POST['adv_id']);
            $models = $advertise->generateAdvChannelData($data, true);
        }

        $results = array();
        foreach($models as $model){
            
            $adv_channel_id = $model->adv_channel_id;
            $date = $model->date;
            $tag = $model->tag;
            
            $update_data = array(
                'tag'=>$model->tag,
                'download_number'=>$model->download_number,
                'profit'=>$model->profit,
                'profit_margin'=>$model->profit_margin,
                'running_account'=>$model->running_account,
                'total_price'=>$model->total_price,
                'price'=>$model->price,
                'income_price'=>$model->income_price,
                'star_level'=>$model->star_level
            );
            //update
            $dataList = AdvData::model()->getAdvCtime($date, $tag);
            if($dataList){
                $ctime = $dataList->ctime;
            }
            if($model->isDataExists() && $this->checkExpireTime($ctime)){
                $results[] = $model->updateByDateandAdvChannelId($date, $tag, $update_data);
            }else{
                try{

                    if(!$model->isDataExists() && $model->save()) {
                        $results[] = $model->id;
                    }
                }catch(Exception $e) {
                    Yii::log(json_encode(array(
                        'error' =>  $model->getErrors(),
                    )), 'error', __CLASS__.'.'.__FUNCTION__);
                }
            }
        }

        $counts = count($results);
        echo json_encode(array(
            'code' => count($results) != 0 ? 0 : -1,
            'message' => count($results) != 0 ? '上传成功' : '上传失败'
        ));
    }

    /**
     *
     *上传对外（渠道）公开的数据
     */
    public function actionUploadPublic() {
        $adv_channel_id = $_POST['adv_channel_id'];
        $advertiseChannel = AdvertiseChannel::model()->findByPk($adv_channel_id);
        $advertise = $advertiseChannel->advertise;
        $results = array();
        $successNumber = 0;
        $failNumber = 0;

        if(isset($_POST['adv_channel_id']) && $_POST['adv_channel_id']) {
            //只上传单个渠道的数据
            $adv_channel_id = $_POST['adv_channel_id'];
            $advertiseChannel = AdvertiseChannel::model()->findByPk($adv_channel_id);
            $advertise = $advertiseChannel->advertise;
        } else if(isset($_POST['adv_id']) && $_POST['adv_id']) {
            //上传业务下多个渠道的数据
            $advertise = Advertise::model()->findByPk($_POST['adv_id']);
        }

        foreach($_POST['data'] as $row) {
            $model = new AdvDataForChannel();
            $model->adv_id = $advertise->id;
            $model->cp_id = $advertise->cp_id;
            $model->upload_user_id = Yii::app()->user->id;
            foreach($this->getAdminAttributes() as $index => $field) {
                if(strpos('.', $field) === false ) {
                    //$field名字中如果包含点，说明是关联的属性，不需要赋值
                    $model->setAttribute($field, $row[$index]);
                    if($field == 'tag') {
                        $advChannel = $advertise->getAdvChannelByTag($row[$index]);
                        $model->channel_id = $advChannel->channel_id;
                        $model->adv_channel_id = $advChannel->id;
                        $model->tag = $advChannel->tag;
                    } else if($advertiseChannel) {
                        $model->channel_id = $advertiseChannel->channel_id;
                        $model->adv_channel_id = $advertiseChannel->id;
                        $model->tag = $advertiseChannel->tag;
                    }
                }
            }
            try{
                if($model->save()) {
                    $results[$index] = true;
                    $successNumber++;
                } else {
                    $results[$index] = false;
                    $failNumber++;
                }
            }catch(Exception $e) {
                $results[$index] = false;
                $failNumber++;
                 Yii::log(json_encode(array(
                    'error' =>  $model->getErrors(),
                )), 'error', __CLASS__.'.'.__FUNCTION__);
            }
        }

        echo json_encode(array(
            'code' => $failNumber == 0 ? 0 : -1,
            'message' => "上传结果: 成功{$successNumber}条,失败{$failNumber}条"
        ));
    }

    public function getRowAdminOperations($model) {
        return array();
    }

    /**
     * @return bool
     *
     * 是否需要“新建”按钮
     */
    public function allowAddRow(){
        return false;
    }

    public function getTableBaseName() {
        return '业务报表';
    }


    public function getTableName() {
        $filters = $this->getFilterCondition();
        $conditions = array();
        if(isset($filters['cp_id'])) {
            $model = CP::model()->findByPk($filters['cp_id']);
            if($model) {
                $conditions[] = "CP:{$model->name}";
            }
        }
        if(isset($filters['adv_id'])) {
            $model = Advertise::model()->findByPk($filters['adv_id']);
            if($model) {
                $conditions[] = "业务:{$model->name}";
            }
        }

        if(isset($filters['channel_id'])) {
            $model = Channel::model()->findByPk($filters['channel_id']);
            if($model) {
                $conditions[] = "渠道:{$model->name}";
            }
        }
        /*2014-11-17包名*/
        if(isset($filters['adv_channel_id'])) {
            $model = AdvertiseChannel::model()->findByPk($filters['adv_channel_id']);
            if($model) {
                $conditions[] = "包名:{$model->tag}";
            }
        }
        /*end*/
        if(isset($filters['begin_date']) && isset($filters['end_date'])) {
                $conditions[] = "从{$filters['begin_date']}到{$filters['end_date']}";
        }

        if(isset($filters['date'])) {
            $conditions[]= "日期:{$filters['date']}";
        }

        if(count($conditions)){
            return $this->getTableBaseName().'-('.join('-',$conditions).')';
        } else {
            return $this->getTableBaseName().'-全部';
        }
    }

    private function convertTextTo2DArray($data) {
        //去掉非法字符
        $data = preg_replace('/[^\x00-\x7F]/', ' ', $data);
        $data = str_replace("\r\n", "\n", $data);
        $data = str_replace(' ', ' ', $data);
        $data = str_replace(',', '', $data); //有些数字用,分隔

        $data = explode("\n", $data);

        $result = array();
        foreach($data as $row) {
            if($row) {
                $row = trim($row, "\"\t\n\r ");
                $splited = preg_split('/\s+/', $row);
                $result[] = $splited;
            }
        }
        return $result;
    }

    public function renderTableField($model, $field) {
        switch($field) {
            case 'date':
                return "<a href='/admin/{$this->getId()}/?f[date]={$model->date}'>{$model->getAttribute($field)}</a>";
            case 'cp.name':
                return "<a href='/admin/{$this->getId()}/?f[cp_id]={$model->cp_id}'>{$model->getAttribute($field)}</a>";
            case 'adv.name':
                return "<a href='/admin/{$this->getId()}/?f[adv_id]={$model->adv_id}'>{$model->getAttribute($field)}</a>";
            case 'channel.name':
                return "<a href='/admin/{$this->getId()}/?f[channel_id]={$model->channel_id}'>{$model->getAttribute($field)}</a>";

        }
        return $model->getAttribute($field);
    }

    protected function getIndexCriteria() {
        $criteria = new CDbCriteria();
        $criteria->order = 'date desc';
        $criteria->compare('t.download_number', '<>0');
        foreach($this->getFilterCondition() as $field => $value) {
            if($field == 'begin_date') {
                $criteria->compare('t.date', '>='.$value);
            } elseif($field == 'end_date') {
                $criteria->compare('t.date', '<='.$value);
            } else {
                $criteria->compare('t.'.$field, $value);
            }
        }
        //$criteria->with = array('advertise', 'channel', 'uploader');
        $criteria->with = array('advertise', 'channel', 'uploader','advertiseChannel');
        return $criteria;
    }
}
