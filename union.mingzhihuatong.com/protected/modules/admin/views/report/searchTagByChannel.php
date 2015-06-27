<?php
$len = count($adv_channels);
$i = 0 ;
echo "[";
foreach($adv_channels as $adv_channel) {
    $i++;
    $arr = array (
        'tagid'=>$adv_channel->id,
        'tagname'=>$adv_channel->tag,

    );
    echo json_encode($arr);
    if($i<$len){
        echo ",";
    }
}
echo "]";
?>