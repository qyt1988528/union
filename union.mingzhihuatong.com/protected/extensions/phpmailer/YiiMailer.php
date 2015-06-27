<?php

require_once dirname(__FILE__).'/PHPMailerAutoload.php';

class YiiMailer extends CComponent {

    public function init() {
    }

    public function send($addresses, $subject, $body) {
        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        $mail->From = 'notify@mail.mingzhihuatong.com';
        $mail->FromName = '铭智广告联盟通知邮件';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);
        if(is_string($addresses)) {
            $mail->addAddress($addresses);
        } else {
            foreach($addresses as $addr) {
                $mail->addAddress($addr);
            }
        }
        $mail->Body = $body;
        $mail->Subject = $subject;
        Yii::log(json_encode(array('mail'=>$mail->send())), 'info');
    }


    public function sendAttachment($addresses, $subject, $body, $attachment){
        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        $mail->From = 'notify@mail.mingzhihuatong.com';
        $mail->FromName = '铭智广告联盟通知邮件';
        $mail->Encoding = 'base64';
        $mail->isHTML(true);
        if(is_string($addresses)) {
            $mail->addAddress($addresses);
        } else {
            foreach($addresses as $addr) {
                $mail->addAddress($addr);
            }
        }
        $mail->Body = $body;
        $mail->Subject = $subject;
        $mail->addAttachment($attachment);
        Yii::log(json_encode(array('mail'=>$mail->send())), 'info');
    }

    public function __call($method,$args){
        return call_user_func_array(array($this->mail,$method),$args);
    }

}

