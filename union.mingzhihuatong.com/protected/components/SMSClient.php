<?php

Yii::import('ext.NuSoapClient');

class SMSClient extends CApplicationComponent{

	const SESSID = '761re43nnnre4oc8q58drqgtc6';

    public $serialNumber;
    public $password;

	private $gwUrl = 'http://sdkhttp.eucp.b2m.cn/sdk/SDKService?wsdl';
    private $sessionKey;
    private $client;

    public function init() {
        parent::init();
        $connectTimeOut = 2;
        $readTimeOut = 10;
        $proxyhost = false;
        $proxyport = false;
        $proxyusername = false;
        $proxypassword = false;
        $sessionKey = md5('zhantai'.uniqid());
        $client = new NuSoapClient($gwUrl,$this->serialNumber,$this->password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
		$client->setOutgoingEncoding("UTF-8");
		$statusCode = $client->login(self::SESSID);
        if ($statusCode!=null && $statusCode=="0")
        {
            $this->sessionKey = $client->getSessionKey();
            $this->client = $client;
        } else {
            //登录失败
            $this->client = null;
        }
    }

    public function sendMessage($phone, $message) {
        if(!$this->client) {
            return false;
        }
        if(is_string($phone)) {
            $phone = array($phone);
        }

        
        $this->client->sendSMS($phone, $message);
    }
}
