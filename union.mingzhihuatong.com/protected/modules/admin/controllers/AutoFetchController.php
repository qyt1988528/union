<?php

/**
 * Class AutoFetchController
 * chrome自动抓取插件服务端
 */
class AutoFetchController extends Controller {
    public function actionGetLoginInfo() {
        $config = require(dirname(__FILE__).'/loginInfoConfig.php');
        if(Yii::app()->user->isGuest) {
            echo json_encode(array(
                'code' => 403,
                'message' => '您还未登录，请先登录union.zhantai.com'
            )); 
        } else {
            $host = $_SERVER['HTTP_HOST'];
            $host = 'union.zhantai.com';
            $data = array();
            foreach($config as $adv_id => $c) {
                $item = array();
                $item['submit_url'] = "http://{$host}/admin/advertise/upload/?f[adv_id]={$adv_id}";
                $item['data'] = $c;
                $data[] = $item;
            }
            $data = array($data[2]);
            echo json_encode(array(
                'code' => 200,
                'message' => '登录成功，当前登录用户'. Yii::app()->user->name,
                'data' => $data
            ));  
        }   
    }
}
