<?php
include "../configs/configs.php";
include "../configs/functions.php";

$id = clear($id);
$token = clear($token);
$q = $pdo->prepare("SELECT * FROM users WHERE id = :id AND token_app = :token");
$q->execute([
    $id,
    $token
]);

if($q->rowCount()>0){
    $r = $q->fetch();
    $qt = $pdo->prepare("SELECT * FROM times WHERE id_recibe = :id AND is_timing = 1");
    $qt->execute([
        $id
    ]);

    $time = "";

    if($qt->rowCount()>0){
        $rt = $qt->fetch();

        $datetimeObj1 = new DateTime(date("Y-m-d H:i:s"));
        $datetimeObj2 = new DateTime($rt['created_at']);
        $interval = $datetimeObj1->diff($datetimeObj2);

        $time = $interval->h." Hora(s) ".$interval->i." Minuto(s)";  //abs(($ta - $t) / (60*60));

        echo json_encode(array("result"=>1,"message"=>"Solicitud aceptada","timing"=>1,"nombre_habbo"=>nombre_habbo($rt['id_dio']),"time"=>$time));
    }else{
        echo json_encode(array("result"=>1,"message"=>"Solicitud aceptada","timing"=>0));
    }
}else{
    echo json_encode(array("result"=>0,"message"=>"Ha ocurrido un error","data"=>array()));
}
?>