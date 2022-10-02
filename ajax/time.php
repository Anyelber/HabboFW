<?php
include "../configs/configs.php";
include "../configs/functions.php";

$query = '%'.clear($query).'%';

if(rol($_SESSION['id'])>=54){
        $q = $pdo->prepare("SELECT id,habbo FROM users WHERE habbo LIKE :query AND id != :id");
    }else{
        $q = $pdo->prepare("SELECT id,habbo FROM users WHERE habbo LIKE :query AND id != :id AND especial = 0");
    }
    $q->execute([
        $query,
        $_SESSION['id']
    ]);

    if($q->rowCount()>0){

        while($r=$q->fetch()){
            if(!is_timing($r['id'])){
                ?>
                    <p style="width: 50%; background: rgba(0,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;cursor:pointer;" onclick="prepararUsuario(<?=$r['id']?>,'<?=$r['habbo']?>');"><?=keko_user($r['id'])?> <?=$r['habbo']?></p>
                <?php
            }else{
                ?>
                    <p style="width: 50%; background: rgba(200,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;cursor:default;" ><?=keko_user($r['id'])?> <?=$r['habbo']?> <i style="color: red">[ Le toman time ]</i></p>
                <?php

            }
        }
    }else{
        ?>
            <p style="width: 50%; background: rgba(0,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;cursor:default;color: red;" onclick="prepararUsuario(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><i class="fas fa-exclamation-triangle"></i> No se encontro ningun keko con este nombre</p>
        <?php
    }
?>