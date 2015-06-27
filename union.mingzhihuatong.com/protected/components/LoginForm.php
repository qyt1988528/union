<?php
/**
 * Created by PhpStorm.
 * User: mortyu
 * Date: 14-9-5
 * Time: 上午10:55
 */

class LoginForm extends CFormModel {

    public $email;
    public $password;
    public $verify;
    public $rememberMe=false;

    private $_identity;


    public function rules() {
        return array(
            array(
                'email, password', 'required',
            )
        );
    }

    public function authenticateAdmin()
    {
        $result = true;
        $redis = Yii::app()->redis;
        $value = $redis->get($this->email);
        if($value >=3){
            if(!$this->isVerify($this->verify)){
                $result = false;
                $this->addError('showVerify',"true");
                $this->addError('verify',"验证码错误");
            }     
        }
        
        if($result){
            $this->_identity=new AdminUserIdentity($this->email,$this->password);
            if($this->_identity->authenticate()) {
                Yii::app()->user->login($this->_identity);
                return true;
            } else {
                $this->addError('password','用户名或密码错误');
                $counts = $redis->incr($this->email);
                if($counts == 1){
                    $redis->expire($this->email,3600);
                }elseif($counts >= 3){
                    $this->addError('showVerify',"true");
                }

                return false;
            }
        }
    }
    
    public function isVerify($verify){
        session_start();
        $trueCode = $_SESSION['trueCode'];
        if(strtolower($trueCode) == strtolower($verify)){
            unset($_SESSION['trueCode']);
            return true;
        }else{
            return false;
        }
    }
    

    public function authenticate() {
        $this->_identity=new UserIdentity($this->email,$this->password);
        if($this->_identity->authenticate()) {
            Yii::app()->user->login($this->_identity);
            return true;
        } else {
            $this->addError('password','用户名或密码错误');
            return false;
        }
    }
}
