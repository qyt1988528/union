<?php

class MergeChannelCommand extends CConsoleCommand {
   //合并相同渠道
    public function actionMerge(){
        $ids = array(
          '100040',
          '100039',
          '100022',
          '100046'
        );
        
        $adv_data = AdvData::model();
        $advertice_channel = AdvertiseChannel::model();
        $channel = Channel::model();
        
        foreach($ids as $channel_id){
            if($channel_id != 100046){
                $adv_data->updateRelatedChannelId(array('channel_id'=>100043), $channel_id);
                $advertice_channel->updateRelatedChannelId(array('channel_id'=>100043),$channel_id);
            }else{
                $adv_data->updateRelatedChannelId(array('channel_id'=>100123),$channel_id);
                $advertice_channel->updateRelatedChannelId(array('channel_id'=>100123),$channel_id);

            }
            $channel->deleteOneRow($channel_id);
            
        }

    }

   //给渠道添加账户，并发送重置密码邮件
    public function actionAddChannelUser(){
        $user = User::model();
        $channelList = $user->getChannelUsers();
        foreach($channelList as $val){
            $user = new User();
            $user->insertOneData($val['name'], $val['email'], 123456, $val['phone'], $val['channel_id']);
            sleep(2);
        }
    }
     

    public function actionUpdate(){                                                                      
	    $model = AdvData::model();                                                                       
	    $result = $model->findAll();                                                                     
	    foreach($result as $v){                                                                          
		    $download_number = $v['download_number'];                       
		    $price = $v['price'];                                                                    
		    $income_price = $v['income_price'];                                                      
		    if($price>0){                                                                              
			    $profit_margin = sprintf("%01.2f", (floatval(($income_price - $price) / $price) * 100));
		    }else{                                                                                   
			    $profit_margin = "0.00";                                                             
		    }                                                                                        


		    $data = array(                                                                           
				    'profit_margin'=>$profit_margin,                                                     
				    'profit'=>sprintf("%01.2f",floatval($income_price-$price) * intval($download_number)),
				    'running_account'=>sprintf("%01.2f",floatval($income_price) * intval($download_number)),
				    'total_price'=>sprintf("%01.2f",floatval($price) * intval($download_number)) 
				 );                                                                                       
		    $model->updateByPk($v['id'], $data);                            
	    }                                                                       
    }
}
