<?php

class SendMailGearmanCommand  extends CConsoleCommand{

    public function actionSendEmail(){

        $worker = new GearmanWorker();
        $worker->addServer();

        $worker->addFunction("sendMail", function(GearmanJob $job) {
            $workload = json_decode($job->workload(),true);

            $subject = $workload['subject'];
            $body = $workload['content'];
            $addresses = $workload['email'];
            Yii::app()->mailer->send($addresses, $subject, $body);
        });

        while($worker->work());
    }


    public function actionSendPasswordEmail(){

        $worker = new GearmanWorker();
        $worker->addServer();

        $worker->addFunction("sendPasswordEmail", function(GearmanJob $job) {
            $workload = json_decode($job->workload(),true);

            $addresses = $workload['email'];
            $id = $workload['id'];
            $token = $workload['token'];

            Yii::log(json_encode(array(
                'token' => $token
            )), 'info');

            $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'union.zhantai.com';
            $link = "http://{$host}/admin/login/setPassword/id/{$id}/token/{$token}";
            $message = <<<EOT
            <p><b>亲爱的用户您好！</b></p>
            <p>这是一封铭智华通广告联盟密码设置邮件，<a target="_blank" href="{$link}">点击设置密码</a></p>
            <p>如果上述文字点击无效，请把下面网页地址复制到浏览器地址栏中打开：</p>
            <p><a target="_blank" href="{$link}">{$link}</a></p>
EOT;
            $subject = '铭智广告联盟密码设置邮件';
            return Yii::app()->mailer->send($addresses, $subject, $message);
        });

        while($worker->work());
    }

  

}



