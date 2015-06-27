<?php

class SendMessageGearmanCommand  extends CConsoleCommand{

    public function actionIndex(){
        $worker = new GearmanWorker();
        $worker->addServer("219.232.243.98");

        $worker->addFunction("sendMessage", function(GearmanJob $job) {
            $workload = json_decode($job->workload(),true);

            $phones = $workload['phones'];
            $content = $workload['content'];
            Yii::app()->smsClient->sendMessage($phones, $content);
            return true;
        });

        while($worker->work());

    }
}
