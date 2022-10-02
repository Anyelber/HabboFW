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
        if(requiere_ascenso($r['id'])){
            ?>
                <p style="width: 100% display: flex; justify-content: center; align-items:center; width: 100%; background: rgba(0,0,0,0.04); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;cursor:pointer;" onclick="prepararUsuario(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><?=keko_user($r['id'])?> <?=$r['habbo']?></p>
            <?php
        }else{
            $ql = $pdo->prepare("SELECT created_at FROM ascensos WHERE id_recibe = :id ORDER BY id DESC LIMIT 1");
            $ql->execute([
                $r['id']
            ]);

            $rl = $ql->fetch();
            
            if(is_agente($r['id'])){
                $hora_ascenso = $agente_tiempo;  
            }elseif(is_seguridad($r['id'])){
                $hora_ascenso = $seguridad_tiempo;
            }elseif(is_tecnico($r['id'])){
                $hora_ascenso = $tecnico_tiempo;
            }elseif(is_logistica($r['id'])){
                $hora_ascenso = $logistica_tiempo;
            }elseif(is_supervisor($r['id'])){
                $hora_ascenso = $supervisor_tiempo;
            }elseif(is_director($r['id'])){
                $hora_ascenso = $director_tiempo;
            }elseif(is_presidente($r['id'])){
                $hora_ascenso = $presidente_tiempo;
            }elseif(is_elite($r['id'])){
                $hora_ascenso = $elite_tiempo;
            }elseif(is_junta_directiva($r['id'])){
                $hora_ascenso = $junta_directiva_tiempo;
            }elseif(is_administrador($r['id'])){
                $hora_ascenso = $administrador_tiempo;
            }elseif(is_manager($r['id'])){
                $hora_ascenso = $manager_tiempo;
            }elseif(is_founder($r['id'])){
                $hora_ascenso = $founder_tiempo;
            }elseif(is_owner($r['id'])){
                $hora_ascenso = $owner_tiempo;
            }elseif(is_developer($r['id'])){
                $hora_ascenso = $developer_tiempo;
            }

            $date_actual = date("Y-m-d H:i:s");

            $time = new DateTime(date("Y-m-d H:i:s",strtotime($rl['created_at']." +".$hora_ascenso)));
            $dif = $time->diff(new DateTime(date("Y-m-d H:i:s")));

            


            $restante = "Asciende en: ".$dif->d." Dias ".$dif->h." Horas ".$dif->i." Minutos ".$dif->s." Segundos";

            ?>
                <div style="width: 100% display: flex; justify-content: center; align-items:center; width: 100%; background: rgba(200,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;cursor:default;" ><div style="display: inline-block;"><?=keko_user($r['id'])?></div> <div style="display:inline-block;"><?=$r['habbo']?><br><i style="color: red">[No requiere ascenso]</i><br><small><?=$restante?></small></div></div>
            <?php
        }
    }
}else{ 
    ?>
        <p style="width: 100% display: flex; justify-content: center; align-items:center; width: 100%; background: rgba(0,0,0,0.1); padding: 10px;border-top: 1px solid #333;margin-bottom: 0;cursor:default;color: red;" onclick="prepararUsuario(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><i class="fas fa-exclamation-triangle"></i> No se encontro ningun keko con este nombre</p>
    <?php
}
?>