<?php

class NoticeController extends AdminController{


    public function actionindex(){
        $channel = Channel::model();
        $channelList = $channel->getAllData();
        

        if(Yii::app()->request->getIsPostRequest()){

            $name= $_POST['name'];
            if(!$name){
                $getParams = $this->getFilterCondition();
                $methods = $getParams['methods'];
                $ids = $_POST['id'];
                $content = $_POST['content'];
                $content = $this->setMailContents($content);
                $subject = $_POST['subject'];

                if($ids) {
                    foreach ($ids as $val) {
                        $oneList = $channel->getOneList($val);

                        if ($methods == "email") {
                            //发送邮件
                            if ($oneList) {
                                $emails[] = $oneList['contactor_email'];
                            }
                        } elseif ($methods == "mes") {
                            //发送信息
                            if ($oneList) {
                                $phones[] = $oneList['contactor_phone'];       
                            }

                        }
                    }
                }else{
                    $message = "请选择要发送的对象";
                }
                if($emails){
                    $channel->sendMail($emails, $subject, $content);
                }

                if($phones){
                    $channel->sendMessage($phones, $content);
                }
            }else{
                $channelList = $channel->getSearchData($name); 
                foreach($channelList as $k=>$val){
                    $result[$k] = $val;
                }
                echo json_encode($result);
                
                exit;
            }
        }




        $this->render('index',array(
            'channelList'=>$channelList,
            'message'=>$message
        ));
    }


    public function actionSearch(){
        $channel = Channel::model();
        $channelList = $channel->getAllData();
        foreach($channelList as $val){
            $result[] = $val['name'];
        }

        echo json_encode($result);
       
    }

/*
    public function actionAlldata(){
        $channel = Channel::model();
        $channelList = $channel->getAllData();
        foreach($channelList as $k=>$val){
            $result[$k]['name'] = $val['name'];
            $result[$k]['id'] = $val['id'];
            $result[$k]['contactor_email'] = $val['contactor_email'];
            $result[$k]['contactor_phone'] = $val['contactor_phone'];
        }
        echo json_encode($result);
       
    }
 */


}

