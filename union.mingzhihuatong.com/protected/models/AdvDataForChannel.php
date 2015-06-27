<?php

class AdvDataForChannel extends AdvData {

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
        //return 'adv_data_for_channel';
        return 'adv_data';
	}

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('adv_id, channel_id, adv_channel_id, download_number, install_number, active_number, new_user, status', 'numerical', 'integerOnly'=>true),
            array('left_user_2days, left_user_7days, left_user_14days, convert_ratio', 'numerical'),
            array('ctime', 'default', 'value' => new CDbExpression('now()')),
            array('tag,date, ctime, mtime', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, adv_id, channel_id, adv_channel_id, date, download_number, install_number, active_number, new_user, left_user_2days, left_user_7days, left_user_14days, convert_ratio, status, ctime, mtime', 'safe', 'on'=>'search'),
        );
    }

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
            'mtime' => 'Mtime',
            'new_user' => '新增用户',
            'left_user_2days' => '次日留存(%)',
            'left_user_7days' => '7日留存(%)',
            'left_user_14days' => '14日留存(%)',
            'convert_ratio' => '转化率(%)',
            'uploader.name' => '上传人',
            'price' => '单价',//原为单价
            'total_price' => '总价',//原为总价
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
        }
        return parent::getAttribute($field);
    }

    /*
     *获得前一天的数据
     * */
    public function getDayToTalData($date) {

        /*把获得时间转换为前一天的时间*/
        $date =strtotime($date);//获得数据创建时间
        $date =strtotime('-1 day',$date);
        $date = date('Y-m-d',$date);
        /*end*/

        $other_data = array();
        $advname = array();//业务名称
        $channelname = array();
        $all_data = array();

        $db = Yii::app()->db;
        /*先根据日期获取数据*/
        $sql = "select adv_id,tag,price,date,sum(total_price) as total_price,sum(download_number) as download_number,channel_id
        from adv_data where date = :date group by concat(adv_id,concat('-',channel_id))";
        $command = $db->createCommand($sql);
        $command->bindParam(':date', $date);
        $other_data = $command->queryAll();
        /*根据adv_data_for_channel.id获取对应的advertise、channel的name*/
        for($i=0;$i<count($other_data);$i++){
            /*获得Adv名称*/
            $sql = "select name as adv_name from advertise where id = :id";
            $command = $db->createCommand($sql);
            $command->bindParam(':id', $other_data[$i]['adv_id']);
            $advname[$i] = $command->queryAll();
            /*获取渠道名称 用于发送给客户时的判断条件*/
            $sql = "select name as channel_name from channel where id = :id ";
            $command = $db->createCommand($sql);
            $command->bindParam(':id', $other_data[$i]['channel_id']);
            $channelname[$i] = $command->queryAll();
            /*end*/
            $all_data[$i]=$other_data[$i]+$advname[$i][0];
        }
        //$end =microtime(true);
        return $all_data;
    }
    /*
     *获得一周内的消息
     * */
    public function getWeekToTalData($date) {

        /*通过获得时间得到上周日的时间*/
        $lastsunday = $this->lastSunday($date);
        $mid = strtotime($lastsunday);
        $mid = strtotime('-6 day',$mid);
        $lastmonday = date('Y-m-d',$mid);
        /*end*/

        $other_data = array();
        $advname = array();
        $channelname = array();
        $all_data = array();

        $db = Yii::app()->db;
        /*先根据日期获取数据*/
        $sql = "select adv_id,channel_id,price,date,tag,sum(download_number) as download_number,sum(total_price) as total_price
        from adv_data where date between :lastmonday and :lastsunday group by concat(adv_id,concat('-',channel_id))";
        $command = $db->createCommand($sql);
        $command->bindParam(':lastmonday', $lastmonday);
        $command->bindParam(':lastsunday', $lastsunday);
        $other_data = $command->queryAll();
        /*根据adv_data_for_channel.id获取对应的advertise、channel的name*/
        for($i=0;$i<count($other_data);$i++){
            /*获得Adv名称*/
            $sql = "select name as adv_name from advertise where id = :id";
            $command = $db->createCommand($sql);
            $command->bindParam(':id', $other_data[$i]['adv_id']);
            $advname[$i] = $command->queryAll();
            /*获取渠道名称 用于发送给客户时的判断条件*/
            $sql = "select name as channel_name from channel where id = :id ";
            $command = $db->createCommand($sql);
            $command->bindParam(':id', $other_data[$i]['channel_id']);
            $channelname[$i] = $command->queryAll();
            /*end*/

            $all_data[$i]=$other_data[$i]+$advname[$i][0]+$channelname[$i][0];
        }
        return $all_data;
    }

    /*
     *获得一个月的消息
     * */
    public function getMonthToTalData($date) {

        /*把获得时间转换为上个月最后一天的时间*/
        $lastmonthend = $this->lastMonthEnd($date);
        $mid = strtotime($lastmonthend);
        $lastmonthstart = date('Y-m-01',$mid);
        /*end*/

        $other_data = array();
        $advname = array();
        $channelname = array();
        $all_data = array();

        $db = Yii::app()->db;
        /*先根据日期获取数据*/
        $sql = "select adv_id,channel_id,date,tag,sum(total_price) as total_price,sum(download_number) as download_number
        from adv_data where date between :lastmonthstart and :lastmonthend group by concat(adv_id,concat('-',channel_id))";
        $command = $db->createCommand($sql);
        $command->bindParam(':lastmonthstart', $lastmonthstart);
        $command->bindParam(':lastmonthend', $lastmonthend);
        $other_data = $command->queryAll();
        /*根据adv_data_for_channel.id获取对应的advertise、channel的name*/
        for($i=0;$i<count($other_data);$i++){
            /*获得Adv名称*/
            $sql = "select name as adv_name from advertise where id = :id";
            $command = $db->createCommand($sql);
            $command->bindParam(':id', $other_data[$i]['adv_id']);
            $advname[$i] = $command->queryAll();
            /*获取渠道名称 用于发送给客户时的判断条件*/
            $sql = "select name as channel_name from channel where id = :id ";
            $command = $db->createCommand($sql);
            $command->bindParam(':id', $other_data[$i]['channel_id']);
            $channelname[$i] = $command->queryAll();
            /*end*/
            $all_data[$i]=$other_data[$i]+$advname[$i][0]+$channelname[$i][0];
        }
        return $all_data;
    }
    /*
     * 11-12
     * 对应日期减一天
     * */
    public function dateDecrease($date){
        $time = strtotime($date);
        $time = strtotime('-1 day',$time);
        $time = date('Y-m-d',$time);
        return $time;
    }
    /*
     * 11-12
     * 获得对应日期是星期几
     * */
    public function weekNum($date){
        $time = strtotime($date);
        $time = date('w',$time);
        return $time;
    }
    /*
     * 11-12
     * 获得上周日的日期
     * */
    public function lastSunday($date){
        while($this->weekNum($date)!=1){
            $date = $this->dateDecrease($date);
        }
        $time = strtotime($date);
        $time = strtotime('-1 day',$time);
        $time = date('Y-m-d',$time);
        return $time;
    }
    /*
     * 11-12
     * 对应日期是几号
     * */
    public function monthNum($date){
        $time = strtotime($date);
        $time = date('d',$time);
        return $time;
    }
    /*
     * 11-12
     * 获得上个月底的日期
     * */
    public function lastMonthEnd($date){
        while($this->monthNum($date)!=1){
            $date = $this->dateDecrease($date);
        }
        $time = strtotime($date);
        $time = strtotime('-1 day',$time);
        $time = date('Y-m-d',$time);
        return $time;
    }
}
