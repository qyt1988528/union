<?php
$len = count($channels);
$i = 0 ;
echo "[";
foreach($channels as $chan) {
    $i++;
    $arr = array (
        'chanid'=>$chan->id,
        'channame'=>$chan->name,
    );
    echo json_encode($arr);
    if($i<$len){
        echo ",";
    }
}
echo "]";
?>