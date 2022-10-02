<?php
if(isset($agregar)){

    $verify = $pdo->prepare("SELECT id FROM solicitudes_amistad WHERE sender = :id1 AND receiver = :id2 OR sender = :id22 AND receiver = :id12");
    $verify->execute([
        $_SESSION['id'],
        $agregar,
        $agregar,
        $_SESSION['id']
    ]);

    if($verify->rowCount()>0){
        alert("Ya existe una solicitud de amistad con esta persona, espera a que te responda, o aceptala si el/ella te ha enviado la solicitud a ti","?p=amistades",0);
    }

    $agregar = clear($agregar);
    $fecha = date("Y-m-d H:i:s");
    $q = $pdo->prepare("INSERT INTO solicitudes_amistad (sender, receiver, fecha) VALUES (:sender, :receiver, :fecha)");
    $q->execute([
        $_SESSION['id'],
        $agregar,
        $fecha
    ]);

    alert("Se ha enviado la solicitud de amistad, espera a que la persona acepta la solicitud","?p=amistades",1);
}

if(isset($aceptar)){

    $aceptar = clear($aceptar);

    $qs = $pdo->prepare("SELECT * FROM solicitudes_amistad WHERE id = :id");
    $qs->execute([
        $aceptar
    ]);

    $rs = $qs->fetch();

    $sender = $rs['sender'];
    $receiver = $rs['receiver'];

    $q = $pdo->prepare("DELETE FROM solicitudes_amistad WHERE sender = :sender AND receiver = :receiver");
    $q->execute([
        $sender,
        $receiver
    ]);

    
    $q2 = $pdo->prepare("DELETE FROM solicitudes_amistad WHERE sender = :receiver AND receiver = :sender");
    $q2->execute([
        $receiver,
        $sender
    ]);

    $fecha = date("Y-m-d H:i:s");
    $q3 = $pdo->prepare("INSERT INTO amigos (id_user1, id_user2, fecha) VALUES (:sender, :receiver, :fecha)");
    $q3->execute([
        $sender,
        $receiver,
        $fecha
    ]);

    alert("Se ha aceptado la invitacion de amistad","?p=amistades",1);
}

if(isset($eliminar)){
    $eliminar = clear($eliminar);
    $q = $pdo->prepare("DELETE FROM amigos WHERE id = :id");
    $q->execute([
        $eliminar
    ]);

    alert("Se ha eliminado a esta persona de amigos satisfactoriamente","?p=amistades",1);
}

if(isset($declinar)){

    $declinar = clear($declinar);

    $qs = $pdo->prepare("SELECT * FROM solicitudes_amistad WHERE id = :id");
    $qs->execute([
        $declinar
    ]);

    $rs = $qs->fetch();

    $sender = $rs['sender'];
    $receiver = $rs['receiver'];

    $q = $pdo->prepare("DELETE FROM solicitudes_amistad WHERE sender = :sender AND receiver = :receiver");
    $q->execute([
        $sender,
        $receiver
    ]);


    alert("Se ha declinado la invitacion de amistad","?p=amistades",1);
}
?>

<div style="padding: 30px;"> 


<div class="row">
    <div class="col-sm-4 col-md-4 col-lg-4">Tienes <?=cantidad_amigos($_SESSION['id'])?> Amigo(s)</div>
    <div class="col-sm-4 col-md-4 col-lg-4">
        <div>
            <input style="width: 100%" class="form-control" id="search" placeholder="Busca a alguien para invitarlo a ser tu amigo..."/>
            <div id="output" style="display:none;"></div>
        </div>
    </div>
</div>


<div class="row mt-5">
    &nbsp;
</div>


<?php
$colores = array("default","danger","warning","success","primary","secondary","info");
?>
<span style="color: red;">Solicitudes de amistad</span><br>
        <small>Estas personas quieren ser tus amigos</small><br>
        <div class="row mt-3">
            <?php
                $q = $pdo->prepare("SELECT * FROM solicitudes_amistad WHERE receiver = :idu");
                $q->execute([
                    $_SESSION['id']
                ]);

                if($q->rowCount()>0){

                    while($r = $q->fetch()){
                        ?>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-<?=$colores[rand(0,6)]?>"><img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=nombre_habbo($r['sender'])?>&direction=3&head_direction=3&gesture=sml&size=l&action=wav"/></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"><?=nombre_habbo($r['sender'])?></span>
                                    <span class="info-box-number"><a href="?p=amistades&aceptar=<?=$r['id']?>" class="text text-success">Aceptar</a> &nbsp; &nbsp; <a href="?p=amistades&declinar=<?=$r['id']?>" class="text text-danger">Rechazar</a></span>
                                </div>
                            </div>
                        </div>      

                        <?php
                    }
                   
                }else{
                    echo '<div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">No tienes solicitudes de amistad</i></div>';
                }
            ?>
        </div>
       
            
           


        <div class="row mt-5">&nbsp;</div>



        <span style="color: red;width: 100%;margin-top:100px;">Mis amistades</span><br>
        <small>Lista de tus amigos</small><br>
        <div class="row mt-3">
        <?php
                $q = $pdo->prepare("SELECT * FROM amigos WHERE id_user1 = :idu1 OR id_user2 = :idu2");
                $q->execute([
                    $_SESSION['id'],
                    $_SESSION['id']
                ]);

                if($q->rowCount()>0){

                    while($r = $q->fetch()){

                        if($r['id_user1'] == $_SESSION['id']){
                            $id_habbo = $r['id_user2'];
                            $nombre_habbo = nombre_habbo($r['id_user2']);
                        }else{
                            $id_habbo = $r['id_user1'];
                            $nombre_habbo = nombre_habbo($r['id_user1']);
                        }

                        $t1 = new DateTime($r['fecha']);
                        $t2 = new DateTime(date("Y-m-d H:i:s"));

                        $dif = $t1->diff($t2);

                        $tiempo = "";

                        

                        if($dif->s > 0){
                            if($dif->s == 1){
                                $tiempo = "1 segundo ";
                            }else{
                                $tiempo = $dif->s." segundos ";
                            }
                        }

                        

                        if($dif->i > 0){
                            if($dif->i == 1){
                                $tiempo = "1 minuto ";
                            }else{
                                $tiempo = $dif->i." minutos ";
                            }
                        }

                        

                        if($dif->h > 0){
                            if($dif->h == 1){
                                $tiempo = "1 hora ";
                            }else{
                                $tiempo = $dif->h." horas ";
                            }
                        }

                        

                        if($dif->d > 0){
                            if($dif->d == 1){
                                $tiempo = "1 dia ";
                            }else{
                                $tiempo = $dif->d." dias ";
                            }
                        }

                        if($dif->m > 0){
                            if($dif->m == 1){
                                $tiempo = "1 mes ";
                            }else{
                                $tiempo = $dif->m." meses ";
                            }
                        }

                        if($dif->y >0){
                            if($dif->y == 1){
                                $tiempo = "1 año ";
                            }else{
                                $tiempo = $dif->y." años ";
                            }
                        }


                        ?>
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-<?=$colores[rand(0,6)]?>"><img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=$nombre_habbo?>&direction=3&head_direction=3&gesture=sml&size=l&action="/></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"><?=$nombre_habbo?></span>
                                    <span class="info-box-text"><?=$tiempo?> de amistad</span>
                                    <span class="info-box-text">
                                        <a href="?p=chat&user=<?=$id_habbo?>"><i class="fas fa-envelope" data-toggle="tooltip" title="Enviar Mensaje"></i></a>
                                         &nbsp; 
                                        <a href="#" onclick="preguntarEliminar(<?=$r['id']?>)"><i data-toggle="tooltip" title="Eliminar" class="fas fa-trash"></i></a>
                                         &nbsp; 
                                        <a href="#"><i data-toggle="tooltip" title="Ver Perfil Social <?=$habbo_name?>" class="fas fa-eye"></i></a></span>
                                </div>
                            </div>
                        </div>      

                        <?php
                    }
                   
                }else{
                    echo '<div class="col-sm-12 col-md-6 col-lg-4 col-xl-3">No tienes amigos, ¡Encuentra uno!</i></div>';
                }
            ?>
           
           
           
        </div>
</div>


<script>
$("#search").keyup(function(){
          var query = $(this).val();
          if (query.length > 2) {
            $.ajax({
              url: 'ajax/buscar_amigos.php',
              method: 'POST',
              data: {query:query},
              success: function(data){
 
                $('#output').html(data);
                $('#output').css('display', 'block');
 
                // $("#search").focusout(function(){
                //     $('#output').css('display', 'none');
                // });
                // $("#search").focusin(function(){
                //     $('#output').css('display', 'block');
                // });
              },
              beforeSend:function(){
                $("#output").html('<img src="imgs/loading.gif" style="width: 50px;"/>');
              }
            });
          } else {
          $('#output').css('display', 'none');
        }
      });


      function prepararUsuario(id, nombre, rango){

$("#search").val('');
$("#output").fadeOut();

$("#ocultar").fadeOut();

  $("#kekox").css("opacity","1");

  $("#nombreHabbo").html(nombre);
  $("#user").val(id);

  $("#kekox").attr("src","imgs/loading.gif");

  $("#rango_habbo").html(rango);

  $("#cs").fadeIn();


              $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                  url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                  $("#kekox").attr("src",url);
              });

}

function preguntarEliminar(idu){

    Swal.fire({
            icon: 'warning',
          title: '¿Estás seguro de querer eliminar a esta persona? si lo haces no podras revertir esta acción',
          showDenyButton: false,
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#d33',
        }).then((result) => {
          if (result.isConfirmed) {
            window.location="?p=amistades&eliminar="+idu
          } 
        })

}
    </script>