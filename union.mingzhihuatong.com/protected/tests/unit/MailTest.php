<?php
/**
 * Created by PhpStorm.
 * User: mortyu
 * Date: 14-8-29
 * Time: 下午4:14
 */

class MailTest extends CTestCase {
    public function testSend() {
        $this->assertNotNull(Yii::app()->mailer);
        $mail = Yii::app()->mailer;
        $mail->From = 'union@union.mingzhihuatong.com';
        $mail->FromName = 'yuziyu';
        $mail->addAddress('yuziyu@zhantai.com');
        $mail->isHTML(true);
        $mail->Subject = '铭智联盟通知邮件';
        $mail->Body    = '报警邮件This is the HTML message body <b>in bold!</b>';
        var_dump(!$mail->send());
    }
}
