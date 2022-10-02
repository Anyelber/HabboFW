<?php

// Logistica -> Oficinistas y Agentes
// Supervisor -> Oficinistas Agentes y Seguridad
// 

function check_conectado(){
    if(!isset($_SESSION['id'])){
        redir("./");
    }
}



function clear($var){
    return htmlspecialchars($var);
}

function alert($msj, $url, $type){

    if($type == 0){
        $t = "error";
    }else{
        $t = "success";
    }

    $_SESSION['msg'] = $msj;
    $_SESSION['type_msg'] = $t;

    redir($url);

}

function redir($url){
    ?>
    <script>
        window.location="<?=$url?>";
    </script>
    <?php
    die();
}

function check_msg(){
    if(isset($_SESSION['msg']) && !empty($_SESSION['msg'])){




        ?>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            Swal.fire(
                '',
                '<?=$_SESSION["msg"]?>',
                '<?=$_SESSION["type_msg"]?>'
            )
        </script>
        <?php

        $_SESSION['msg'] = "";
        $_SESSION['type_msg'] = "";
    }
}

function nombre_usuario($id){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    return "[".$r['user']."]";
}

function nombre_habbo($id){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    return $r['habbo'];
}

function keko($id){

    
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();


    ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function(){
            // Get Habbos from our API
            $.getJSON('https://www.habbo.es/api/public/users?name=<?=$r["habbo"]?>&rand='.md5(date("Y-m-d H:i:s").rand(0,9999999999)), function(habbos){
                url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                $("#keko").attr("src",url);
            });
        })
    </script>
    <?php

}

function rol($id){
    global $pdo;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    return $r['rol'];
}

function esta_despedido($id){
    $id = clear($id);
    if(rol($id)<0){
        return true;
    }else{
        return false;
    }
}

function nombre_rol($id){
    if($id == "-1"){
        return "Despedido";
    }else{
        global $pdo;
        $q = $pdo->prepare("SELECT nombre FROM roles WHERE id = :id");
        $q->execute([$id]);

        $r = $q->fetch();

        return $r['nombre'];
    }
}

function nombre_rol_usuario($id){
    global $pdo;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);

    $r = $q->fetch();

    if($r['rol'] == "-1"){
        return "Despedido";
    }else{

        $q2 = $pdo->prepare("SELECT * FROM roles WHERE id = :id");
        $q2->execute([$r['rol']]);

        $r2 = $q2->fetch();

        return $r2['nombre'];
    }
}

function nombre_antiguo_rol_usuario($id){
    global $pdo;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);

    $r = $q->fetch();

    $rol = $r['rol']-1;

    $q2 = $pdo->prepare("SELECT * FROM roles WHERE id = :id");
    $q2->execute([$rol]);

    $r2 = $q2->fetch();

    return $r2['nombre'];
}

function rol_usuario($id){
    global $pdo;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);

    $r = $q->fetch();

    return $r['rol'];
}

function roles_options(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM roles ORDER BY id ASC");
    $q->execute();
    while($r = $q->fetch()){
        if($r['id']!=65){
            ?>
                <option value="<?=$r['id']?>"><?=$r['nombre']?></option>
            <?php
        }
    }
}

function fecha_placa_seg($id){
    global $pdo;
    $id = clear($id);

    $q = $pdo->prepare("SELECT created_at FROM ascensos WHERE id_recibe = :id AND new_rol = 12");
    $q->execute([
        $id
    ]);

    if($q->rowCount()>0){
        $r = $q->fetch();
        return date("d/m/Y",strtotime($r['created_at']));
    }else{
        return "--";
    }
}

function tabla_usuarios(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
    $q->execute();
    while($r=$q->fetch()){

        if($r['especial'] == 1){
            $especial = "<b class='badge badge-success'>Si</b>";
        }else{
            $especial = "<b class='badge badge-danger'>No</b>";
        }
        ?>
            <tr>
                
                <td><?=keko_user($r['id'])?>
            
                <?=$r['habbo']?></td>
                <td><?=keko_user($r['created_by'])?>
                <?=nombre_habbo($r['created_by'])?></td>
                <td><?=$r['user']?></td>
                <td><?=fecha_placa_seg($r['id'])?></td>
                <td><?=nombre_rol_usuario($r['id'])?></td>
                <td><?=$r['firma']?></td>
                <td><?=$especial?></td>
                <td>
                <?php
                        if(is_admin_or_more(rol($_SESSION['id']))){
                    ?>
                    <input type="checkbox" id="paga" <?php if($r['placa_paga'] == 1){ echo "checked"; } ?> onclick="marcarPaga(<?=$r['id']?>)"/> Paga
                    <input type="checkbox" id="boni" <?php if($r['placa_boni'] == 1){ echo "checked"; } ?> onclick="marcarBoni(<?=$r['id']?>)"/> Boni
                    <?php
                        }
                    ?>
                </td>
                <td>
                    <a href="?p=ver_detalles_habbo&id=<?=$r['id']?>" data-toggle="tooltip" title="Ver Perfil"><i class="fas fa-eye"></i></a>
                    <?php
                        if(is_admin_or_more(rol($_SESSION['id']))){
                    ?>
                            &nbsp;
                            <a href="?p=editarusuario&id=<?=$r['id']?>" data-toggle="tooltip" title="Editar"><i class="fas fa-edit"></i></a>
                            &nbsp;
                            <a href="?p=traslado&id=<?=$r['id']?>" data-toggle="tooltip" title="Traslado"><i class="fas fa-arrow-right"></i></a>
                        
                        <?php
                        }

                        if(is_manager_or_more(rol($_SESSION['id']))){
                            ?>
                        
                        &nbsp;
                        <a href="#" onclick="eliminarusuario(<?=$r['id']?>,'<?=$r['habbo']?>')" data-toggle="tooltip" title="Eliminar"><i class="fas fa-trash"></i></a>
                        <?php
                            }
                        ?>
                </td>
            </tr>
        <?php
    }

    ?>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      function eliminarusuario(id, nombre){
        Swal.fire({
            icon: 'warning',
          title: 'Estás seguro de querer eliminar a <span style="color:red">'+nombre+'</span>',
          showDenyButton: false,
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#d33',
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            window.location="?p=usuarios&eliminar="+id
          } 
        })
      }
      </script>
    <?php

}

function is_funder($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();
    if($r['rol']>=63){
        return true;
    }else{
        return false;
    }
}


function cant_times_validos_user($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT created_at, ended_at FROM times WHERE id_dio = :id AND pagado = 0 AND is_timing = 0 AND valid_timer = 1");
    $q->execute([$id]);

    $cont = 0;

    while($r = $q->fetch()){
        $time1 = new DateTime($r['created_at']);
        $dif = $time1->diff(new DateTime($r['ended_at']));


        if($dif->d>0){
            $min_dias = (($dif->d * 24) * 60);
        }else{
            $min_dias = 0;
        }
        
        $min_sumar = $dif->h * 60;

        $cont += $dif->i + $min_sumar + $min_dias;
    }

    $contt = $cont / 60;

    return number_format($contt,2,".",",");
}


function cant_times_validos_user_text($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT * FROM times WHERE id_dio = :id AND pagado = 0 AND is_timing = 0 AND valid_timer = 1");
    $q->execute([$id]);

    $cont = 0;

    $hrs = 0;
    $mins = 0;

    while($r = $q->fetch()){
        $time1 = new DateTime($r['created_at']);
        $dif = $time1->diff(new DateTime($r['ended_at']));

        
        $hrs += $dif->h;

        if($dif->d>0){
            $hrs += $dif->d * 24;
        }

        $mins += $dif->i;
    }

    $horas_sumar = floor($mins/60);
    $mins_finales = $mins % 60;



    $hrs += $horas_sumar;

    return $hrs." Hora(s) ".$mins_finales." Minuto(s)";
}

function es_especial($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT especial FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    if($r['especial'] == 1){
        return true;
    }else{
        return false;
    }
}

function horas_trabajadas($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT created_at, ended_at FROM times WHERE id_recibe = :id AND pagado_recibe = 0 AND is_timing = 0");
    $q->execute([$id]);

    $hrs = 0;

    $minutos = 0;
    while($r = $q->fetch()){
        $t1 = new DateTime($r['created_at']);
        $dif = $t1->diff(new DateTime($r['ended_at']));

        if($dif->d>0){
            $minutos_dias = (($dif->h * 24) * 60);
        }else{
            $minutos_dias = 0;
        }

        $minutos_horas = $dif->h * 60;
        $minutos_minutos = $dif->i;

        $minutos += $minutos_horas + $minutos_minutos + $minutos_dias;

    }

    
    $hrs = ($minutos / 60);

    return $hrs;

}

function horas_trabajadas_text($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT * FROM times WHERE id_recibe = :id AND pagado_recibe = 0 AND is_timing = 0");
    $q->execute([$id]);

    $hrs = 0;

    
    
    $minutos = 0;

    while($r = $q->fetch()){
        $t1 = new DateTime($r['created_at']);
        $dif = $t1->diff(new DateTime($r['ended_at']));

        if($dif->d>0){
            $hrs += $dif->d * 24;
        }

        $hrs = $hrs + $dif->h;
        $minutos = $minutos + $dif->i;

    }




    $horas_adicional = floor($minutos/60);

    $hrs += $horas_adicional;
    $minutos_convertidos = $minutos % 60;

    
    
    echo  $hrs." Hora(s) ".$minutos_convertidos." Minuto(s)"; 

    
    // list($whole, $decimal) = explode('.', $hrs);

    // $minutos_final = $decimal * 60;
    // $horas_final = $whole;

    // return $horas_final." Hora(s), ".$minutos_final." Minuto(s)";

}


function horas_trabajadas_text_return($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT * FROM times WHERE id_recibe = :id AND pagado_recibe = 0 AND is_timing = 0");
    $q->execute([$id]);

    $hrs = 0;

    
    
    $minutos = 0;

    while($r = $q->fetch()){
        $t1 = new DateTime($r['created_at']);
        $dif = $t1->diff(new DateTime($r['ended_at']));

        if($dif->d>0){
            $hrs += $dif->d * 24;
        }

        $hrs = $hrs + $dif->h;
        $minutos = $minutos + $dif->i;

    }




    $horas_adicional = floor($minutos/60);

    $hrs += $horas_adicional;
    $minutos_convertidos = $minutos % 60;

    
    
    return  $hrs." Hora(s) ".$minutos_convertidos." Minuto(s)"; 

    
    // list($whole, $decimal) = explode('.', $hrs);

    // $minutos_final = $decimal * 60;
    // $horas_final = $whole;

    // return $horas_final." Hora(s), ".$minutos_final." Minuto(s)";

}

function minutos_trabajados($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT * FROM times WHERE id_recibe = :id AND pagado_recibe = 0 AND is_timing = 0");
    $q->execute([$id]);

    $hrs = 0;

    $minutos = 0;
    while($r = $q->fetch()){
        $t1 = new DateTime($r['created_at']);
        $dif = $t1->diff(new DateTime($r['ended_at']));

        $minutos = $minutos + $dif->i;

    }

    return $minutos;

}

function horas_trabajadas_profile($id){
 
    
    $minutos = minutos_trabajados($id);

    
    $hrs = ($minutos / 60);

    if(es_especial($id)){
        if(is_supervisor($id)){
            $necesarias = "9";
        }elseif(is_director($id)){
            $necesarias = "9";
        }elseif(is_director($id)){
            $necesarias = "9";
        }else{
            $necesarias = "--";
        }
    }else{

        if(rol($id)>= 5 && rol($id)<=18){
            $necesarias = 6;
        }elseif(rol($id)>=19 && rol($id)<=25){
            $necesarias = 7;
        }else{
            $necesarias = "--";
        }
    }

    echo horas_trabajadas_text($id)." / ".$necesarias." Horas";

}

function horas_trabajadas_return($id){
 

    
    $hrs = ($minutos / 60);

    if(es_especial($id)){
        if(is_supervisor($id)){
            $necesarias = "9";
        }elseif(is_director($id)){
            $necesarias = "9";
        }elseif(is_director($id)){
            $necesarias = "9";
        }else{
            $necesarias = "--";
        }
    }else{

        if(rol($id)>= 5 && rol($id)<=18){
            $necesarias = 6;
        }elseif(rol($id)>=19 && rol($id)<=25){
            $necesarias = 7;
        }else{
            $necesarias = "--";
        }
    }

    return horas_trabajadas_text_return($id)." / ".$necesarias." Horas";

}

function horas_total_trabajadas($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT * FROM times WHERE id_recibe = :id AND pagado = 0");
    $q->execute([$id]);

    $hrs = 0;

    $minutes_total = 0;
    $hours_total = 0;

    $timing = 0;
    

    while($r = $q->fetch()){

        if($r['is_timing'] == 1){
            $timing = 1;
        }else{

            
            $t1 = new DateTime($r['created_at']);
            $dif = $t1->diff(new DateTime($r['ended_at']));

            $minutes = $dif->i;

            $minutes_total += $minutes;

        }

        

    }

    $temp_total = $minutes_total / 60;


    if($temp_total<1){
        $hours_total = $minutes_total." Minuto(s)";
    }else{
        $hours_total = number_format(($minutes_total / 60),2,",",".")." Hora(s)";

    }


    

    if($timing == 1){
        return $hours_total." <span class='badge badge-warning'>Timing</span>";
    }else{
        return $hours_total;
    }

}

function minutos_total_trabajados($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT * FROM times WHERE id_recibe = :id AND pagado = 0");
    $q->execute([$id]);

    $hrs = 0;

    $minutes_total = 0;
    $hours_total = 0;

    $timing = 0;
    

    while($r = $q->fetch()){

        if($r['is_timing'] == 1){
            $timing = 1;
        }else{

            
            $t1 = new DateTime($r['created_at']);
            $dif = $t1->diff(new DateTime($r['ended_at']));

            $minutes = $dif->days * 24 * 60;
            $minutes += $dif->h * 60;
            $minutes += $dif->i;

            $minutes_total += $minutes;

        }

        

    }

    

    if($timing == 1){
        return "<span class='badge badge-warning'>Timing</span>";
    }else{
        return $minutes_total." Minutos(s)";
    }

}

function cant_ascensos($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT id FROM ascensos WHERE id_dio = :id AND pagado = 0");
    $q->execute([$id]);

    return $q->rowCount();

}

function status_pago($user){
    global $req_pago_seg;
    global $req_pago_tec;
    global $req_pago_log;
    global $req_pago_sup;
    global $req_pago_dir;
    global $req_pago_pre;
    global $req_pago_eli;
    global $req_pago_jtd;
    global $req_pago_especial_log;
    global $req_pago_especial_other;
    global $pdo;
    
    $q = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $q->execute([
        $user
    ]);

    $r = $q->fetch();
    
    $qa = $pdo->prepare("SELECT id FROM ascensos WHERE id_dio = :id AND pagado = 0");
    $qa->execute([$r['id']]);

    $ascensos = $qa->rowCount();


    $es_especial = es_especial($r['id']);



    if($r['rol'] >=5 && $r['rol'] <=11){
        $requisito = $req_pago_seg;
    }elseif($r['rol'] >= 12 && $r['rol'] <= 18){
        $requisito = $req_pago_tec;
    }elseif($r['rol'] >= 19 && $r['rol'] <= 25){
        $requisito = $req_pago_log;
    }elseif($r['rol'] >= 26 && $r['rol'] <= 32){
        if($es_especial){
            $requisito = $req_pago_especial_other;
        }else{
            $requisito = $req_pago_sup;
        }
    }elseif($r['rol'] >= 33 && $r['rol'] <= 39){
        if($es_especial){
            $requisito = $req_pago_especial_other;
        }else{
            $requisito = $req_pago_dir;
        }
    }elseif($r['rol'] >= 40 && $r['rol'] <= 46){
        if($es_especial){
            $requisito = $req_pago_especial_other;
        }else{
            $requisito = $req_pago_pre;
        }
    }elseif($r['rol'] >= 47 && $r['rol'] <= 53){
        if($es_especial){
            $requisito = $req_pago_especial_other;
        }else{
            $requisito = $req_pago_eli;
        }
    }elseif($r['rol'] >= 54 && $r['rol'] <= 60){
        if($es_especial){
            $requisito = $req_pago_especial_other;
        }else{
            $requisito = $req_pago_jtd;
        }
    }elseif($r['rol']<5 || $r['rol']>60){
        $requisito = "na";
    }


    if($requisito == "na"){
        $status = -1;
    }else{
        if($requisito[0] == "time"){
            if(horas_trabajadas($r['id']) >= $requisito[1]){
                if(horas_trabajadas($r['id']) >= ($requisito[2] + $requisito[1])){
                    $status = 2;
                }else{
                    $status = 1;
                }
            }else{
                $status = 0;
            }

        }elseif($requisito[0] == "asc"){
            if(cant_ascensos($r['id']) >= $requisito[1]){
                if(cant_ascensos($r['id']) >= ($requisito[2] + $requisito[1])){
                    $status = 2;
                }else{
                    $status = 1;
                }
            }else{
                $status = 0;
            }
        }elseif($requisito[0] == "asct"){
            if(cant_ascensos($r['id']) >= $requisito[1]){
                if(cant_times_validos_user($r['id']) >= $requisito[2]){
                    $status = 2;
                }else{
                    $status = 1;
                }
            }else{
                $status = 0;
            }
        }elseif($requisito[0] == "timea"){
            if(cant_times_validos_user($r['id'])>=$requisito[1]){
                if(cant_ascensos($r['id'])>=$requisito[2]){
                    $status = 2;
                }else{
                    $status = 1;
                }
            }else{
                $status = 0;
            }
        }
    }

    return $status;
}

function tabla_pagos(){
    global $req_pago_seg;
    global $req_pago_tec;
    global $req_pago_log;
    global $req_pago_sup;
    global $req_pago_dir;
    global $req_pago_pre;
    global $req_pago_eli;
    global $req_pago_jtd;
    global $req_pago_especial_log;
    global $req_pago_especial_other;
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM users ORDER BY id DESC");
    $q->execute();
    while($r=$q->fetch()){

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


       
        ?>
            <tr>
                <td><?=$r['id']?></td>
                <td><?=keko_user($r['id'])?></td>
                <td><?=$r['habbo']?></td>
                <td><?=nombre_rol($r['rol'])?></td>
                <td><?=$ascensos?></td>
                <td><?=$times_validos?></td>
                <td><?php echo horas_trabajadas_text($r['id'])?></td>
                <td><?=$asc?></td>
                <td><?=$save?></td>
                <td>
                    
                    <?=$status?>
                    <?=$placa_paga?>
                    <?=$placa_boni?>

                </td>
                <td>
                    <a href="?p=ver_detalles_habbo&id=<?=$r['id']?>" data-toggle="tooltip" title="Ver Detalles"><i class="fas fa-eye"></i></a>
                        &nbsp;
                    <a href="?p=pagos&pagar=<?=$r['id']?>" data-toggle="tooltip" title="Pagar"><i class="fa fa-check"></i></a>
                        &nbsp;
                    <a href="?p=pagar_ascenso&id=<?=$r['id']?>" data-toggle="tooltip" title="Pagar con Ascenso"><i class="fa fa-crown"></i></a>
                        &nbsp;
                    <a href="?p=pagos&rechazar=<?=$r['id']?>" data-toggle="tooltip" title="Rechazar"><i class="fas fa-times-circle"></i></a>
                </td>
            </tr>
        <?php
    }

}



function tabla_pagos_realizados($date=""){
    global $pdo;
    if(empty($date)){
        $q = $pdo->prepare("SELECT * FROM pagas WHERE status = 1 ORDER BY id DESC LIMIT 200");
        $q->execute();
    }else{
        $date_busq = "%".$date."%";
        $q = $pdo->prepare("SELECT * FROM pagas WHERE status = 1 AND fecha LIKE :date ORDER BY id DESC");
        $q->execute([
            $date_busq
        ]);
    }
    while($r=$q->fetch()){

       $qu = $pdo->prepare("SELECT * FROM users WHERE id = :id");
       $qu->execute([$r['id_recibe']]);

       $ru = $qu->fetch();



        $ascensos = $r['ascensos'];
        $hrs_trabajadas = $r['horas'];
        $times_validos = $r['times'];

        if($r['tipo'] == 0){
            $tipo = "Paga Creditos";
            $tipoc = "success";
        }else{
            $tipo = "Paga Ascenso";
            $tipoc = "primary";
        }

        if($r['rol_pago'] == 0){
            $rol = "N/A";
        }else{
            $rol = nombre_rol($r['rol_pago']);
        }




       
        ?>
            <tr>
                <td><?=$r['id']?></td>
                <td><?=keko_user($ru['id'])?></td>
                <td><?=$ru['habbo']?></td>
                <td><span class="badge badge-<?=$tipoc?>"><?=$tipo?></span></td>
                <td><?=$rol?></td>
                <td><?=date("d/m/Y h:i a", strtotime($r['fecha']))?></td>
            </tr>
        <?php
    }

}

function tabla_pagos_rechazados($date=""){
    global $pdo;
    if(empty($date)){
        $q = $pdo->prepare("SELECT * FROM pagas WHERE status = 0 ORDER BY id DESC LIMIT 200");
        $q->execute();
    }else{
        $date_busq = "%".$date."%";
        $q = $pdo->prepare("SELECT * FROM pagas WHERE status = 0 AND fecha LIKE :date ORDER BY id DESC");
        $q->execute([
            $date_busq
        ]);
    }
    while($r=$q->fetch()){

       $qu = $pdo->prepare("SELECT * FROM users WHERE id = :id");
       $qu->execute([$r['id_recibe']]);

       $ru = $qu->fetch();



        $ascensos = $r['ascensos'];
        $hrs_trabajadas = $r['horas'];
        $times_validos = $r['times'];




       
        ?>
            <tr>
                <td><?=$r['id']?></td>
                <td><?=keko_user($ru['id'])?></td>
                <td><?=$ru['habbo']?></td>
                <td><?=nombre_rol($ru['rol'])?></td>
                <td><?=date("d/m/Y h:i a", strtotime($r['fecha']))?></td>
            </tr>
        <?php
    }

}

function keko_user($id, $px="50", $act="", $dir=2){
    global $pdo;
    $q = $pdo->prepare("SELECT id,habbo FROM users WHERE id = :id");
    $q->execute([$id]);

    $r = $q->fetch();
    //&rand=<?=md5(date("Y-m-d H:i:s").rand(0,99999999))
    ?>
            <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=$r['habbo']?>&direction=<?=$dir?>&head_direction=<?=$dir?>&gesture=sml&size=l&action=<?=$act?>" style="width:<?=$px?>px; height:auto;" id="avatar<?=$r['id']?>"/>
    <?php

}

function tabla_roles(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM roles ORDER BY id ASC");
    $q->execute();
    while($r=$q->fetch()){
        ?>
            <tr>
                <td><?=$r['id']?></td>
                <td><?=$r['nombre']?></td>
                <td>
                    <a href="#" onclick="eliminarrol(<?=$r['id']?>)" data-toggle="tooltip" title="Eliminar"><i class="fas fa-trash"></i></a>
                </td>
            </tr>

         
        <?php
    }

}

function options_usuarios_pendientes_ascenso(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM users WHERE rol <= 63");
    $q->execute();

    while($r=$q->fetch()){
        if(requiere_ascenso($r['id'])){
            ?>
                <option value="<?=$r['id']?>"><?=$r['habbo']?></option>
            <?php
        }
    }
    
}



function options_roles(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM roles WHERE id < 65");
    $q->execute();

    while($r=$q->fetch()){
            ?>
                <option value="<?=$r['id']?>"><?=$r['nombre']?></option>
            <?php
    }
    
}

function lista_usuarios_pendientes_ascenso(){
    global $pdo;
    $q = $pdo->prepare("SELECT id,habbo FROM users WHERE rol <= 63");
    $q->execute();

    while($r=$q->fetch()){
        if(requiere_ascenso($r['id'])){
            ?>
                <li><a href="#" onclick="prepararUsuario(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><?=$r['habbo']?></a></li>
            <?php
        }
    }

}

function lista_usuarios_degrado(){
    global $pdo;
    if(is_owner_or_more(rol($_SESSION['id']))){
        $q = $pdo->prepare("SELECT id,habbo FROM users");
    }else{
        $q = $pdo->prepare("SELECT id,habbo FROM users WHERE rol <= 63");
    }
    $q->execute();

    while($r=$q->fetch()){
        ?>
            <li><a href="#" onclick="prepararUsuario(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><?=$r['habbo']?></a></li>
        <?php
    }
    
}

function lista_usuarios_despido(){
    global $pdo;
    if(is_owner_or_more(rol($_SESSION['id']))){
        $q = $pdo->prepare("SELECT id,habbo FROM users");
    }else{
        $q = $pdo->prepare("SELECT id,habbo FROM users WHERE rol <= 63");
    }
    $q->execute();

    while($r=$q->fetch()){
        ?>
            <li><a href="#" onclick="prepararUsuario2(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><?=$r['habbo']?></a></li>
        <?php
    }
    
}

function lista_usuarios_pendientes_ascenso2(){
    global $pdo;
    $q = $pdo->prepare("SELECT id,habbo FROM users");
    $q->execute();

    while($r=$q->fetch()){
        ?>
            <li><a href="#" onclick="prepararUsuario2(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><?=$r['habbo']?></a></li>
        <?php
    }
    
}

function lista_usuarios_pendientes_save(){
    global $pdo;
    $q = $pdo->prepare("SELECT id,habbo FROM users");
    $q->execute();

    while($r=$q->fetch()){

       if(!tiene_save($r['id'])){

            ?>
                <li><a href="#" onclick="prepararUsuario1(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><?=$r['habbo']?></a></li>
            <?php
        }
    }
    
}

function lista_usuarios_pendientes_save2(){
    global $pdo;
    $q = $pdo->prepare("SELECT id,habbo FROM users");
    $q->execute();

    while($r=$q->fetch()){

       if(!tiene_fila($r['id'])){

            ?>
                <li><a href="#" onclick="prepararUsuario2(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><?=$r['habbo']?></a></li>
            <?php
        }
    }
    
}

function lista_usuarios_pendientes_save3(){
    global $pdo;
    $q = $pdo->prepare("SELECT id,habbo FROM users");
    $q->execute();

    while($r=$q->fetch()){

       if(!tiene_vip($r['id'])){

            ?>
                <li><a href="#" onclick="prepararUsuario3(<?=$r['id']?>,'<?=$r['habbo']?>','<?=rango_habbo($r['id'])?>');"><?=$r['habbo']?></a></li>
            <?php
        }
    }
    
}

function tiene_save($id){
    global $pdo;
    $q = $pdo->prepare("SELECT id FROM saves WHERE id_recibe = :id AND status = 1 AND tipo = 0 LIMIT 1");
    $q->execute([$id]);

    if($q->rowCount()==0){
        return false;
    }else{
        return true;
    }
}

function tiene_fila($id){
    global $pdo;
    $q = $pdo->prepare("SELECT id FROM saves WHERE id_recibe = :id AND status = 1 AND tipo = 1 LIMIT 1");
    $q->execute([$id]);

    if($q->rowCount()==0){
        return false;
    }else{
        return true;
    }
}

function tiene_vip($id){
    global $pdo;
    $q = $pdo->prepare("SELECT id FROM saves WHERE id_recibe = :id AND status = 1 AND tipo = 2 LIMIT 1");
    $q->execute([$id]);

    if($q->rowCount()==0){
        return false;
    }else{
        return true;
    }
}

function tiene_pase($id){
    global $pdo;
    $q = $pdo->prepare("SELECT id FROM saves WHERE id_recibe = :id AND status = 1 AND tipo = 3 LIMIT 1");
    $q->execute([$id]);

    if($q->rowCount()==0){
        return false;
    }else{
        return true;
    }
}

function lista_usuarios_pendientes_time(){
    global $pdo;
    if(rol($_SESSION['id'])>=54){
        $q = $pdo->prepare("SELECT * FROM users WHERE id != :id");
    }else{
        $q = $pdo->prepare("SELECT * FROM users WHERE id != :id AND especial = 0");
    }
    $q->execute([$_SESSION['id']]);

    while($r=$q->fetch()){
        if(!is_timing($r['id'])){
        ?>
            <li><a href="#" onclick="prepararUsuario(<?=$r['id']?>,'<?=$r['habbo']?>');"><?=$r['habbo']?></a></li>
        <?php
        }
    }
    
}

function is_timing($id){
    global $pdo;
    $q = $pdo->prepare("SELECT is_timing FROM `times` WHERE id_recibe = :id ORDER BY id DESC LIMIT 1");
    $q->execute([$id]);

    $r = $q->fetch();

    if($r['is_timing'] == 1){
        return true;
    }else{
        return false;
    }
}

function is_timing_user($id){
    global $pdo;
    $q = $pdo->prepare("SELECT is_timing FROM `times` WHERE id_recibe = :id ORDER BY id DESC LIMIT 1");
    $q->execute([$id]);

    $r = $q->fetch();

    if($r['is_timing'] == 1){
        return true;
    }else{
        return false;
    }
}

function tabla_ascensos(){
    global $pdo;

    if(is_admin_or_more(rol($_SESSION['id']))){
        $q = $pdo->prepare("SELECT * FROM ascensos ORDER BY id DESC LIMIT 100");
        $q->execute();
    }else{
        $q = $pdo->prepare("SELECT * FROM ascensos WHERE id_dio = :id ORDER BY id DESC LIMIT 100");
        $q->execute([$_SESSION['id']]);
    }
    while($r=$q->fetch()){

        if($r['tipo'] == 0){
            $tipo = "<span class='badge badge-primary'>Ascenso</span>";
        }elseif($r['tipo'] == 1){
            $tipo = "<span class='badge badge-warning'>Ascenso Merito</span>";
        }elseif($r['tipo'] == 2){
            $tipo = "<span class='badge badge-success'>Paga Ascenso</span>";
        }elseif($r['tipo'] == 3){
            $tipo = "<span class='badge badge-info'>Paga Ascenso Save</span>";
        }elseif($r['tipo'] == 4){
            $tipo = "<span class='badge badge-default'>Ascenso Libre</span>";
        }

        if($r['pagado'] == 0){
            $status = "<span class='badge badge-success'>No Pagado</span>";
        }else{
            $status = "<span class='badge badge-danger'>Pagado ó Revocado</span>";
        }

        ?>
            <tr>
                <td><?=$r['id']?></td>
                <td>
                <div style="display: flex; align-items:center;">
                    <div style="display:inline-block;">
                    <?=keko_user($r['id_dio'])?>   
                    </div>
                    <div style="display:inline-block;">
                    <?=nombre_habbo($r['id_dio'])?><br>
                    <b><?=nombre_rol(rol($r['id_dio']))?></b>
                        <?php
                            if(is_admin_or_more(rol($_SESSION['id']))){
                                if($r['pagado'] == 0){
                                ?>
                                    <br>
                                    <a href="?p=ascensos&revocar=<?=$r['id']?>">Revocar</a>
                                <?php
                                }else{
                                    ?>
                                        <br>
                                        <a href="?p=ascensos&devolver=<?=$r['id']?>">Devolver</a>
                                    <?php

                                }
                            }
                        ?>
                    </div>
                    </div>
                </td>
                <td>
                    <div style="display: flex; align-items:center;">
                    <div style="display:inline-block;">
                    <?=keko_user($r['id_recibe'])?>   
                    </div>
                    <div style="display:inline-block;">
                    <?=nombre_habbo($r['id_recibe'])?><br>
                    <b><?=nombre_rol(rol($r['id_recibe']))?></b>
                    </div>
                    </div>
                </td>
                <td><?=nombre_rol($r['old_rol'])?></td>
                <td><?=nombre_rol($r['new_rol'])?></td>
                <td><?=date("d/m/Y h:i a", strtotime($r['created_at']))?></td>
                <td><?=$status?></td>
                <td><?=$tipo?></td>
                <td>
                    <?php
                        if(rol($_SESSION['id'])>=63){
                    ?>
                    <a href="#" onclick="eliminarrol(<?=$r['id']?>)" data-toggle="tooltip" title="Eliminar"><i class="fas fa-trash"></i></a>
                    <?php
                        }
                    ?>
                </td>
            </tr>

        
        <?php
    }

}


function tabla_ascensos_pendientes(){
    global $pdo;
    $q = $pdo->prepare("SELECT id,habbo FROM users ORDER BY id DESC");
    $q->execute();
    
    while($r=$q->fetch()){
        if(requiere_ascenso($r['id'])){
            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td>
                        <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id'])?><br>
                        <b><?=nombre_rol(rol($r['id']))?></b>
                        </div>
                        </div>
                    </td>
                    
                </tr>

            
            <?php
        }
    }

}

function tabla_degrados(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM degrados ORDER BY id ASC");
    $q->execute();
    while($r=$q->fetch()){

        if($r['tipo'] == 0){
            $tipo = "<span class='badge badge-warning'>Degrado</span>";
        }else{
            $tipo = "<span class='badge badge-danger'>Despido</span>";
        }

        if($r['motivo'] == 0){
            $motivo = "Robo";

        }elseif($r['motivo']==1){
            $motivo = "Petar";

        }elseif($r['motivo']==2){
            $motivo = "Acosar";

        }elseif($r['motivo']==3){
            $motivo = "Auto Ascenso";
        }elseif($r['motivo']==4){
            $motivo = "Troleo";
        }elseif($r['motivo']==5){
            $motivo = "Insulto";
        }elseif($r['motivo']==6){
            $motivo = "Desobediencia";
        }elseif($r['motivo']==7){
            $motivo = "Actividad Baja";
        }elseif($r['motivo']==8){
            $motivo = "Auto Degrado";
        }elseif($r['motivo']==9){
            $motivo = "Traslado Agencia";
        }elseif($r['motivo']==10){
            $motivo = "Renuncia";
        }elseif($r['motivo']==11){
            $motivo = "Doble Empleo";
        }

            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td>
                    <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id_dio'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id_dio'])?><br>
                        <b><?=nombre_rol(rol($r['id_dio']))?></b>
                        </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id_recibe'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id_recibe'])?><br>
                        <b><?=nombre_rol(rol($r['id_recibe']))?></b>
                        </div>
                        </div>
                    </td>
                    <td><?=nombre_rol($r['old_rol'])?></td>
                    <td><?=nombre_rol($r['new_rol'])?></td>
                    <td><?=$motivo?></td>
                    <td><?=date("d/m/Y h:i a", strtotime($r['created_at']))?></td>
                    <td><?=$tipo?></td>
                </tr>

            
            <?php
        }

}


function tabla_ascensos_admin(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM ascensos_admin ORDER BY id ASC");
    $q->execute();
    while($r=$q->fetch()){

        if($r['tipo']==0){
            $tipo = "<span class='badge badge-primary'>Ajuste de rango</span>";
        }elseif($r['tipo'] == 1){
            $tipo = "<span class='badge badge-success'>Traslado</span>";
        }elseif($r['tipo'] == 2){
            $tipo = "<span class='badge badge-warning'>Venta de rango</span>";
        }

            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td>
                        <?=keko_user($r['id_dio'])?>    
                        <?=nombre_habbo($r['id_dio'])?>
                    </td>
                    <td>
                        <?=keko_user($r['id_recibe'])?>   
                        <?=nombre_habbo($r['id_recibe'])?>
                    </td>
                    <td><?=nombre_rol($r['old_rol'])?></td>
                    <td><?=nombre_rol($r['new_rol'])?></td>
                    <td><?=$tipo?></td>
                    <td><?=date("d/m/Y h:i a", strtotime($r['created_at']))?></td>
                </tr>

            
            <?php
    }

}



function tabla_times_admin(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM times_admin ORDER BY id ASC");
    $q->execute();
    while($r=$q->fetch()){

            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td>
                        <?=keko_user($r['id_dio'])?>    
                        <?=nombre_habbo($r['id_dio'])?>
                    </td>
                    <td>
                        <?=keko_user($r['id_recibe'])?>   
                        <?=nombre_habbo($r['id_recibe'])?>
                    </td>
                    <td><?=$r['tipo']?></td>
                    <td><?=$r['cantidad']?></td>
                    <td><?=date("d/m/Y h:i a", strtotime($r['created_at']))?></td>
                </tr>

            
            <?php
    }

}

function tabla_users_vip(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM saves WHERE status = 1 AND tipo = 2 ORDER BY id ASC");
    $q->execute();

   

    while($r=$q->fetch()){

        if($r['tipo'] == 0){
            $tipo = "<span class='badge badge-warning'>Save</span>";
        }elseif($r['tipo'] == 1){
            $tipo = "<span class='badge badge-primary'>Fila</span>";
        }elseif($r['tipo'] == 2){
            $tipo = "<span class='badge badge-danger'>VIP</span>";
        }elseif($r['tipo'] == 3){
            $tipo = "<span class='badge badge-success'>PASE</span>";
        }

       
            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td><?=$tipo?></td>
                    <td>
                        <?=keko_user($r['id_dio'])?>    
                        <?=nombre_habbo($r['id_dio'])?>
                    </td>
                    <td>
                        <?=keko_user($r['id_recibe'])?>   
                        <?=nombre_habbo($r['id_recibe'])?>
                    </td>
                    <td><?=date("d/m/Y", strtotime($r['fecha']))?></td>
                    <td>
                        <a href="?p=membresias&eliminar_vip=<?=$r['id']?>" ><i class="fas fa-trash"></i> Eliminar VIP</a>
                </td>
                </tr>

            
            <?php
    }

}


function tabla_users_save(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM saves WHERE status = 1 AND tipo IN (0,1,3) ORDER BY id ASC");
    $q->execute();

   

    while($r=$q->fetch()){

        if($r['tipo'] == 0){
            $tipo = "<span class='badge badge-warning'>Save</span>";
        }elseif($r['tipo'] == 1){
            $tipo = "<span class='badge badge-primary'>Fila</span>";
        }elseif($r['tipo'] == 2){
            $tipo = "<span class='badge badge-danger'>VIP</span>";
        }elseif($r['tipo'] == 3){
            $tipo = "<span class='badge badge-success'>PASE</span>";
        }

       
            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td><?=$tipo?></td>
                    <td>
                        <?=keko_user($r['id_dio'])?>    
                        <?=nombre_habbo($r['id_dio'])?>
                    </td>
                    <td>
                        <?=keko_user($r['id_recibe'])?>   
                        <?=nombre_habbo($r['id_recibe'])?>
                    </td>
                    <td><?=date("d/m/Y", strtotime($r['fecha']))?></td>
                    <td><?=date("d/m/Y", strtotime($r['fecha_exp']))?></td>
                    <td>
                        <?php
                         if($r['extendido_por'] == 0){
                            echo "--";
                        }else{
                            keko_user($r['extendido_por']);
                            echo nombre_habbo($r['extendido_por']);
                        }
                        ?>
                    </td>
                    <td>
                        <a href="?p=membresias&eliminar=<?=$r['id']?>" ><i class="fas fa-trash"></i> Eliminar</a>
                        &nbsp;
                        <a href="?p=membresias&extender=<?=$r['id']?>" ><i class="fas fa-check"></i> Extender</a>
                </td>
                </tr>

            
            <?php
    }

}




function tabla_users_save_antiguos(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM saves WHERE status = 0 ORDER BY id ASC");
    $q->execute();
    while($r=$q->fetch()){

        if($r['tipo'] == 0){
            $tipo = "<span class='badge badge-warning'>Save</span>";
        }elseif($r['tipo'] == 1){
            $tipo = "<span class='badge badge-primary'>Fila</span>";
        }elseif($r['tipo'] == 2){
            $tipo = "<span class='badge badge-danger'>VIP</span>";
        }elseif($r['tipo'] == 3){
            $tipo = "<span class='badge badge-success'>PASE</span>";
        }

        
        $continuar = true;

        if($r['tipo'] == 2 && $r['deleted_by'] == 0){
            $continuar = false;
        }

        if($continuar == true){

       

            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td><?=$tipo?></td>
                    <td>
                        <?=keko_user($r['id_dio'])?>    
                        <?=nombre_habbo($r['id_dio'])?>
                    </td>
                    <td>
                        <?=keko_user($r['id_recibe'])?>   
                        <?=nombre_habbo($r['id_recibe'])?>
                    </td>
                    <td><?=date("d/m/Y", strtotime($r['fecha']))?></td>
                    <td><?=date("d/m/Y", strtotime($r['fecha_exp']))?></td>
                    <td> 
                    <?php
                         if($r['extendido_por'] == 0){
                            echo "--";
                        }else{
                            keko_user($r['extendido_por']);
                            echo nombre_habbo($r['extendido_por']);
                        }
                        ?>
                        </td>
                    <td>
                        <?php
                            if($r['deleted_by'] != 0){
                        ?>
                        <?=keko_user($r['deleted_by'])?>   
                        <?=nombre_habbo($r['deleted_by'])?>
                        <?php
                            }else{
                                echo "Expirado";
                            }
                        ?>
                    </td>
                </tr>

            
            <?php
        }
    }

}


function tabla_times_curso(){
    global $pdo;
    if(rol($_SESSION['id'])>=61){
        $q = $pdo->prepare("SELECT * FROM `times` WHERE is_timing = 1 ORDER BY id ASC LIMIT 100");
        $q->execute();
    }else{
        $q = $pdo->prepare("SELECT * FROM times WHERE id_dio = :id AND is_timing = 1 ORDER BY id ASC LIMIT 100");
        $q->execute([
            $_SESSION['id']
        ]);
    }
    while($r=$q->fetch()){

            $ta = strtotime(date("Y-m-d H:i:s"));
            $t = strtotime($r['created_at']);

            $datetimeObj1 = new DateTime(date("Y-m-d H:i:s"));
            $datetimeObj2 = new DateTime($r['created_at']);
            $interval = $datetimeObj1->diff($datetimeObj2);
            
            if($interval->format('%a') > 0){
            $hour1 = $interval->format('%a')*24;
            }
            if($interval->format('%h') > 0){
            $hour2 = $interval->format('%h');
            }


            $horas = $interval->h;
            
            if($interval->d>0){
                $horas += $interval->d * 24;
            }

            $tiempo_transcurrido = $horas." Hora(s) ".$interval->i." Minuto(s)";  //abs(($ta - $t) / (60*60));


            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td>
                    <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id_dio'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id_dio'])?><br>
                        <b><?=nombre_rol(rol($r['id_dio']))?></b>
                        </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id_recibe'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id_recibe'])?><br>
                        <b><?=nombre_rol(rol($r['id_recibe']))?></b>
                        </div>
                        </div>
                    </td>
                    <td><?=date("d/m/Y h:i a", strtotime($r['created_at']))?></td>
                    <td>
                        <?=$tiempo_transcurrido?>
                    </td>
                    <td>
                        <?php
                        /*if(horas_trabajadas($r['id_recibe'])>=1){
                            echo number_format(horas_trabajadas($r['id_recibe']),2,",",".")." Horas";
                        }else{
                            echo minutos_trabajados($r['id_recibe'])." Minutos";
                        }*/
                        echo horas_trabajadas_text($r['id_recibe']);
                        ?>
                    </td>
                    <td>
                        <?php
                            if($_SESSION['id'] == $r['id_dio'] || rol($_SESSION['id']) >= 61){
                        ?>
                            <a style="color:red; cursor:pointer;" onclick="pararTime(<?=$r['id']?>,'<?=nombre_habbo($r['id_recibe'])?>')" data-toggle="tooltip" title="Parar Time"><i class="fas fa-stop"></i></a>
                        <?php
                            }
                        ?>

                        &nbsp;

                        <?php
                            if(rol($_SESSION['id']) >= 61){
                        ?>
                            <a style="color:red; cursor:pointer;" onclick="pararTimeAdmin(<?=$r['id']?>,'<?=nombre_habbo($r['id_recibe'])?>')" data-toggle="tooltip" title="Invalidar Time"><i class="fas fa-handshake-slash"></i></a>
                        <?php
                            }
                        ?>
                    </td>
                </tr>

            
            <?php
    }

}


function tabla_times_realizados(){
    global $pdo;

    if(rol($_SESSION['id'])>=61){
        $q = $pdo->prepare("SELECT * FROM `times` WHERE is_timing = 0 ORDER BY id DESC LIMIT 100");
        $q->execute();
    }else{
        $q = $pdo->prepare("SELECT * FROM times WHERE id_dio = :id AND is_timing = 0 ORDER BY id DESC LIMIT 100");
        $q->execute([
            $_SESSION['id']
        ]);
    }
    while($r=$q->fetch()){

            $ta = strtotime(date("Y-m-d H:i:s"));
            $t = strtotime($r['created_at']);

            $datetimeObj1 = new DateTime($r['created_at']);
            $datetimeObj2 = new DateTime($r['ended_at']);
            $interval = $datetimeObj1->diff($datetimeObj2);
            
            if($interval->format('%a') > 0){
            $hour1 = $interval->format('%a')*24;
            }
            if($interval->format('%h') > 0){
            $hour2 = $interval->format('%h');
            }

            $horas = $interval->h;
            
            if($interval->d>0){
                $horas += $interval->d * 24;
            }

            $tiempo_transcurrido = $horas." Hora(s) ".$interval->i." Minuto(s)";  //abs(($ta - $t) / (60*60));

            if($r['pagado'] == 1){
                $status = "<span class='badge badge-danger'>Pagado</span>";
            }else{
                $status = "<span class='badge badge-success'>No Pagado</span>";

            }

            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td>
                    <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id_dio'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id_dio'])?><br>
                        <b><?=nombre_rol(rol($r['id_dio']))?></b>
                        </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id_recibe'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id_recibe'])?><br>
                        <b><?=nombre_rol(rol($r['id_recibe']))?></b>
                        </div>
                        </div>
                    </td>
                    <td><?=date("d/m/Y h:i a", strtotime($r['created_at']))?></td>
                    <td><?=date("d/m/Y h:i a", strtotime($r['ended_at']))?></td>
                    <td>
                        <?=$tiempo_transcurrido?>
                    </td>
                    <td>
                       
                    <?php
                       echo horas_trabajadas_text($r['id_recibe']);
                    ?>
                    </td>
                    <td>
                        <?=$status?>
                    </td>
                    <td>
                        <?php
                            if(is_founder_or_more(rol($_SESSION['id']))){
                                ?>
                                    <a href="#" onclick="borrarTime(<?=$r['id']?>)"><i class="fas fa-trash"></i> Borrar</a>
                                <?php
                            }
                        ?>
                    </td>
                </tr>

            
            <?php
    }

}

function requiere_ascenso($id){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM ascensos WHERE id_recibe = :id ORDER BY id DESC LIMIT 1");
    $q->execute([$id]);

    if($q->rowCount()==0){
        return true;
    }else{
        
        $r = $q->fetch();

        if(is_agente($id)){
            global $agente_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$agente_tiempo));  
        }elseif(is_seguridad($id)){
            global $seguridad_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$seguridad_tiempo));
        }elseif(is_tecnico($id)){
            global $tecnico_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$tecnico_tiempo));
        }elseif(is_logistica($id)){
            global $logistica_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$logistica_tiempo));
        }elseif(is_supervisor($id)){
            global $supervisor_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$supervisor_tiempo));
        }elseif(is_director($id)){
            global $director_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$director_tiempo));
        }elseif(is_presidente($id)){
            global $presidente_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$presidente_tiempo));
        }elseif(is_elite($id)){
            global $elite_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$elite_tiempo));
        }elseif(is_junta_directiva($id)){
            global $junta_directiva_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$junta_directiva_tiempo));
        }elseif(is_administrador($id)){
            global $administrador_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$administrador_tiempo));
        }elseif(is_manager($id)){
            global $manager_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$manager_tiempo));
        }elseif(is_founder($id)){
            global $founder_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$founder_tiempo));
        }elseif(is_owner($id)){
            global $owner_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$owner_tiempo));
        }elseif(is_developer($id)){
            global $developer_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$developer_tiempo));
        }

        if(strtotime(date("Y-m-d H:i:s")) >= strtotime($hora_ascenso)){
            return true;
        }else{
            return false;
        }

    }

}

function firma($id){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $q->execute([$id]);

    $r = $q->fetch();

    return $r['firma'];
}

function tabla_ascendidos_user($id){
    global $pdo;
    $id = clear($id);

    $q = $pdo->prepare("SELECT * FROM ascensos WHERE id_dio = :id ORDER BY id DESC");
    $q->execute([$id]);
    while($r = $q->fetch()){
        ?>
        <tr>
            <td><?=$r['id']?></td>
            <td><?=keko_user($r['id_recibe'])?></td>
            <td><?=nombre_habbo($r['id_recibe'])?></td>
            <td><?=nombre_rol($r['new_rol'])?></td>
            <td><?=date("d/m/Y h:i a",strtotime($r['created_at']))?></td>
        </tr>
        <?php
    }
}

function tabla_ascensos_recibidos_user($id){
    global $pdo;
    $id = clear($id);

    $q = $pdo->prepare("SELECT * FROM ascensos WHERE id_recibe = :id ORDER BY id DESC");
    $q->execute([$id]);
    while($r = $q->fetch()){
        ?>
        <tr>
            <td><?=$r['id']?></td>
            <td><?=keko_user($r['id_dio'])?></td>
            <td><?=nombre_habbo($r['id_dio'])?></td>
            <td><?=nombre_rol($r['new_rol'])?></td>
            <td><?=date("d/m/Y h:i a",strtotime($r['created_at']))?></td>
        </tr>
        <?php
    }
}

function tabla_times_user($id){
    global $pdo;
    $id = clear($id);

    $q = $pdo->prepare("SELECT * FROM times WHERE id_dio = :id ORDER BY id DESC");
    $q->execute([$id]);
    while($r = $q->fetch()){

        $time1 = new DateTime($r['created_at']);
        $dif = $time1->diff(new DateTime($r['ended_at']));
        
        $min_sumar = $dif->h;

        $cont += $dif->i + $min_sumar;
        ?>
        <tr>
            <td><?=$r['id']?></td>
            <td><?=keko_user($r['id_recibe'])?></td>
            <td><?=nombre_habbo($r['id_recibe'])?></td>
            <td><?=date("d/m/Y h:i a",strtotime($r['created_at']))?></td>
            <td>
               Horas: <?=$dif->h?><br>
               Minutos: <?=$dif->i?>
            </td>
        </tr>
        <?php
    }
}

function tabla_horas_user($id){
    global $pdo;
    $id = clear($id);

    $q = $pdo->prepare("SELECT * FROM times WHERE id_recibe = :id ORDER BY id DESC");
    $q->execute([$id]);
    while($r = $q->fetch()){


        $time1 = new DateTime($r['created_at']);
        $dif = $time1->diff(new DateTime($r['ended_at']));
        
        $min_sumar = $dif->h;

        $cont += $dif->i + $min_sumar;


        ?>
        <tr>
            <td><?=$r['id']?></td>
            <td><?=keko_user($r['id_dio'])?></td>
            <td><?=nombre_habbo($r['id_dio'])?></td>
            <td><?=date("d/m/Y h:i a",strtotime($r['created_at']))?></td>
            <td><?=date("d/m/Y h:i a",strtotime($r['ended_at']))?></td>
            <td>
               Horas: <?=$dif->h?><br>
               Minutos: <?=$dif->i?>
            </td>
        </tr>
        <?php
    }
}

























function is_agente($id){
    global $pdo;
    global $agente_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$agente_roles)){
        return true;
    }else{
        return false;
    }
}

function is_seguridad($id){
    global $pdo;
    global $seguridad_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$seguridad_roles)){
        return true;
    }else{
        return false;
    }
}

function is_tecnico($id){
    global $pdo;
    global $tecnico_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$tecnico_roles)){
        return true;
    }else{
        return false;
    }
}

function is_logistica($id){
    global $pdo;
    global $logistica_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$logistica_roles)){
        return true;
    }else{
        return false;
    }
}

function is_supervisor($id){
    global $pdo;
    global $supervisor_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$supervisor_roles)){
        return true;
    }else{
        return false;
    }
}

function is_director($id){
    global $pdo;
    global $director_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$director_roles)){
        return true;
    }else{
        return false;
    }
}

function is_presidente($id){
    global $pdo;
    global $presidente_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$presidente_roles)){
        return true;
    }else{
        return false;
    }
}

function is_elite($id){
    global $pdo;
    global $elite_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$elite_roles)){
        return true;
    }else{
        return false;
    }
}

function is_junta_directiva($id){
    global $pdo;
    global $junta_directiva_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$junta_directiva_roles)){
        return true;
    }else{
        return false;
    }
}

function is_administrador($id){
    global $pdo;
    global $administrador_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$administrador_roles)){
        return true;
    }else{
        return false;
    }
}

function is_manager($id){
    global $pdo;
    global $manager_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$manager_roles)){
        return true;
    }else{
        return false;
    }
}

function is_founder($id){
    global $pdo;
    global $founder_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$founder_roles)){
        return true;
    }else{
        return false;
    }
}

function is_owner($id){
    global $pdo;
    global $owner_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$owner_roles)){
        return true;
    }else{
        return false;
    }
}

function is_developer($id){
    global $pdo;
    global $developer_roles;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    $rol = $r['rol'];

    if(in_array($rol,$developer_roles)){
        return true;
    }else{
        return false;
    }
}


function tiene_poder_ascenso($user1, $user2){
  
    if(is_agente($user1)){
        $max = 0;
    }

    if(is_seguridad($user1)){
        $max = 0;
    }

    if(is_tecnico($user1)){
        $max = 0;
    }

    if(is_logistica($user1)){
        $max = 4;
    }

    if(is_supervisor($user1)){
        $max = 10;
    }

    if(is_director($user1)){
        $max = 17;
    }

    if(is_presidente($user1)){
        $max = 24;
    }

    if(is_elite($user1)){
        $max = 31;
    }

    if(is_junta_directiva($user1)){
        $max = 38;
    }

    if(is_administrador($user1)){
        $max = 52;
    }

    if(is_manager($user1)){
        $max = 59;
    }

    if(is_founder($user1)){
        $max = 61;
    }

    if(is_owner_or_more(rol($user1))){
        $max = 9999;
    }

    if(rol($user2)>$max){
        return false;
    }else{
        return true;
    }

}

function tiene_poder_time($user1, $user2){
  
    if(is_agente($user1)){
        $max = 0;
    }

    if(is_seguridad($user1)){
        $max = 0;
    }

    if(is_tecnico($user1)){
        $max = 0;
    }

    if(is_logistica($user1)){
        $max = 0;
    }

    if(is_supervisor($user1)){
        $max = 0;
    }

    if(is_director($user1)){
        $max = 25;
    }

    if(is_presidente($user1)){
        $max = 25;
    }

    if(is_elite($user1)){
        $max = 25;
    }

    if(is_junta_directiva($user1)){
        $max = 46;
    }

    if(is_administrador($user1)){
        $max = 9999;
    }

    if(is_manager($user1)){
        $max = 9999;
    }

    if(is_founder($user1)){
        $max = 9999;
    }

    if(is_owner_or_more(rol($user1))){
        $max = 9999;
    }

    if(rol($user2)>$max){
        return false;
    }else{
        return true;
    }

}



function tiene_poder($user1, $user2){
  
    if(is_agente($user1)){
        $max = 0;
    }

    if(is_seguridad($user1)){
        $max = 0;
    }

    if(is_tecnico($user1)){
        $max = 0;
    }

    if(is_logistica($user1)){
        $max = 4;
    }

    if(is_supervisor($user1)){
        $max = 11;
    }

    if(is_director($user1)){
        $max = 18;
    }

    if(is_presidente($user1)){
        $max = 25;
    }

    if(is_elite($user1)){
        $max = 32;
    }

    if(is_junta_directiva($user1)){
        $max = 39;
    }

    if(is_administrador($user1)){
        $max = 53;
    }

    if(is_manager($user1)){
        $max = 60;
    }

    if(is_founder($user1)){
        $max = 61;
    }

    if(is_owner_or_more(rol($user1))){
        $max = 999;
    }

    if(rol($user2)>$max){
        return false;
    }else{
        return true;
    }

}

function is_agente_or_more($rol){
    if($rol>=0){
        return true;
    }else{
         return false;
    }
}

function is_seguridad_or_more($rol){
    if($rol>=5){
        return true;
    }else{
         return false;
    }
}

function is_tecnico_or_more($rol){
    if($rol>=12){
        return true;
    }else{
         return false;
    }
 }

 function is_logistica_or_more($rol){
     if($rol>=19){
         return true;
     }else{
         return false;
     }
 }

 function is_supervisor_or_more($rol){
     if($rol>=26){
         return true;
     }else{
         return false;
     }
 }

 function is_director_or_more($rol){
     if($rol>=33){
         return true;
     }else{
         return false;
     }
 }

 function is_presidente_or_more($rol){
     if($rol>=40){
         return true;
     }else{
         return false;
     }
 }

 function is_elite_or_more($rol){
     if($rol>=47){
         return true;
     }else{
         return false;
     }
 }

 function is_junta_directiva_or_more($rol){
     if($rol>=54){
         return true;
     }else{
         return false;
     }
 }



 function is_admin_or_more($rol){
    if($rol>=61){
        return true;
    }else{
         return false;
    }
 }

function is_manager_or_more($rol){
   if($rol>=62){
       return true;
   }else{
        return false;
   }
}

function is_founder_or_more($rol){
   if($rol>=63){
       return true;
   }else{
        return false;
   }
}

function is_owner_or_more($rol){
   if($rol>=64){
       return true;
   }else{
        return false;
   }
}

function rango_habbo($id){
    global $pdo;
    $q = $pdo->prepare("SELECT rol FROM users WHERE id = :id");
    $q->execute([$id]);

    $r = $q->fetch();

    return nombre_rol($r['rol']);
}

function generar_codigo(){
    $cod = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",0,1,2,3,4,5,6,7,8,9);
    $completo = $cod[rand(0,(sizeof($cod)-1))].$cod[rand(0,(sizeof($cod)-1))].$cod[rand(0,(sizeof($cod)-1))].$cod[rand(0,(sizeof($cod)-1))].$cod[rand(0,(sizeof($cod)-1))];

    return $completo;
}

function codigo($id){
    global $pdo;
    $id = clear($id);
    $q = $pdo->prepare("SELECT codigo FROM users WHERE id = :id");
    $q->execute([$id]);
    $r = $q->fetch();

    return $r['codigo'];
}

function actualizar_codigo_seguridad($id){
    global $pdo;
    $id = clear($id);
    $nc = generar_codigo();
    $q = $pdo->prepare("UPDATE users SET codigo = :nc WHERE id = :id");
    $q->execute([
        $nc,
        $id
    ]);

}

function verificar_codigo($id,$codigo){
    global $pdo;
    $id = clear($id);
    $codigo = clear($codigo);

    $q = $pdo->prepare("SELECT id FROM users WHERE id = :id AND codigo = :codigo");
    $q->execute([$id, $codigo]);
    if($q->rowCount()>0){
        return true;
    }else{
        return false;
    }
}

function notificar_staff($texto){
    global $pdo;
    $q = $pdo->prepare("SELECT id FROM users WHERE rol >= 61");
    $q->execute();

    while($r=$q->fetch()){
        notificar($r['id'],$texto);
    }
}

function notificar($id, $texto){
    global $pdo;
    $fecha = date("Y-m-d H:i:s");
    $q = $pdo->prepare("INSERT INTO notificaciones (id_recibe, texto, fecha, status) VALUES (:idr, :t, :fecha, 0)");
    $q->execute([
        $id,
        $texto,
        $fecha
    ]);
}

function set_last_visit($id){
    global $pdo;
    $id = clear($id);
    $fecha = date("Y-m-d H:i:s");
    $q = $pdo->prepare("UPDATE users SET last_visit = :fecha WHERE id = :id");
    $q->execute([
        $fecha,
        $id
    ]);
}

function proximo_ascenso($id){


    global $pdo;
    $q = $pdo->prepare("SELECT * FROM ascensos WHERE id_recibe = :id ORDER BY id DESC LIMIT 1");
    $q->execute([$id]);

    if($q->rowCount()==0){
        return "--";
    }else{
        
        $r = $q->fetch();

        if(is_agente($id)){
            global $agente_tiempo;
            $hora_ascenso = date("d/m/Y h:i a", strtotime($r['created_at']." +".$agente_tiempo));  
        }elseif(is_seguridad($id)){
            global $seguridad_tiempo;
            $hora_ascenso = date("d/m/Y h:i a", strtotime($r['created_at']." +".$seguridad_tiempo));
        }elseif(is_tecnico($id)){
            global $tecnico_tiempo;
            $hora_ascenso = date("d/m/Y h:i a", strtotime($r['created_at']." +".$tecnico_tiempo));
        }elseif(is_logistica($id)){
            global $logistica_tiempo;
            $hora_ascenso = date("d/m/Y h:i a", strtotime($r['created_at']." +".$logistica_tiempo));
        }elseif(is_supervisor($id)){
            global $supervisor_tiempo;
            $hora_ascenso = date("d/m/Y h:i a", strtotime($r['created_at']." +".$supervisor_tiempo));
        }elseif(is_director($id)){
            global $director_tiempo;
            $hora_ascenso = date("d/m/Y h:i a", strtotime($r['created_at']." +".$director_tiempo));
        }elseif(is_presidente($id)){
            global $presidente_tiempo;
            $hora_ascenso = date("d/m/Y h:i a", strtotime($r['created_at']." +".$presidente_tiempo));
        }elseif(is_elite($id)){
            global $elite_tiempo;
            $hora_ascenso = date("d/m/Y h:i a", strtotime($r['created_at']." +".$elite_tiempo));
        }elseif(is_junta_directiva($id)){
            global $junta_directiva_tiempo;
            $hora_ascenso = date("d/m/Y h:i a", strtotime($r['created_at']." +".$junta_directiva_tiempo));
        }elseif(is_administrador($id)){
            global $administrador_tiempo;
            $hora_ascenso = "--";
        }elseif(is_manager($id)){
            global $manager_tiempo;
            $hora_ascenso = "--";
        }elseif(is_founder($id)){
            global $founder_tiempo;
            $hora_ascenso = "--";
        }elseif(is_owner($id)){
            global $owner_tiempo;
            $hora_ascenso = "--";
        }elseif(is_developer($id)){
            global $developer_tiempo;
            $hora_ascenso = "--";
        }

        
        return $hora_ascenso;

    }

}


function proximo_ascenso_default($id){


    global $pdo;
    $q = $pdo->prepare("SELECT * FROM ascensos WHERE id_recibe = :id ORDER BY id DESC LIMIT 1");
    $q->execute([$id]);

    if($q->rowCount()==0){
        return "--";
    }else{
        
        $r = $q->fetch();

        if(is_agente($id)){
            global $agente_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$agente_tiempo));  
        }elseif(is_seguridad($id)){
            global $seguridad_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$seguridad_tiempo));
        }elseif(is_tecnico($id)){
            global $tecnico_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$tecnico_tiempo));
        }elseif(is_logistica($id)){
            global $logistica_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$logistica_tiempo));
        }elseif(is_supervisor($id)){
            global $supervisor_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$supervisor_tiempo));
        }elseif(is_director($id)){
            global $director_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$director_tiempo));
        }elseif(is_presidente($id)){
            global $presidente_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$presidente_tiempo));
        }elseif(is_elite($id)){
            global $elite_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$elite_tiempo));
        }elseif(is_junta_directiva($id)){
            global $junta_directiva_tiempo;
            $hora_ascenso = date("Y-m-d H:i:s", strtotime($r['created_at']." +".$junta_directiva_tiempo));
        }elseif(is_administrador($id)){
            global $administrador_tiempo;
            $hora_ascenso = "--";
        }elseif(is_manager($id)){
            global $manager_tiempo;
            $hora_ascenso = "--";
        }elseif(is_founder($id)){
            global $founder_tiempo;
            $hora_ascenso = "--";
        }elseif(is_owner($id)){
            global $owner_tiempo;
            $hora_ascenso = "--";
        }elseif(is_developer($id)){
            global $developer_tiempo;
            $hora_ascenso = "--";
        }

        
        return $hora_ascenso;

    }

}

function cant_ascensos_necesarios($id){
    global $pdo;

    if(is_agente($id)){
        return "0";
    }elseif(is_seguridad($id)){
        return "0";
    }elseif(is_tecnico($id)){
        return "0";
    }elseif(is_logistica($id)){
        return "0";
    }elseif(is_supervisor($id)){
        global $req_pago_sup;
        $ascensos = $req_pago_sup[1] + $req_pago_sup[2];
        return $ascensos;
    }elseif(is_director($id)){
        global $req_pago_dir;
        $ascensos = $req_pago_dir[1];
        return $ascensos;
    }elseif(is_presidente($id)){
        global $req_pago_pre;
        $ascensos = $req_pago_pre[2];
        return $ascensos;
    }elseif(is_elite($id)){
        global $req_pago_eli;
        $ascensos = $req_pago_eli[2];
        return $ascensos;
    }elseif(is_junta_directiva($id)){
        global $req_pago_jtd;
        $ascensos = $req_pago_jtd[2];
        return $ascensos;
    }else{
        return "--";
    }

}

function cant_times_necesarios($id){
    global $pdo;

    if(is_agente($id)){
        return "0";
    }elseif(is_seguridad($id)){
        return "0";
    }elseif(is_tecnico($id)){
        return "0";
    }elseif(is_logistica($id)){
        return "0";
    }elseif(is_supervisor($id)){
        return "0";
    }elseif(is_director($id)){
        global $req_pago_dir;
        $times = $req_pago_dir[2];
        return $times;
    }elseif(is_presidente($id)){
        global $req_pago_pre;
        $times = $req_pago_pre[1];
        return $times;
    }elseif(is_elite($id)){
        global $req_pago_eli;
        $times = $req_pago_eli[1];
        return $times;
    }elseif(is_junta_directiva($id)){
        global $req_pago_jtd;
        $times = $req_pago_jtd[1];
        return $times;
    }else{
        return "--";
    }

}

function ascensos_realizados($id){
    global $pdo;
    $id = clear($id);

    $q = $pdo->prepare("SELECT id FROM ascensos WHERE id_dio = :id");
    $q->execute([$id]);

    $cant = cant_ascensos($id);

    if(es_especial($id)){
        $ascensos = "--";
    }else{
        $ascensos = cant_ascensos_necesarios($id);
    }


    return $cant." / ".$ascensos;
}
function times_tomados($id){
    global $pdo;
    $id = clear($id);


    $cant = cant_times_validos_user_text($id);
    $times = cant_times_necesarios($id);

    return $cant." / ".$times." Horas";
}

function esta_conectado($user){

//     global $pdo;

//     $q = $pdo->prepare("SELECT habbo FROM users WHERE id = :id");
//     $q->execute([$user]);

//     $r = $q->fetch();

//     $keko = $r['habbo'];

//     include 'vendor/autoload.php';

//     // Shortcut for the FQN
//     //use HabboAPI\Entities\Badge;
//     //use HabboAPI\Entities\Habbo;
//     //use HabboAPI\Entities\Photo;
//     //use HabboAPI\Entities\Profile;
//     //use HabboAPI\HabboAPI;
//     //use HabboAPI\HabboParser;

//     // Create new Parser and API instance
//     $habboParser = new HabboAPI\HabboParser('es');
//     $habboApi = new HabboAPI\HabboAPI($habboParser);

//     try {
//         // Find the user 'koeientemmer' and get their ID
//         $myHabbo = $habboApi->getHabbo($keko);
//     } catch (Exception $e) {
//         echo '
//             <p>Oops. Ha ocurrido un error con la api de habbo!</p>
//             <p>Informale este error al administrador!</p>
//         ';
//         exit();
//     }

//     if ($myHabbo->hasProfile()) {
//         // Collect all the profile info
//         /** @var Profile $myProfile */
//         $myProfile = $habboApi->getProfile($myHabbo->getId());
//     } else {
//         // This Habbo has a closed home, only show their Habbo object
//         $myProfile = new HabboAPI\Entities\Profile();
//         $myProfile->setHabbo($myHabbo);
//     }

//     $habbo = $myProfile->getHabbo();

    
//    if($habbo->isOnline()){
//        return true;
//    }else{
//        return false;
//    }

   return true;
}

function pertenece_coca($keko){

   
//     /* Deshabilitado por ahora */

//     include 'vendor/autoload.php';

//     // Shortcut for the FQN
//     //use HabboAPI\Entities\Badge;
//     //use HabboAPI\Entities\Habbo;
//     //use HabboAPI\Entities\Photo;
//     //use HabboAPI\Entities\Profile;
//     //use HabboAPI\HabboAPI;
//     //use HabboAPI\HabboParser;

//     // Create new Parser and API instance
//     $habboParser = new HabboAPI\HabboParser('es');
//     $habboApi = new HabboAPI\HabboAPI($habboParser);

//     try {
//         // Find the user 'koeientemmer' and get their ID
//         $myHabbo = $habboApi->getHabbo($keko);
//     } catch (Exception $e) {
//         echo '
//             <p>Oops. Ha ocurrido un error con la api de habbo!</p>
//             <p>Informale este error al administrador!</p>
//             <p>El usuario <b>"'.$keko.'"</b> No existe</p>
//         ';
//         exit();
//     }

//     if ($myHabbo->hasProfile()) {
//         // Collect all the profile info
//         $myProfile = $habboApi->getProfile($myHabbo->getId());
//     } else {
//         // This Habbo has a closed home, only show their Habbo object
//         $myProfile = new HabboAPI\Entities\Profile();
//         $myProfile->setHabbo($myHabbo);
//     }

//     $pertenece_grupo = 0; 

//     if(is_array(call_user_func(array($myProfile, "getgroups"))) || is_object(call_user_func(array($myProfile, "getgroups")))){

//         foreach (call_user_func(array($myProfile, "getgroups")) as $object) {
//             if(!empty(strpos($object,"COCA COLA COMPANY")) || !empty(strpos($object,"COCA COLA 50K"))){
//                 $pertenece_grupo = 1;
//             }else{
//                 $pertenece_grupo = 0;
//             }
//         }
//     }else{
//         $pertenece_grupo = 0;
//     }

    

//     $pertenece_grupo = 1;

    
//    if($pertenece_grupo == 1){
//        return true;
//    }else{
//        return false;
//    }

return true;

}

function reiniciar_user($id){
    global $pdo;
    $id = clear($id);


    $q = $pdo->prepare("UPDATE ascensos SET pagado = 1 WHERE id_dio = :id");
    $q->execute([$id]);


    $q = $pdo->prepare("UPDATE times SET pagado = 1 WHERE id_dio = :id");
    $q->execute([$id]);


    $q = $pdo->prepare("UPDATE times SET pagado_recibe = 1 WHERE id_recibe = :id");
    $q->execute([$id]);

    $q = $pdo->prepare("UPDATE users SET placa_paga = 0, placa_boni = 0 WHERE id = :id");
    $q->execute([
        $id
    ]);


}

function can_merito($user){
    global $pdo;
    $q = $pdo->prepare("SELECT created_at FROM ascensos WHERE id_recibe = :user AND tipo = 1");
    $q->execute([
        $user
    ]);

    if($q->rowCount()>0){
        $r = $q->fetch();
        
        $date_target = strtotime(date("Y-m-d",strtotime($r['created_at']." +26 days")));
        $actual_date = strtotime(date("Y-m-d"));

        if($actual_date>=$date_target){
            return true;
        }else{
            return false;
        }
    }else{
        return true;
    }
}

function check_saves(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM saves WHERE status = 1 AND tipo < 2");
    $q->execute();

    $fecha = strtotime(date("Y-m-d"));

    $ft = date("Y-m-d");

    while($r = $q->fetch()){
        if($fecha>=strtotime($r['fecha_exp'])){
            $qf = $pdo->prepare("UPDATE saves SET status = 0, deleted_by = 0, fecha_cierre = :ft WHERE id = :id");
            $qf->execute([
                $ft,
                $r['id']
            ]);
            notificar_staff("se ha vencido el save de ".nombre_habbo($r['id_redibe']));
        }

    }

}

function tabla_sugerencias(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM sugerencias WHERE deleted = 0");
    $q->execute();

    while($r = $q->fetch()){

        if($r['tipo'] == 0){
            $tipo = "Queja Foro Web";
        }elseif($r['tipo'] == 1){
            $tipo = "Sugerencia Foro Web";
        }elseif($r['tipo'] == 2){
            $tipo = "Queja Personal Administrativo";
        }elseif($r['tipo'] == 3){
            $tipo = "Sugerencia Personal Administrativo";
        }elseif($r['tipo'] == 4){
            $tipo = "Queja Sistema de Pagos";
        }elseif($r['tipo'] == 5){
            $tipo = "Sugerencia Sistema de Pagos";
        }elseif($r['tipo'] == 6){
            $tipo = "Queja Discord";
        }elseif($r['tipo'] == 7){
            $tipo = "Sugerencia Discord";
        }elseif($r['tipo'] == 8){
            $tipo = "Queja Radio";
        }elseif($r['tipo'] == 9){
            $tipo = "Sugerencia Radio";
        }elseif($r['tipo'] == 10){
            $tipo = "Queja Trabajador";
        }

        ?>
            <tr>

                <td><?=keko_user($r['created_by'])?>
            <?=nombre_habbo($r['created_by'])?></td>
            <td><?=$tipo?></td>
            <td><?=$r['text']?></td>
            <td><?=date("d/m/Y h:i a",strtotime($r['fecha']))?></td>
            <td>
                <?php
                    if(strlen($r['imagen'])>0){
                        ?>
                            <a href="imgs_sugerencias/<?=$r['imagen']?>" target="_blank"><i data-toggle="tooltip" title="Ver Anexo" class="fas fa-eye"></i></a> &nbsp;
                        <?php
                    }
                ?>
                <a href="?p=sugerencias&eliminar=<?=$r['id']?>"><i data-toggle="tooltip" title="Eliminar" class='fa fa-trash'></i></a> &nbsp; 
                <a href="?p=chat&responderSugerencia=<?=$r['id']?>"><i data-toggle="tooltip" title="Responder Sugerencia" class="fa fa-envelope"></i></a>
            </td>

            </tr>
        <?php
    }
}

function tabla_peticiones(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM peticiones WHERE deleted = 0");
    $q->execute();

    while($r = $q->fetch()){

    if($r['status'] == 0){
        $status = "<span class='badge badge-warning'>Pendiente</span>";
    }else{
        $status = "<span class='badge badge-success'>Completado</span>";

    }

        ?>
            <tr>

            <td><?=keko_user($r['id_user'])?>
            <?=nombre_habbo($r['id_user'])?></td>
           
            <td><?=$r['peticion']?></td>
            <td><?=date("d/m/Y h:i a",strtotime($r['fecha']))?></td>
            <td><?=$status?></td>
            <?php
                if($_SESSION['id'] == 1){
            ?>
            <td><a href="?p=peticiones&eliminar=<?=$r['id']?>"><i data-toggle="tooltip" title="Eliminar" class='fa fa-trash'></i></a> &nbsp;
            <a href="?p=peticiones&completado=<?=$r['id']?>"><i data-toggle="tooltip" title="Completar" class='fa fa-check'></i></a></td>
                    <?php
                }
                    ?>
            </tr>
        <?php
    }
}

function working_on(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM configs WHERE tipo = 'work'");
    $q->execute();
    $r = $q->fetch();

    if($r['valor'] == 1){
        return true;
    }else{
        return false;
    }
}

function tabla_atencion(){
    global $pdo;

   
        $q = $pdo->prepare("SELECT * FROM atencion ORDER BY fecha DESC");
        $q->execute();
    while($r=$q->fetch()){

        $q2 = $pdo->prepare("SELECT id FROM atencion WHERE id_recibe = :id");
        $q2->execute([
            $r['id_recibe']
        ]);





        $cant = $q2->rowCount();


            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td>
                    <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id_dio'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id_dio'])?><br>
                        <b><?=nombre_rol(rol($r['id_dio']))?></b>
                        </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id_recibe'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id_recibe'])?><br>
                        <b><?=nombre_rol(rol($r['id_recibe']))?></b>
                        </div>
                        </div>
                    </td>
                    <td><?=$cant?></td>
                    <td><?=date("d/m/Y h:i a", strtotime($r['fecha']))?></td>
                    <td>
                        <?php
                            if(rol($_SESSION['id'])>=61){
                        ?>
                            <a href="?p=ver_llamados_atencion&id=<?=$r['id_recibe']?>" data-toggle="tooltip" title="Ver Llamados"><i class="fas fa-eye"></i></a>
                        <?php
                            }
                        ?>
                    </td>
                </tr>

            
            <?php
    }

}

function tabla_atencion_user($id){
    global $pdo;
    $id = clear($id);

   
        $q = $pdo->prepare("SELECT * FROM atencion WHERE id_recibe = :id");
        $q->execute([$id]);
    while($r=$q->fetch()){


        if($r['tipo'] == 0){
            $tipof = "No respetar autoridad";

        }elseif($r['tipo'] == 1){
            $tipof = "No respetar compañeros";
            
        }elseif($r['tipo'] == 2){
            $tipof = "Incumplimiento de reglas";
            
        }elseif($r['tipo'] == 3){
            $tipof = "Acoso";
            
        }elseif($r['tipo'] == 4){
            $tipof = "Spam";
        }

            ?>
                <tr>
                    <td><?=$r['id']?></td>
                    <td>
                    <div style="display: flex; align-items:center;">
                        <div style="display:inline-block;">
                        <?=keko_user($r['id_dio'])?>   
                        </div>
                        <div style="display:inline-block;">
                        <?=nombre_habbo($r['id_dio'])?><br>
                        <b><?=nombre_rol(rol($r['id_dio']))?></b>
                        </div>
                        </div>
                    </td>
                    <td><?=$r['texto']?></td>
                    <td><?=$tipof?></td>
                    <td><?=date("d/m/Y h:i a", strtotime($r['fecha']))?></td>
                    <td>
                        <?php
                            $qa = $pdo->prepare("SELECT * FROM imagenes_atencion WHERE id_atencion = :id");
                            $qa->execute([
                                $r['id']
                            ]);

                            $cont = 1;

                            while($ra = $qa->fetch()){
                                ?>
                                    <a href="imgs_atencion/<?=$ra['imagen']?>" target="_blank"><i class="fas fa-eye"></i> Prueba <?=$cont?></a><br>
                                <?php
                                $cont++;
                            }
                        ?>
                    </td>
                </tr>

            
            <?php
    }

}

function es_clon($user1,$user2){
    global $pdo;
    $q1 = $pdo->prepare("SELECT ip FROM users WHERE id = :user1");
    $q2 = $pdo->prepare("SELECT ip FROM users WHERE id = :user2");

    $q1->execute([
        $user1
    ]);

    $q2->execute([
        $user2
    ]);

    $r1 = $q1->fetch();
    $r2 = $q2->fetch();

    if($r1['ip'] == $r2['ip']){
        return true;
    }else{
        return false;
    }
}


function cant_likes($id){
    global $pdo;
    $q = $pdo->prepare("SELECT count(id) AS cantidad FROM likes WHERE id_noticia = :id");
    $q->execute([
        $id
    ]);

    $r = $q->fetch();

    return $r['cantidad'];
}

function lista_likes($id){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM likes WHERE id_noticia = :id LIMIT 10");
    $q->execute([
        $id
    ]);
    $q2 = $pdo->prepare("SELECT id FROM likes WHERE id_noticia = :id");
    $q2->execute([
        $id
    ]);

    if($q2->rowCount()>10){
        $total = $q2->rowCount() - 10;
        $complemento = "y <span style='color:red;font-weight:bold;'>".$total."</span> personas mas dieron like a esta noticia <a href='#' data-toggle='modal' data-target='#likes' onclick='ver_likes(".$id.")'><i class='fas fa-eye'></i> Ver Todas</a>";

    }else{
        $complemento = "Dieron like a esta noticia";
    }

    $text = "";

    while($r = $q->fetch()){
        $text.= "<span style='color:red; font-weight:bold;'>".nombre_habbo($r['id_user'])."</span> , ";
    }

    $ftext = $text." ".$complemento;

    return $ftext;
}

function options_categorias_productos(){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM categorias ORDER BY nombre ASC");
    $q->execute();

    while($r = $q->fetch()){
        ?>
            <option value="<?=$r['id']?>"><?=$r['nombre']?></option>
        <?php
    }
}

function cantidad_amigos($id){
    global $pdo;
    $q = $pdo->prepare("SELECT * FROM amigos WHERE id_user1 = :idu OR id_user2 = :idu2");
    $q->execute([
        $_SESSION['id'],
        $_SESSION['id']
    ]);

    return $q->rowCount();
}

function son_amigos($id1, $id2){
    global $pdo;
    $q = $pdo->prepare("SELECT id FROM amigos WHERE (id_user1 = :id11 AND id_user2 = :id21) OR (id_user1 = :id22 AND id_user2 = :id12)");
    $q->execute([
        $id1,
        $id2,
        $id2,
        $id1
    ]);

    if($q->rowCount()>0){
        return true;
    }else{
        return false;
    }
}

function invitacion_pendiente($sender, $receiver){
    global $pdo;
    $q = $pdo->prepare("SELECT id FROM solicitudes_amistad WHERE sender = :sender AND receiver = :receiver");
    $q->execute([
        $sender,
        $receiver
    ]);

    if($q->rowCount()>0){
        return true;
    }else{
        return false;
    }
}

function cantidad_solicitudes_amistad_pendiente($id){
    $id = clear($id);
    global $pdo;

    $q = $pdo->prepare("SELECT id FROM solicitudes_amistad WHERE receiver = :id");
    $q->execute([
        $id
    ]);

    return $q->rowCount();
}

function cantidad_mensajes_no_leidos($id){
    $id = clear($id);
    global $pdo;

    $q = $pdo->prepare("SELECT id FROM chat WHERE receiver = :id AND status = 0");
    $q->execute([
        $id
    ]);

    return $q->rowCount();
}

function tiene_firma($id){
    global $pdo;
    $id = clear($id);

    $q = $pdo->prepare("SELECT firma FROM users WHERE id = :id");
    $q->execute([
        $id
    ]);

    $r = $q->fetch();

    if(empty($r['firma'])){
        return false;
    }else{
        return true;
    }
}
?>

