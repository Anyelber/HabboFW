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
        ?>
            <p style="width: 50%; background: rgba(0,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;cursor:pointer;" onclick="prepararUsuario2(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><?=keko_user($r['id'])?> <?=$r['habbo']?></p>
        <?php
    }
}else{  
    ?>
        <p style="width: 50%; background: rgba(0,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;cursor:default;color: red;" onclick="prepararUsuario(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><i class="fas fa-exclamation-triangle"></i> No se encontro ningun keko con este nombre</p>
    <?php
}
?>