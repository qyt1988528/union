<?php

/**
 * This is the model class for table "report_schema_fields".
 *
 * The followings are the available columns in table 'report_schema_fields':
 * @property integer $schema_id
 * @property integer $position
 * @property string $name
 * @property string $field
 *
 * The followings are the available model relations:
 * @property ReportSchema $schema
 */
class ReportSchemaFields extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportSchemaFields the static model class
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
		return 'report_schema_fields';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('schema_id, position', 'numerical', 'integerOnly'=>true),
			array('name, field', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('schema_id, position, name, field', 'safe', 'on'=>'search'),
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
			'schema' => array(self::BELONGS_TO, 'ReportSchema', 'schema_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'schema_id' => 'Schema',
			'position' => 'Position',
			'name' => 'Name',
			'field' => 'Field',
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

		$criteria->compare('schema_id',$this->schema_id);
		$criteria->compare('position',$this->position);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('field',$this->field,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getName() {
        return AdvData::model()->getAttributeLabel($this->field);
    }
}
