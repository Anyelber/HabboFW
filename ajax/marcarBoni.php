<?php
include "../configs/configs.php";
include "../configs/functions.php";

$id = clear($id);
$token = clear($token);

if($token!="adg090adhjaln1madogi1#a0d98adh!!"){
    die();
}

if(is_admin_or_more(rol($_SESSION['id']))){
    $q = $pdo->prepare("SELECT placa_boni FROM users WHERE id = :id");
    $q->execute([
        $id
    ]);
    $r = $q->fetch();

    if($r['placa_boni'] == 1){
        $q = $pdo->prepare("UPDATE users SET placa_boni = 0 , accepted_boni = 0 WHERE id = :id");
        $q->execute([
            $id
        ]);
    }else{
        $q = $pdo->prepare("UPDATE users SET placa_boni = 1 , accepted_boni = :idc WHERE id = :id");
        $q->execute([
            $_SESSION['id'],
            $id
        ]);
    }

}