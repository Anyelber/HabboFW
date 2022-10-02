<?php
include "../configs/configs.php";
include "../configs/functions.php";

$id = clear($id);
$token = clear($token);
$q = $pdo->prepare("SELECT codigo FROM users WHERE id = :id AND token_app = :token");
$q->execute([
    $id,
    $token
]);

$horas = horas_trabajadas_return($id);

if($q->rowCount()>0){
    $r = $q->fetch();
    echo json_encode(array("result"=>1,"message"=>"Solicitud aceptada","codigo"=>$r['codigo'],"horas"=>$horas));
}else{
    echo json_encode(array("result"=>0,"message"=>"Ha ocurrido un error","data"=>array()));
}
?>