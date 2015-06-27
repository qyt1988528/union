<?php
$len = count($cp);
$i = 0 ;
echo "[";
foreach($cp as $c) {
    $i++;
    $arr = array (
        'cpid'=>$c->id,
        'cpname'=>$c->name,
    );
    echo json_encode($arr);
    if($i<$len){
        echo ",";
    }
}
echo "]";
?>