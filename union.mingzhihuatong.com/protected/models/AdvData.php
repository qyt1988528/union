<?php

/**
 * This is the model class for table "adv_data".
 *
 * The followings are the available columns in table 'adv_data':
 * @property integer $id
 * @property integer $adv_id
 * @property integer $channel_id
 * @property integer $adv_channel_id
 * @property string $date
 * @property integer $download_number
 * @property integer $install_number
 * @property integer $active_number
 * @property integer $new_user
 * @property double $left_user_2days
 * @property double $left_user_7days
 * @property double $left_user_14days
 * @property double $convert_ratio
 * @property integer $status
 * @property string $ctime
 * @property string $mtime
 *
 * The followings are the available model relations:
 * @property AdvertiseChannel $advertiseChannel
 */
class AdvData extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return AdvData the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'adv_data';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('adv_id, channel_id, adv_channel_id, download_number, install_number, active_number, new_user, status', 'numerical', 'integerOnly'=>true),
            array('left_user_2days, left_user_7days, left_user_14days, convert_ratio', 'numerical'),
            array('ctime', 'default', 'value' => new CDbExpression('now()')),
            array('tag,date, ctime, mtime', 'safe'),
            array('income_price,profit_margin,running_account,profit', 'safe','on'=>'search'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, adv_id, channel_id, adv_channel_id, date, download_number, install_number, active_number, new_user, left_user_2days, left_user_7days, left_user_14days, convert_ratio, status, ctime, mtime', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'advertise' => array(self::BELONGS_TO, 'Advertise', 'adv_id'),
            'channel' => array(self::BELONGS_TO, 'Channel', 'channel_id'),
            'advertiseChannel' => array(self::BELONGS_TO, 'AdvertiseChannel', 'adv_channel_id'),
            'uploader' => array(self::BELONGS_TO, 'User', 'upload_user_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'date' => '日期',
            'cp.name' => 'CP',
            'adv.name' => '业务',
            'tag' => '包名',
            'adv_id' => 'Adv',
            'channel_id' => '渠道',
            'channel.name' => '渠道',
            'adv_channel_id' => 'Advertise Channel',
            'status' => 'Status',
            'download_number' => '下载量',
            'install_number' => '安装量',
            'active_number' => '激活量',
            'earnings' => '收入',
            'ctime' => '上传时间',
            'mtime' => '修改时间',//原为Mtime
            'new_user' => '新增用户',
            'left_user_2days' => '次日留存(%)',
            'left_user_7days' => '7日留存(%)',
            'left_user_14days' => '14日留存(%)',
            'convert_ratio' => '转化率(%)',
            'uploader.name' => '上传人',
            'price' => '放出价',//原为单价
            'total_price' => '成本',//原为总价
            'income_price' => '接入价',
            'profit_margin' => '利润率',
            'running_account' => '流水',
            'profit' => '利润',
            'star_level'=>'信用等级',
            'comments'=>'备注'
        );
    }

    public function getAttribute($field) {
        switch($field) {
        case 'adv.name':
            return $this->advertise->name;
        case 'channel.name':
            return $this->channel->name;
        case 'cp.name':
            return $this->advertise->cp->name;
        case 'uploader.name':
            return $this->uploader->name;
            /*11-12*/
        case 'income_price':
            return sprintf("%01.2f",$this->income_price);
        case 'price':
            return sprintf("%01.2f",$this->price);
        case 'profit_margin':
            return sprintf("%01.2f",$this->profit_margin).'%';
        case 'running_account':
            return sprintf("%01.2f",$this->running_account);
        case 'total_price':
            return sprintf("%01.2f",$this->total_price);
        case 'profit':
            return sprintf("%01.2f",$this->profit);
            /*end*/
        }
        return parent::getAttribute($field);
    }

    public function isDataExists() {
        return $this->countByAttributes(array(
            'date' => $this->date,
            'adv_channel_id' => $this->adv_channel_id
        )) != 0;
    }

    public function sum($fields, $condition='',$params=array()) {
        $builder=$this->getCommandBuilder();
        $criteria=$builder->createCriteria($condition,$params);
        $table = $this->getTableSchema();
        $alias = 't';
        $condition = $criteria->condition;
        if($criteria->condition) {
            $condition = "WHERE {$condition}";
        }
        if(is_string($fields)) {
            $sum_fields = "SUM(`{$fields}`)";
        } else {
            $sum_fields = array_map(function($field) {
                return "SUM(`{$field}`)";
            }, $fields);
            $sum_fields = join(',', $sum_fields);
        }

        $sql = "SELECT {$sum_fields} FROM {$table->rawName} $alias {$condition}";
        $this->applyScopes($criteria);
        $command=$this->getDbConnection()->createCommand($sql);
        $builder->bindValues($command,array_merge($params,$criteria->params));
        if(is_string($fields)) {
            return $command->queryScalar();
        } else {
            return $command->queryRow(false);
        }
    }

    public function beforeSave(){
        $this->total_price = intval($this->download_number) * floatval($this->price);
        return parent::beforeSave();
    }
    public function getDailyToTalData($date) {
    /*
    $db = Yii::app()->db;
    $sql = "select advertise.name as adv_name,sum(total_price) as prices,sum(download_number) as downloads from adv_data join advertise on advertise.id = adv_id where date = :date group by adv_id";
    $command = $db->createCommand($sql);
    $command->bindParam(':date', $date);
    return $command->queryAll();
     */
        $criteria = new CDbCriteria;
        $criteria->compare('ctime', ">= $date");
        $criteria->order = 'date desc';
        $data = array(
            );

        $advId2NameMap = array();
        foreach(Advertise::model()->findAll() as $adv) {
            $advId2NameMap[$adv->id] = $adv->name;
        }

        foreach($this->findAll($criteria) as $model) {
            if(!isset($data[$model->date])) {
                $data[$model->date] = array();
            }
            if(!isset($data[$model->date][$model->adv_id])) {
                $data[$model->date][$model->adv_id] = array(
                    'adv_name' => $advId2NameMap[$model->adv_id],
                    'download_number' => 0,
                    'total_price' => 0,
                );
            }
            $data[$model->date][$model->adv_id]['download_number'] += sprintf("%01.2f",floatval($model->download_number));
            $data[$model->date][$model->adv_id]['total_price'] += sprintf("%01.2f",floatval($model->total_price));
        }
        return $data;
    }
    /*
     *根据日期、adv_advchannel_id更新数据
     * $date 日期
     * $tag
     * $data需更改的数据
     * */
    public function updateByDateandAdvChannelId($date, $tag, $data){
        $result = $this->updateAll($data, 'date = :date and tag = :tag', array(":date"=>$date,":tag"=>$tag));
        if($result){
            return true;
        }
    }

    //对内
    public function getDayDataForIn($startTime, $endTime, $flag){
        $result = array();
        $criteria = new CDbCriteria();

        if($startTime){
            $criteria->addBetweenCondition('ctime', $startTime." 00:00:00", $endTime.' 23:59:59');
        }
        $criteria->order = "date desc";

        $result = $this->findAll($criteria);
        $result = $this->getAdvConnectData($result);
        if(!$flag) {
            return $result;
        }else{
            $active_number_sum = 0;
            $running_account_sum = 0;
            $profit_margin_sum = 0;
            $profit_sum = 0;
            $total_price = 0;
            $income_price = 0;
                if($result) {
                    foreach($result as $val){
                        $active_number_sum += $val['download_number'];;
                        $running_account_sum += $val['running_account'];
                        $profit_sum += $val['profit'];
                        $total_price += $val['price'];
                        $income_price += $val['income_price'];

                    }
                }

            if($total_price){
                $profit_margin_sum = sprintf("%01.2f", (floatval(($income_price - $total_price) / $total_price) *100));
            }
            $result = array(
                'active_sum'=>$active_number_sum,
                'running_sum'=>$running_account_sum."w",
                'profit_sum'=>$profit_sum."w",
                'profit_margin_sum'=>$profit_margin_sum."%"
            );
            return $result;
        }
    }




    public  function getAdvConnectData($other_data){
        $all_data = array();
        foreach($other_data as $k=>$v){
            $db = yii::app()->db;
            $rs=$db->createcommand();
            $rs->select('name as adv_name');
            $rs->from('advertise');
            $rs->where('id = :id', array(':id'=>$v['adv_id']));
            $advname = $rs->query()->read();

            //获取邮箱  name
            $db = yii::app()->db;
            $rs=$db->createcommand();
            $rs->select('contactor_email as email,name as channel_name');
            $rs->from('channel');
            $rs->where('id = :id', array(':id'=>$v['channel_id']));
            $channel = $rs->query()->read();

            $db = yii::app()->db;
            $rs=$db->createcommand();
            $rs->select('name as cp_name');
            $rs->from('cp');
            $rs->where('id = :id', array(':id'=>$v['cp_id']));
            $cp = $rs->query()->read();

            $db = yii::app()->db;
            $rs=$db->createcommand();
            $rs->select('name as user_name');
            $rs->from('user');
            $rs->where('id = :id', array(':id'=>$v['upload_user_id']));
            $user = $rs->query()->read();
            /*end*/
            $all_data[$k]['date'] = $v['date'];
            $all_data[$k]['tag'] = $v['tag'];
            $all_data[$k]['download_number'] = $v['download_number'];
            $all_data[$k]['price'] = $v['price'];
            $all_data[$k]['channel_id'] = $v['channel_id'];
            $all_data[$k]['total_price'] = sprintf("%01.2f",$v['total_price']);
            $all_data[$k]['running_account'] = sprintf("%01.2f",$v['running_account']);
            $all_data[$k]['profit'] =sprintf("%01.2f",$v['profit']);
            $all_data[$k]['profit_margin'] = sprintf("%01.2f",$v['profit_margin'])."%";
            $all_data[$k]['adv_name'] = $advname['adv_name'];
            $all_data[$k]['email'] = $channel['email'];
            $all_data[$k]['channel_name'] = $channel['channel_name'];
            $all_data[$k]['cp_name'] = $cp['cp_name'];
            $all_data[$k]['user_name'] = $user['user_name'];
        }

        return $all_data;
    }

    /*
     *获得前一天的数据  对于客户
     * */
    public function getDayToTalData($date) {
        $all_data = array();

        /*先根据日期获取数据*/
        $criteria = new CDbCriteria();
        $criteria->condition = "ctime between :date1 and :date2";
        $criteria->params = array(":date1"=>$date." 00:00:00",":date2"=>$date." 23:59:59");
        $criteria->order = "date desc";

        $other_data = $this->findAll($criteria);

        $all_data = $this->getAdvConnectData($other_data);

        //获取分组情况
        $counts = count($all_data);
        $result = array();
        $flag = false;

        foreach ($all_data as $key=>$val) {

            foreach ($result as $value) {
                foreach($value as $fields){
                    if (in_array($val['channel_id'], $fields)) {
                        $flag = true;
                    }
                }
            }

            if($flag){
                continue;
            }

            foreach($all_data as $k=>$v){
                if($val['channel_id'] == $v['channel_id']){
                    foreach ($v as $kk=>$vv) {
                        $result[$key][$k][$kk] = $vv;
                    }

                }else{
                    foreach($v as $kk=>$vv){
                        $result[$key+1][$k][$kk] = $vv;
                    }
                }
            }

        }


        return $result;
    }




    public function updateRelatedChannelId($data, $channel_id){
        $this->updateAll($data, "channel_id = :channel_id",array(":channel_id"=>$channel_id));
    }


    public function getAdvCtime($date, $tag){
        return $this->find("date = :date and tag = :tag",array(":date"=>$date,":tag"=>$tag));
    }

}
