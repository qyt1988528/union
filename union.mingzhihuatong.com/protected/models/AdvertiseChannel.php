<?php

/**
 * This is the model class for table "advertise_channel".
 *
 * The followings are the available columns in table 'advertise_channel':
 * @property integer $id
 * @property integer $cp_id
 * @property integer $adv_id
 * @property string $adv_name
 * @property integer $channel_id
 * @property string $tag
 * @property string $data
 * @property integer $status
 * @property string $mtime
 * @property string $ctime
 *
 * The followings are the available model relations:
 * @property AdvData[] $advDatas
 */
class AdvertiseChannel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AdvertiseChannel the static model class
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
		return 'advertise_channel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cp_id, adv_id, channel_id, status', 'numerical', 'integerOnly'=>true),
            array('adv_id, tag', 'required'),
			array('adv_name, tag', 'length', 'max'=>128),
			array('download_url, data, mtime, ctime,price', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, cp_id, adv_id, adv_name, channel_id, tag, data, status, mtime, ctime', 'safe', 'on'=>'search'),
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
            'cp' => array(self::BELONGS_TO, 'CP', 'cp_id'),
            'advertise' => array(self::BELONGS_TO, 'Advertise', 'adv_id'),
            'channel' => array(self::BELONGS_TO, 'Channel', 'channel_id', 'joinType' => 'INNER JOIN'),
			'advDatas' => array(self::HAS_MANY, 'AdvData', 'adv_channel_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cp_id' => 'Cp',
			'adv_id' => '业务',
			'adv_name' => '业务名',
			'channel_id' => '渠道',
			'tag' => '包名(用于标记数据)',
			'data' => 'Data',
			'status' => 'Status',
			'mtime' => 'Mtime',
			'ctime' => 'Ctime',
            'adv.name' => '业务名',
            'channel.name' => '渠道名',
            'price' => '单价',
            'download_url' => '下载链接'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('cp_id',$this->cp_id);
		$criteria->compare('adv_id',$this->adv_id);
		$criteria->compare('adv_name',$this->adv_name,true);
		$criteria->compare('channel_id',$this->channel_id);
		$criteria->compare('tag',$this->tag,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('mtime',$this->mtime,true);
		$criteria->compare('ctime',$this->ctime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getAttribute($field) {
        switch($field) {
            case 'cp.name':
                return $this->cp ? $this->cp->name : '';
            case 'adv.name':
                return $this->advertise ? $this->advertise->name : '';
            case 'channel.name':
                return $this->channel ? $this->channel->name : '';
        }
        return parent::getAttribute($field);
    }

    public function updateRelatedChannelId($data,$channel_id){
        $this->updateAll($data,"channel_id = :channel_id",array("channel_id"=>$channel_id));
    }
}
