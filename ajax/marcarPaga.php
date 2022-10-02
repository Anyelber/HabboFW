<?php
include "../configs/configs.php";
include "../configs/functions.php";

$id = clear($id);
$token = clear($token);


if(is_admin_or_more(rol($_SESSION['id']))){
    $q = $pdo->prepare("SELECT placa_paga FROM users WHERE id = :id");
    $q->execute([
        $id
    ]);
    $r = $q->fetch();

    if($r['placa_paga'] == 1){
        $q = $pdo->prepare("UPDATE users SET placa_paga = 0, accepted_paga = 0 WHERE id = :id");
        
        $q->execute([
            $id
        ]);
    }else{
        $q = $pdo->prepare("UPDATE users SET placa_paga = 1, accepted_paga = :idc WHERE id = :id");
        
        $q->execute([
            $_SESSION['id'],
            $id
        ]);
    }

}