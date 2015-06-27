<?php
/**
 * Created by PhpStorm.
 * User: mortyu
 * Date: 14-9-4
 * Time: 下午10:19
 */

class AdvertiseCommand extends CConsoleCommand {
    public function actionImport() {
        while($row = fgets(STDIN)) {
            $row = trim($row);
            if(!$row) {
                continue;
            }
            $row = preg_replace('/\s+/', "\t", $row);
            list($name, $income_price, $outcome_price) = explode("\t", $row);
            $advertise = new Advertise();
            $advertise->cp_id = 100000;
            $advertise->name = $name;
            $advertise->income_price = $income_price;
            $advertise->outcome_price = $outcome_price;
            echo $name,":",$advertise->save(), "\n";
        }
    }
}