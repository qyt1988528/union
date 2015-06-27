<?php

class ImportCommand extends CConsoleCommand {

    /**
     *
     * 导入360的渠道-包 分配关系
     */
    public function action360() {
            $adv_name = '';
        while($row = trim(fgets(STDIN))) {
            $data = array_filter(preg_split('/\s+/', $row), 'strlen');
            if(count($data) == 4) {
                $adv_name = $data[0];
                $adv_id = $this->getAdvId($adv_name);
                if(!$adv_id) {
                    echo "adv missing {$adv_name}\n";
                    var_dump($data);
                }
                array_shift($data);
            }
            $tag = $data[0];
            if(!preg_match('/^\d+$/', $tag)) {
                var_dump($tag);
                var_dump($data);
                continue;
            }
            $tag = 'sjzs_'.$tag;
            $price = $data[1];
            $channel_id = $this->getChannelId($data[2]);
            if(!$channel_id) {
                echo "channel id missing {$data[2]}\n";
            }

            $criteria = new CDbCriteria;
            $criteria->compare('tag', $tag);
            $criteria->compare('channel_id', $channel_id);
            if(!$model=AdvertiseChannel::model()->find($criteria)){
                $m = new AdvertiseChannel;
                $m->adv_id = $adv_id;
                $m->channel_id = $channel_id;
                $m->tag = $tag;
                $m->price = $price;
                echo $m->save(),':', $row, "\n";
            }
        }
    }

    public function actionGen() {
        $data = array();
        while(true) {
            $id = trim(fgets(STDIN));
            if(!$id) {
                break;
            }
            $username = trim(fgets(STDIN));
            $password = trim(fgets(STDIN));
            $data[$id][] = array(
                'username' => $username,
                'password' => $password
            );
            fgets(STDIN);
        }
        echo var_export($data);
    }

    private function getChannelId($name) {
        $name = trim($name);
        static $cache = array();
        if(!isset($cache[$name])){
            $channel = Channel::model()->findByAttributes(array(
                'name' => $name
            ));
            if($channel == null) {
                $channel = new Channel();
                $channel->name = $name;
                $channel->save();
            }
            $cache[$name] = $channel->id;
        }
        return $cache[$name];
    }

    private function getAdvId($name) {
        $model = Advertise::model()->findByAttributes(array(
            'name' => $name
        ));
        if($model) {
            return $model->id;
        }
        return 0;
    }
}
