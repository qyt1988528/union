<?php
echo "[";
$length = count($cp)>count($advertises)?count($cp):count($advertises);
$length = count($channels)>$length?count($channels):$length;
$i = 0 ;
foreach($cp as $c) {
    $cpid[$i] = $c->id;
    $cpname[$i] = $c->name;
    $i++;
}
$i = 0 ;
foreach($advertises as $c) {
    $advid[$i] = $c->id;
    $advname[$i] = $c->name;
    $i++;
}
$i = 0 ;
foreach($channels as $c) {
    $chanid[$i] = $c->id;
    $channame[$i] = $c->name;
    $i++;
}

$i = 0;
for($i=0;$i<$length;$i++){

    if(count($cp)<=$i){
        $cpid[$i]='';
        $cpname[$i]='';
    }
    if(count($advertises)<=$i){
        $advid[$i]='';
        $advname[$i]='';
    }
    if(count($channels)<=$i){
        $chanid[$i]='';
        $channame[$i]='';
    }
    $arr = array(
        'cpid' => $cpid[$i],
        'cpname' => $cpname[$i],
        'advid' => $advid[$i],
        'advname' => $advname[$i],
        'chanid' => $chanid[$i],
        'channame' => $channame[$i],
    );
    echo json_encode($arr);
    if($i==($length-1)){
        break;
    }
    echo ",";
}
echo "]";
?>