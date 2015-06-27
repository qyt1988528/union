<?php

/**
 * This is the model class for table "cp".
 *
 * The followings are the available columns in table 'cp':
 * @property integer $id
 * @property string $name
 * @property string $contact_name
 * @property string $contact
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property Advertise[] $advertises
 */
class CP extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CP the static model class
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
		return 'cp';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status', 'numerical', 'integerOnly'=>true),
			array('name, contact_name', 'length', 'max'=>128),
            array('name, fullname', 'required'),
            array('contact_phone', 'numerical'),
            array('contact_email', 'safe', 'on' => 'update'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, contact_name, contact_phone, contact_email, status', 'safe', 'on'=>'search'),
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
			'advertises' => array(self::HAS_MANY, 'Advertise', 'cp_id'),
            'adv_number' => array(self::STAT, 'Advertise', 'cp_id')
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
            'fullname' => '公司全称',
			'contact_name' => '接口人',
			'contact_phone' => '接口人电话',
            'contact_email' => '接口人邮箱',
			'status' => '状态',
            'adv_number' => '业务数量'
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
		$criteria->compare('contact_name',$this->contact_name,true);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getAttribute($field) {
        if($field == 'adv_number') {
            return $this->adv_number;
        }
        return parent::getAttribute($field);
    }
}
