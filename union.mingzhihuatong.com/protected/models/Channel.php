<?php

/**
 * This is the model class for table "channel".
 *
 * The followings are the available columns in table 'channel':
 * @property integer $id
 * @property string $name
 * @property integer $status
 * @property string $mtime
 * @property string $ctime
 */
class Channel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Channel the static model class
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
		return 'channel';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>128),
			array('mtime, ctime,contactor,contactor_phone,contactor_email,account_name,account_number,account_bank', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, status, mtime, ctime', 'safe', 'on'=>'search'),
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
            'adv_number' => array(self::STAT, 'AdvertiseChannel', 'channel_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => '渠道名称',//11-11
            'adv_number' => '业务数量',
			'status' => '状态',
			'mtime' => '修改时间',
			'ctime' => '添加时间',
            'contactor' => '负责人',
            'contactor_phone' => '渠道电话',//11-11
            'contactor_email' => '渠道邮箱',//11-11
            'account_name' => '收款人',
            'account_number' => '收款账号',
            'account_bank' => '开户行支行'
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
		$criteria->compare('status',$this->status);
		$criteria->compare('mtime',$this->mtime,true);
		$criteria->compare('ctime',$this->ctime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function beforeSave() {
        if($this->getIsNewRecord()) {
            $this->ctime = new CDbExpression('now()');
        } else {
            $this->mtime = new CDbExpression('now()');
        }
        return parent::beforeSave();
    }

    public function getAttribute($field) {
        if($field == 'adv_number') {
            return $this->adv_number;
        } else {
            return parent::getAttribute($field);
        }
    }
    /*
     * 11-10
     * */
    public function getAllData(){
        $criteria = new CDbCriteria();
        $criteria->order = "ctime desc";
        return $this->findAll($criteria);
    }

    public function getOneList($id){
        $oneList = $this->findByPk($id);
        return $oneList;
    }


    public function sendMail($email, $subject, $content){
        $client = new GearmanClient(); 
        $client->addServer();  
        $client->setTimeout(2000);        
        if($client->timeout() > 2000){                                          
            return false;                                                       

        }                                                                       
        $result = $client->doBackground("sendMail",json_encode(array(
            'content'=>$content,
            'email'=>$email,
            'subject'=>$subject
        )));                

    }

    public function sendMessage($phones, $content){
        $client = new GearmanClient(); 
        $client->addServer("219.232.243.98"); 
        $client->setTimeout(2000); 
        if($client->timeout() > 2000){                                          
            return false;                                                       

        }                                                                       
        $result = $client->doBackground("sendMessage",json_encode(array(
            'content'=>$content,
            'phones'=>$phones
        )));                

    }
    

    public function getSearchData($name){
        $result = $this->find("name = :name",array(":name"=>$name));
        return $result;
    }

    public function deleteOneRow($id){
        $this->deleteByPk($id);
    }

}
