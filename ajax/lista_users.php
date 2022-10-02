<?php
include "../configs/configs.php";
include "../configs/functions.php";

$q = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
$q->execute();

$data = array();
$cont = 0;

while($r=$q->fetch()){

    
    if($r['especial'] == 1){
        $especial = "<b class='badge badge-success'>Si</b>";
    }else{
        $especial = "<b class='badge badge-danger'>No</b>";
    }


        $temp = array();

        array_push($temp, '<img src="https://www.habbo.es/habbo-imaging/avatarimage?user='.$r['habbo'].'&direction=2&head_direction=2&gesture=sml&size=l&action=" style="width:50px; height:auto;" id="avatar'.$r['id'].'"/> '.$r['habbo']);
        array_push($temp, '<img src="https://www.habbo.es/habbo-imaging/avatarimage?user='.nombre_habbo($r['created_by']).'&direction=2&head_direction=2&gesture=sml&size=l&action=" style="width:50px; height:auto;" id="avatar'.$r['created_by'].'"/> '.nombre_habbo($r['created_by']));
        array_push($temp, $r['user']);
        array_push($temp, fecha_placa_seg($r['id']));
        array_push($temp, nombre_rol_usuario($r['id']));
        array_push($temp, $r['firma']);
        array_push($temp, $especial);

        $checked1 = "";
        $checked2 = "";

        if($r['placa_paga'] == 1){ $checked1 = "checked"; }

        if($r['placa_boni'] == 1){ $checked2 = "checked"; }
        
        if(is_admin_or_more(rol($_SESSION['id']))){

            array_push($temp, '<input type="checkbox" id="paga" '.$checked1.' onclick="marcarPaga('.$r['id'].')"/> Paga
            <input type="checkbox" id="boni" '.$checked2.' onclick="marcarBoni('.$r['id'].')"/> Boni');
                    
        }else{
            array_push($temp, '');
        }

        $botones = '<a href="?p=ver_detalles_habbo&id='.$r['id'].'" data-toggle="tooltip" title="Ver Perfil"><i class="fas fa-eye"></i></a>';


                        if(is_admin_or_more(rol($_SESSION['id']))){

                            $botones .= '&nbsp;
                            <a href="?p=editarusuario&id='.$r['id'].'" data-toggle="tooltip" title="Editar"><i class="fas fa-edit"></i></a>
                            &nbsp;
                            <a href="?p=traslado&id='.$r['id'].'" data-toggle="tooltip" title="Traslado"><i class="fas fa-arrow-right"></i></a>';
                        
                        }

                        if(is_manager_or_more(rol($_SESSION['id']))){

                            $botones .= '&nbsp;
                            <a href="#" onclick="eliminarusuario('.$r['id'].',\''.$r['habbo'].'\')" data-toggle="tooltip" title="Eliminar"><i class="fas fa-trash"></i></a>
                            ';
                        
                      
                        }

                        array_push($temp, $botones);
                        

       

        array_push($data,$temp);
}

echo json_encode(array("data"=>$data));
?>