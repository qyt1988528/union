<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property integer $status
 * @property integer $channel_id
 * @property integer $role
 * @property string $ctime
 * @property string $mtime
 */
class User extends CActiveRecord
{
    const ROLE_CHANNEL = 0;
    const ROLE_ADMIN = 1;

    const STATUS_INIT = 0;
    const STATUS_ACTIVE = 1;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
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
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, channel_id, role', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>128),
            array('email', 'required'),
			array('password', 'length', 'max'=>255),
			array('phone', 'length', 'max'=>32),
			array('ctime, mtime, name', 'safe'),
            array('ctime', 'default', 'value' => new CDbExpression('now()')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, email, password, phone, status, channel_id, role, ctime, mtime', 'safe', 'on'=>'search'),
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
            'channel' => array(self::BELONGS_TO, 'Channel', 'channel_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
            'name' => '姓名',
			'email' => '登录邮箱',
			'password' => 'Password',
			'phone' => '电话',
			'status' => '状态',
			'channel_id' => '渠道',
			'role' => '角色',
			'ctime' => 'Ctime',
			'mtime' => 'Mtime',
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
		$criteria->compare('email',$this->email,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('channel_id',$this->channel_id);
		$criteria->compare('role',$this->role);
		$criteria->compare('ctime',$this->ctime,true);
		$criteria->compare('mtime',$this->mtime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    public function insertOneUser($email, $password, $phone) {
        $this->email = $email; 
        $this->password = crypt($password); 
        $this->phone = $phone;
        $this->ctime = $this->mtime = date("Y-m-d H:i:s");
        return $this->save();
    }

    public function insertOneData($name, $email, $password, $phone, $channel_id) {
        $this->email = $email;
        $this->name = $name;
        $this->password = $password;
        $this->phone = $phone;
        $this->ctime = $this->mtime = date("Y-m-d H:i:s");
        $token = $this->makeToken();
        $this->token = $token;
        $this->channel_id = $channel_id;
        $this->status = 1;
        $isSuccess = $this->save();

        //发送邮件
        $this->sendPasswordEmailGearman($this->id, $this->token, $email);

        if(!$isSuccess) {
            Yii::log(json_encode(array(
                'name' => $name,
                'email'=>$email,
                'phone'=>$phone,
                'isSuccess' => $isSuccess
            )), 'info');
        }

    }

    public function sendPasswordEmailGearman($id, $token, $email){
        $client = new GearmanClient(); 
        $client->addServer();  
        $client->setTimeout(2000);        
        if($client->timeout() > 2000){                                          
            return false;                                                       

        }                                                                       
        $result = $client->doBackground("sendPasswordEmail",json_encode(array(
            'id'=>$id,
            'email'=>$email,
            'token'=>$token
        )));                

    }


    public function activateUser($id, $token) {
        return $this->updateAll(array('status'=>1), 'id=:id and token=:token', array(':id'=>$id, ':token'=>$token));
    }

    public function updateToken($token) {
        $this->token = $token;
        return $this->save();
    }

    public function makeToken() {
        $token = mt_rand(1000, 9999);
        return md5($token);
    }

    public function beforeSave() {
        if($this->getIsNewRecord()) {
            $this->token = $this->makeToken();
            $this->password = crypt($this->password);
        }
        return parent::beforeSave();
    }

    public function afterSave() {
        Yii::log(json_encode(array(
            'isnew' => $this->getIsNewRecord()
        )), 'info');

        if($this->getIsNewRecord() && $this->isAdmin()) {
            $this->_sendAdminActivateEmail();
        }

        return parent::afterSave();
    }

    /**
     * @param $token
     * @param $password
     * @return bool
     * 用户激活并重置密码
     */
    public function activate($token, $password) {
        if($this->token == $token) {
            $this->status = User::STATUS_ACTIVE;
            $this->password = crypt($password);
            $this->token = '';
            return $this->save();
        } else {
            return false;
        }
    }

    public function requestResetPassword() {
        $this->token = $this->makeToken();
        if($this->save()) {
            return $this->_sendResetPasswordEmail();
        } else {
            return false;
        }
    }

    public function resetPassword($token, $password) {
        if($this->token !== $token) {
            return false;
        }
        $this->password = crypt($password);
        $this->token = '';
        return $this->save();
    }

    public function isAdmin() {
        return $this->role == self::ROLE_ADMIN;
    }

    public function isChannel() {
        return $this->role == self::ROLE_CHANNEL && $this->channel_id;
    }

    public static function createAdmin($attributes) {
        $user = new User();
        $user->attributes = $attributes;
        $user->role = self::ROLE_ADMIN;
        $user->password = crypt($user->password ? $user->password : mt_rand());
        $user->token = $user->makeToken();
        if($user->save()) {
            $user->_sendAdminActivateEmail();
            return true;
        }
        return $user->getErrors();
    }

    private function _sendAdminActivateEmail() {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'union.mingzhihuatong.com';
        $link = "http://{$host}/admin/login/activate/id/{$this->id}/token/{$this->token}";
        $username = $this->name ? $this->name : '用户';
        $message = <<<EOT
        <p><b>亲爱的{$username}您好！</b></p>
        <p>这是一封铭智华通广告联盟的用户注册激活邮件，<a target="_blank" href="{$link}">点击激活帐号</a></p>
        <p>如果上述文字点击无效，请把下面网页地址复制到浏览器地址栏中打开：</p>
        <p><a target="_blank" href="{$link}">{$link}</a></p>
EOT;
        $subject = '铭智广告联盟用户激活邮件';
        return Yii::app()->mailer->send($this->email, $subject, $message);
    }

    private function _sendResetPasswordEmail() {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'union.mingzhihuatong.com';
        $link = "http://{$host}/admin/login/setPassword/id/{$this->id}/token/{$this->token}";
        $message = <<<EOT
        <p><b>亲爱的用户您好！</b></p>
        <p>这是一封铭智华通广告联盟密码设置邮件，<a target="_blank" href="{$link}">点击设置密码</a></p>
        <p>如果上述文字点击无效，请把下面网页地址复制到浏览器地址栏中打开：</p>
        <p><a target="_blank" href="{$link}">{$link}</a></p>
EOT;
        $subject = '铭智广告联盟密码设置邮件';
        return Yii::app()->mailer->send($this->email, $subject, $message);
    }
    public function getAdminUsers() {
        return $this->findAll("role=:role", array(":role"=>self::ROLE_ADMIN));
    }

    public function getChannelUsers(){
        return $this->findAll("role=:role", array(":role"=>self::ROLE_CHANNEL));
    }



}
