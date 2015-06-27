<?php

/**
 * This is the model class for table "advertise".
 *
 * The followings are the available columns in table 'advertise':
 * @property integer $id
 * @property string $name
 * @property integer $cp_id
 * @property integer $status
 * @property string $mtime
 * @property string $ctime
 *
 * The followings are the available model relations:
 * @property Cp $cp
 */
class Advertise extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Advertise the static model class
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
		return 'advertise';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cp_id', 'numerical', 'integerOnly'=>true, 'min' => 1),
			array('name', 'length', 'max'=>128),
            array('income_price, outcome_price', 'numerical'),
            array('name, cp_id', 'required'),
			array('mtime, ctime, cp_admin_url, description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, cp_id, status, mtime, ctime', 'safe', 'on'=>'search'),
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
            'schemas' => array(
                self::HAS_MANY, 'ReportSchema', 'adv_id'
            ),
            'advChannels' => array(
                self::HAS_MANY, 'AdvertiseChannel', 'adv_id'
            )
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'name' => '名称',
			'cp_id' => 'Cp',
			'status' => 'Status',
            'income_price' => '接入价',
            'outcome_price' => '放出价',
            'description' => '要求',
            'cp_admin_url' => '后台',
			'mtime' => '修改时间',
			'ctime' => '创建时间',
            'cp.name' => 'CP',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('cp_id',$this->cp_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('mtime',$this->mtime,true);
		$criteria->compare('ctime',$this->ctime,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    /*
     * 11-10
    * */
    public function getAttribute($name) {
        if($name === 'cp.name') {
            return $this->cp->name;
        }else{
            return parent::getAttribute($name);
        }
    }

    public function beforeSave() {
        if($this->getIsNewRecord()) {
            $this->ctime = new CDbExpression('now()');
        } else {
            $this->mtime = new CDbExpression('now()');
        }
        return parent::beforeSave();
    }

    public function getSchemas() {
        return $this->schemas ? $this->schemas : array();
    }

    public function getAdvChannelByTag($tag) {
        static $map = null;
        if($map === null){
            $map = array();
            foreach($this->advChannels as $advChannel) {
                $map[trim($advChannel->tag)] = $advChannel;
            }
        }
        return isset($map[$tag]) ? $map[$tag] : null;
    }

    /**
     * @param $schema
     * @param $advertiseChannel
     * @param $data
     * @param bool $private
     * @return array
     *
     * 单个业务下单个渠道的数据
     */
    public function generatePerAdvChannelData($schema, $advertiseChannel, $data, $private = true) {
        $map = array();
        foreach($schema->getFields() as $index => $field) {
            if($field->field != 'tag') {
                $map[$field->field] = $index;
            }
        }

        $result = array();
        foreach($data as $row) {
            $model = $private ? new AdvData() : new AdvDataForChannel();
            foreach($map as $field => $index) {
                if(isset($row[$index])) {
                    $model->setAttribute($field, $row[$index]);
                }
            }
            $model->adv_id = $this->id;
            $model->channel_id = $advertiseChannel->channel_id;
            $model->adv_channel_id = $advertiseChannel->id;
            $model->tag = $advertiseChannel->tag;
            $model->cp_id = $this->cp_id;
            $model->upload_user_id = Yii::app()->user->id;
            $model->price = $advertiseChannel->price;
            $model->total_price = floatval($model->price) * intval($model->download_number);
            $result[] = $model;
        }
        return $result;
    }

    /**
     * @param $data
     * @param bool $private
     * @return array
     *
     * 单个业务下多个渠道的数据
     */
    public function generateAdvChannelData($data, $private = true) {
        $map = array();
        $fields = array(
         'date',
         'tag',
         'download_number',
         'star_level'
        );
        foreach($fields as $index => $field) {
            $map[$field] = $index;
        }
        $result = array();
        foreach($data as $row) {
            $model = $private ? new AdvData() : new AdvDataForChannel();
            $model->adv_id = $this->id;
            $model->cp_id = $this->cp_id;
            $model->upload_user_id = Yii::app()->user->id;
            foreach($map as $field => $index) {
                if(!isset($row[$index])) {
                    continue;
                }
                $value = trim($row[$index]);
                if($field == 'tag') {
                    $advChannel = $this->getAdvChannelByTag($value);
                    if($advChannel) {
                        $model->adv_channel_id = $advChannel->id;
                        $model->channel_id = $advChannel->channel_id;
                        $model->price = $advChannel->price;
                    }
                }
                $model->setAttribute($field, $value);
            }
            /*11-10 添加接入价 利润率 流水 利润*/
            $model->income_price = sprintf("%01.2f",$this->income_price);
            if($model->price) {
                $model->profit_margin = sprintf("%01.2f", (floatval(($model->income_price - $model->price) / $model->price) * 100)) ;
            }
            $model->profit = sprintf("%01.2f",floatval($model->income_price-$model->price) * intval($model->download_number));
            $model->running_account= sprintf("%01.2f",floatval($model->income_price) * intval($model->download_number));
            /*end*/
            $model->total_price = sprintf("%01.2f",floatval($model->price) * intval($model->download_number));
            $result[] = $model;
        }
        return $result;
    }

    public function getAdvId($name){
        $result = $this->find("name = :name",array(":name"=>$name));
        if($result){
            $id = $result->id;
        }else{
            $id = 0;
        }

        return $id;

    }


    }
