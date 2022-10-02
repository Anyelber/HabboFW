<?php
include "../configs/configs.php";
include "../configs/functions.php";

$q = $pdo->prepare("SELECT id,habbo FROM users WHERE rol < 61 ORDER BY id DESC");
$q->execute();

$data = array();
$cont = 0;

while($r=$q->fetch()){
    if(requiere_ascenso($r['id'])){
        $temp = array();

        array_push($temp,$r['id']);
        array_push($temp,'<img src="https://www.habbo.es/habbo-imaging/avatarimage?user='.$r['habbo'].'&direction=2&head_direction=2&gesture=sml&size=l&action=" style="width:50px; height:auto;" id="avatar'.$r['id'].'"/> 
        <br>'.nombre_habbo($r['id']).'<br>
        <b>'.nombre_rol(rol($r['id'])).'</b>');

        array_push($data,$temp);
    }
}

echo json_encode(array("data"=>$data));
?>