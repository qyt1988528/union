<?php
$len = count($advertises);
$i = 0 ;
echo "[";
foreach($advertises as $adv) {
    $i++;
    $arr = array (
        'advid'=>$adv->id,
        'advname'=>$adv->name,
    );
    echo json_encode($arr);
    if($i<$len){
        echo ",";
    }
}
echo "]";
?>