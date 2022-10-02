<?php
include "../configs/configs.php";
include "../configs/functions.php";

$data = array();

    $q = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
    $q->execute();
    while($r=$q->fetch()){
        
        $temp = array();

        $qa = $pdo->prepare("SELECT id FROM ascensos WHERE id_dio = :id AND pagado = 0");
        $qa->execute([$r['id']]);

        $ascensos = $qa->rowCount();

       


        if(tiene_save($r['id'])){
            $save = '<span class="badge badge-success">Si</span>';
        }else{
            $save = '<span class="badge badge-danger">No</span>';
        }




        if(rol($r['id']) >=5 && rol($r['id']) <=11){
            $requisito = $req_pago_seg;
        }elseif(rol($r['id']) >= 12 && rol($r['id']) <= 18){
            $requisito = $req_pago_tec;
        }elseif(rol($r['id']) >= 19 && rol($r['id']) <= 25){
            if(es_especial($r['id'])){
                $requisito = $req_pago_especial_log;
            }else{
                $requisito = $req_pago_log;
            }
        }elseif(rol($r['id']) >= 26 && rol($r['id']) <= 32){
            if(es_especial($r['id'])){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_sup;
            }
        }elseif(rol($r['id']) >= 33 && rol($r['id']) <= 39){
            if(es_especial($r['id'])){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_dir;
            }
        }elseif(rol($r['id']) >= 40 && rol($r['id']) <= 46){
            if(es_especial($r['id'])){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_pre;
            }
        }elseif(rol($r['id']) >= 47 && rol($r['id']) <= 53){
            if(es_especial($r['id'])){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_eli;
            }
        }elseif(rol($r['id']) >= 54 && rol($r['id']) <= 60){
            if(es_especial($r['id'])){
                $requisito = $req_pago_especial_other;
            }else{
                $requisito = $req_pago_jtd;
            }
        }elseif(rol($r['id'])<5 || rol($r['id'])>60){
            $requisito = "na";
        }


        if($requisito == "na"){
            $status = '<span class="badge badge-warning">N/A</span>';
        }else{
            if($requisito[0] == "time"){
                if(horas_trabajadas($r['id']) >= $requisito[1]){
                    if(horas_trabajadas($r['id']) >= ($requisito[2] + $requisito[1])){
                        $status = '<span class="badge badge-success">Pago + Boni</span>';
                    }else{
                        $status = '<span class="badge badge-primary">Pago</span>';
                    }
                }else{
                    $status = '<span class="badge badge-danger">No Pago</span>';
                }

            }elseif($requisito[0] == "asc"){
                if(cant_ascensos($r['id']) >= $requisito[1]){
                    if(cant_ascensos($r['id']) >= ($requisito[2] + $requisito[1])){
                        $status = '<span class="badge badge-success">Pago + Boni</span>';
                    }else{
                        $status = '<span class="badge badge-primary">Pago</span>';
                    }
                }else{
                    $status = '<span class="badge badge-danger">No Pago</span>';
                }
            }elseif($requisito[0] == "asct"){
                if(cant_ascensos($r['id']) >= $requisito[1]){
                    if(cant_times_validos_user($r['id']) >= $requisito[2]){
                        $status = '<span class="badge badge-success">Pago + Boni</span>';
                    }else{
                        $status = '<span class="badge badge-primary">Pago</span>';
                    }
                }else{
                    $status = '<span class="badge badge-danger">No Pago</span>';
                }
            }elseif($requisito[0] == "timea"){
                if(cant_times_validos_user($r['id'])>=$requisito[1]){
                    if(cant_ascensos($r['id'])>=$requisito[2]){
                        $status = '<span class="badge badge-success">Pago + Boni</span>';
                    }else{
                        $status = '<span class="badge badge-primary">Pago</span>';
                    }
                }else{
                    $status = '<span class="badge badge-danger">No Pago</span>';
                }
            }
        }

        
        $times_validos = times_tomados($r['id']);

        $time_asc = proximo_ascenso($r['id']);

        //$actual = new DateTime(date("Y-m-d H:i:s"));
        //$dif = $actual->diff(new DateTime($time_asc));

        $time_final = "Dias: ".$dif->d." Horas: ".$dif->h." Minutos".$dif->i;


        if(requiere_ascenso($r['id'])){

            $asc = "<span class='badge badge-success'>Si</span>";

        }else{

            $asc = "<span data-toggle='tooltip' title='".$time_asc."' class='badge badge-danger'>No</span>";

        }

        if($r['placa_paga'] == 1){
            $placa_paga = "<br><span id='paga".$r['id']."' onclick='quitar_placa_paga(".$r['id'].")' class='badge badge-danger' style='background: black; color: white; cursor:pointer;' data-toggle='tooltip' title='Quitar Placa Paga'>Placa Paga</span>";
        }else{
            $placa_paga = "<br><span  id='paga".$r['id']."' onclick='dar_placa_paga(".$r['id'].")' class='badge badge-secondary' style='cursor:pointer;' data-toggle='tooltip' title='Dar Placa Paga'>No Placa Paga</span>";
        }

        if($r['placa_boni'] == 1){
            $placa_boni = "<br><span id='boni".$r['id']."' onclick='quitar_placa_boni(".$r['id'].")' class='badge badge-warning' style='cursor:pointer;' data-toggle='tooltip' title='Quitar Placa Boni'>Placa Boni</span>";
        }else{
            $placa_boni = "<br><span id='boni".$r['id']."' onclick='dar_placa_boni(".$r['id'].")' class='badge badge-secondary' style='cursor:pointer;' data-toggle='tooltip' title='Dar Placa Boni'>No Placa Boni</span>";
        }

        $horas_trabajadas = horas_trabajadas_text_return($r['id']);


        array_push($temp, $r['id']);
        array_push($temp, '<img src="https://www.habbo.es/habbo-imaging/avatarimage?user='.$r['habbo'].'&direction=2&head_direction=2&gesture=sml&size=l&action=" style="width:50px; height:auto;" id="avatar'.$r['id'].'"/>');
        array_push($temp, $r['habbo']);
        array_push($temp, nombre_rol($r['rol']));
        array_push($temp, $ascensos);
        array_push($temp, $times_validos);
        array_push($temp, $horas_trabajadas);
        array_push($temp, $asc);
        array_push($temp, $save);
        array_push($temp, $status." ".$placa_paga." ".$placa_boni);
        array_push($temp, ' <a target="_blank" href="?p=ver_detalles_habbo&id='.$r['id'].'" data-toggle="tooltip" title="Ver Detalles"><i class="fas fa-eye"></i></a>
        &nbsp;
    <a href="#" id="pagar'.$r['id'].'" onclick="pagar_creditos('.$r['id'].')" data-toggle="tooltip" title="Pagar"><i class="fa fa-check"></i></a>
        &nbsp;
    <a href="#" id="pagar'.$r['id'].'" onclick="cargar_ascenso('.$r['id'].')" data-toggle="modal" data-target="#pagar_ascenso" title="Pagar con Ascenso"><i class="fa fa-crown"></i></a>
        &nbsp;
    <a href="#" id="pagar'.$r['id'].'" onclick="no_pagar_user('.$r['id'].')" data-toggle="tooltip" title="Rechazar"><i class="fas fa-times-circle"></i></a>');

        array_push($data,$temp);
       
       
    }



echo json_encode(array("data"=>$data));
?>