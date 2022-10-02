<?php
include "../configs/configs.php";
include "../configs/functions.php";

$query = "%".clear($query)."%";

$q = $pdo->prepare("SELECT id, habbo FROM users WHERE habbo LIKE :query");
$q->execute([
    $query
]);

if($q->rowCount()>0){
    while($r = $q->fetch()){
        if(!son_amigos($_SESSION['id'], $r['id'])){
            if(!invitacion_pendiente($_SESSION['id'],$r['id'])){
                ?>
                    <div style="">
                        <div style="width: 100%; background: rgba(0,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;display: flex; justify-content:space-between; align-items:center;"><span><?=keko_user($r['id'])?> <?=$r['habbo']?></span> <a href="?p=amistades&agregar=<?=$r['id']?>" style="height: auto;" class="btn btn-success">Agregar</a></div>
                        
                    </div>
                <?php
            }else{
                ?>
                    <div style="">
                        <div style="width: 100%; background: rgba(0,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;display: flex; justify-content:space-between; align-items:center;"><span><?=keko_user($r['id'])?> <?=$r['habbo']?></span> <span  class="text text-primary">Pendiente</a></div>
                        
                    </div>

                <?php
            }
        }else{
            ?>
            
        <div style="">
            <div style="width: 100%; background: rgba(0,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;display: flex; justify-content:space-between; align-items:center;"><span><?=keko_user($r['id'])?> <?=$r['habbo']?></span></div>
            
        </div>
            <?php

        }
    }
}else{  
    ?>
        <p style="width: 100%; background: rgba(0,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;cursor:default;color: red;" onclick="prepararUsuario(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><i class="fas fa-exclamation-triangle"></i> No se encontro ningun keko con este nombre</p>
    <?php
}
?>