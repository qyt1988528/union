<?php
/**
 * Created by PhpStorm.
 * User: mortyu
 * Date: 14-9-11
 * Time: 上午7:22
 */

class AdvDataTest extends CTestCase {
    public function testMerge() {
        $adv = Advertise::model()->findByPk(23);
        $map = array();
        foreach($adv->advChannels as $advChannel) {
            $map[$advChannel->tag] = $advChannel;
        }
    }
}