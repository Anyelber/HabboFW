<?php
if(isset($eliminar)){
  $eliminar = clear($eliminar);

  if(is_founder_or_more(rol($_SESSION['id']))){
    $q = $pdo->prepare("DELETE FROM noticias WHERE id = :id");
    $q->execute([$eliminar]);

    alert("Se ha eliminado la noticia satisfactoriamente","./",1);
  }
}

if(isset($like)){
  $like = clear($like);

  $v = $pdo->prepare("SELECT count(id) as cantidad FROM likes WHERE id_noticia = :id AND id_user = :idu");
  $v->execute([
    $like,
    $_SESSION['id']
  ]);

  $r = $v->fetch();

  if($r['cantidad']>0){
    $q = $pdo->prepare("DELETE FROM likes WHERE id_noticia = :id AND id_user = :idu");
    $msj = "Has quitado tu like de esta noticia";
  }else{
    $q = $pdo->prepare("INSERT INTO likes (id_noticia, id_user) VALUES (:id, :idu)");
    $msj = "Has dado like de esta noticia";
  }
  $q->execute([
    $like,
    $_SESSION['id']
  ]);

  alert($msj,"./",1);

}

if(isset($pin)){
  if(is_manager_or_more(rol($_SESSION['id']))){

    $q = $pdo->prepare("UPDATE noticias SET pinned = 1 WHERE id = :id");
    $q->execute([
      $pin
    ]);
    alert("Se ha marcado como noticia fijada","?p=home",1);
  }else{
    alert("No tienes permisos","?p=home",0);
  }
}

if(isset($unpin)){
  if(is_manager_or_more(rol($_SESSION['id']))){

    $q = $pdo->prepare("UPDATE noticias SET pinned = 0 WHERE id = :id");
    $q->execute([
      $unpin
    ]);
    alert("Se ha quitado como noticia fijada","?p=home",1);
  }else{
    alert("No tienes permisos","?p=home",0);
  }
}

if(isset($escoger_ganador)){
  $escoger_ganador = clear($escoger_ganador);

  $participantes = array();

  $q = $pdo->prepare("SELECT * FROM likes WHERE id_noticia = :id");
  $q->execute([
    $escoger_ganador
  ]);

  while($r = $q->fetch()){
    array_push($participantes, $r['id_user']);
  }

  $ganador = $participantes[rand(0,(sizeof($participantes)-1))];

  $q = $pdo->prepare("UPDATE noticias SET id_ganador = :ganador WHERE id = :id");
  $q->execute([
    $ganador,
    $escoger_ganador
  ]);

  alert("Se ha escogido un ganador al azar","?p=home",1);

}

if(isset($reroll)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $reroll = clear($reroll);
    $q = $pdo->prepare("UPDATE noticias SET id_ganador = 0 WHERE id = :id");
    $q->execute([
      $reroll
    ]);

    alert("Se ha reiniciado el ganador de la noticia.","?p=home",1);
  }
}
?>
<style>
.noticia_container{

}

.title{
  background: #e33;
  padding-top:10px;
  padding-bottom:0px;
  border-radius: 10px 10px 10px 10px;
  color: white;
  padding-bottom:10px;
  position:relative;
}

.text{
  transition: 0.5s;
  position: relative;
  top: 10.1px;
  padding:15px;
  background: #eaeaea;
  color: #333;
  border-radius: 0px 0px 10px 10px;
}

  </style>

<br><br>
    <!-- Main content -->
    <section class="content">


    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" style="background: rgba(255,0,0,0.2);">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="3" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="4" class="active"></li>
  </ol>
  <div class="carousel-inner" >
    <!--<div class="carousel-item active" style="">
      <img class="carousel-image" src="./imgs_carrusel/1.png" style="width: 30%; margin-left:35%;">
    </div>
    <div class="carousel-item" style="">
      <img class="carousel-image" src="./imgs_carrusel/2.png" style="width: 30%; margin-left:35%;">
    </div>
    <div class="carousel-item" style="">
      <img class="carousel-image" src="./imgs_carrusel/3.png" style="width: 30%; margin-left:35%;">
    </div>
    <div class="carousel-item" style="">
      <img class="carousel-image" src="./imgs_carrusel/4.png" style="width: 30%; margin-left:35%;">
    </div>
    <div class="carousel-item" style="">
      <img class="carousel-image" src="./imgs_carrusel/5.png" style="width: 30%; margin-left:35%;">
    </div>
  </div>-->
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
    
    <!--<img src="/imgs/tpaga.png" style="width: 100%"/>-->

    

      <div class="container-fluid">
        <div class="row">
       
          <!-- left column -->
          <div class="col-md-12">
          <?php
        if(!working_on()){
          ?>
            <div style="background: red; padding: 20px; color: white;">
            <i class="fa fa-exclamation-triangle"></i> &nbsp; Actualmente los trabajos estan deshabilitados por motivos de paga
          </div><br><br>
          <?php
        }

        if(date("l") == "Sunday" || date("l") == "Wednesday"){
          if(date("H")>15 && date("H")<20){
      ?>

          <!-- <div style="background: #cccc00; padding: 20px; color: #333;">
            <i class="fa fa-exclamation-triangle"></i> &nbsp; Recuerda pedir que te reinicien si no alcanzaste paga y quieres trabajar antes del corte de paga<br>
            <small>[ El corte de paga se hacen 5 horas despues de la hora de paga ]</small>
          </div><br><br> -->

          <?php
          }
        }
          ?>


          <center>
            <?php
              if(status_pago($_SESSION['id']) == 1){
                ?>
                  <img src="./imgs/pagahome.jpg" style="width:100%"/>
                <?php
              }elseif(status_pago($_SESSION['id']) == 2){
                ?>
                  <img src="./imgs/pagabonihome.jpg" style="width:100%"/>
                <?php
              }
            ?> 
          </center><br><br>

          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Mi Progreso.</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">

                <div class="row d-flex justify-content-between">

                <div class="card card-secondary" style="width: 24%">
                      <div class="card-header">
                        <h3 class="card-title">Horas Trabajadas.</h3>
                      </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                        <div class="card-body">
                          <?=horas_trabajadas_profile($_SESSION['id'])?>
                        </div>
                        <!-- /.card-body -->

                    
                    </div>

                <div class="card card-secondary" style="width: 24%">
                      <div class="card-header">
                        <h3 class="card-title">Siguiente Ascenso.</h3>
                      </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                        <div class="card-body">
                        <?php
                            if(requiere_ascenso($_SESSION['id'])){
                              if(is_admin_or_more(rol($_SESSION['id']))){
                                echo "--";
                              }else{ 
                                echo "<span class='badge badge-success'>Ascenso disponible</span>";
                              }
                            }else{
                              echo "<span class='badge badge-danger'>".proximo_ascenso($_SESSION['id'])."</span>";
                            }
                          ?>
                        </div>
                        <!-- /.card-body -->

                    
                    </div>
                    
                    <div class="card card-secondary" style="width: 24%">
                      <div class="card-header">
                        <h3 class="card-title">Ascensos Realizados.</h3>
                      </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                        <div class="card-body">
                          <?=ascensos_realizados($_SESSION['id'])?>
                        </div>
                        <!-- /.card-body -->

                    
                    </div>

                    <div class="card card-secondary" style="width: 24%">
                      <div class="card-header">
                        <h3 class="card-title">Horas de Times Tomados.</h3>
                      </div>
                      <!-- /.card-header -->
                      <!-- form start -->
                        <div class="card-body">
                          <?=times_tomados($_SESSION['id'])?>
                        </div>
                        <!-- /.card-body -->

                    
                    </div>
                    
                    <?php
                      $qt = $pdo->prepare("SELECT * FROM times WHERE id_recibe = :id AND is_timing = 1");
                      $qt->execute([$_SESSION['id']]);

                      if($qt->rowCount()>0){
                        $rt = $qt->fetch();
                        ?>
                          <br><br>
                          <center style="width: 100%;">
                          <div style="display:inline-block;padding: 20px;border:1px solid #eaeaea;border-radius: 10px;">
                            <span style="color:red">Time En Curso</span><br>
                            <?php
                              $ta = strtotime(date("Y-m-d H:i:s"));
                              $t = strtotime($rt['created_at']);
                  
                              $datetimeObj1 = new DateTime(date("Y-m-d H:i:s"));
                              $datetimeObj2 = new DateTime($rt['created_at']);
                              $interval = $datetimeObj1->diff($datetimeObj2);
                              
                              if($interval->format('%a') > 0){
                              $hour1 = $interval->format('%a')*24;
                              }
                              if($interval->format('%h') > 0){
                              $hour2 = $interval->format('%h');
                              }

                              echo "<b>".$interval->h." Hora(s) ".$interval->i." Minuto(s)</b><br><br>";
                            ?>

                            Llevado por<br>
                            <?=keko_user($rt['id_dio'])?><br>
                            <b><?=nombre_habbo($rt['id_dio'])?></b>
                            </div>
                          </center>
                        <?php
                      }
                    ?>
                    <!-- /.card -->

                  </div>
                  </div>

                </div>
                <!-- /.card-body -->

             
            </div>
            <!-- /.card -->

          </div>
          <!--/.col (right) -->
        </div>

          <div style="background: url(imgs/bgadmins.jpg); position:relative; background-size: cover; background-position: center center; padding: 20px; border-radius: 10px;margin-left: 5px; margin-right: 5px; margin-bottom: 30px;">
              <center><h1 style="color:white;">Conoce a la Administración</h1></center>
              <br>
              <?php
             
                $actions = array("wav","sit","wlk");
                $gestures = array("nor","sml","spk","eyb","srp","agr","sad");

               
              ?>
               

              <?php
                $q = $pdo->prepare("SELECT habbo,rol FROM users WHERE rol >= 61 ORDER BY rol DESC, habbo ASC");
                $q->execute();

                while($r = $q->fetch()){

                  if($r['habbo'] != "alessandrinho10" && strtolower($r['habbo']) != "anyslehider_"){
                  
                    $randdir_temp = rand(2,4);
                    $action_temp = $actions[rand(0,2)];
                    $gesture_temp = $gestures[rand(0,6)];


                    if($action_temp == "sit"){
                      $randdir_temp = 3;
                    }

                    ?>
                    
              <div style="display: inline-block;justify-content:center;flex-wrap: wrap;width: 150px">
                      <center><img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=$r['habbo']?>&direction=<?=$randdir_temp?>&head_direction=<?=$randdir_temp?>&gesture=<?=$gesture_temp?>&size=m&action=<?=$action_temp?>&rand=<?=date("YmdHis").rand(0,99999)?>"/></center>
                      <br><center style="font-weight:bold;color: white;"><?=$r['habbo']?><br><?=nombre_rol($r['rol'])?></center>
                    </div>
                    <?php
                  }
                }
              ?>


              <center style="z-index: 2;">
                <div style="display:inline-block;padding: 10px; background: rgba(255,255,255,0.8);border-radius: 5px;margin-top: 30px; width: 30%">
              <a  href="<?=$instagram_url?>" target="_blank"><i data-toggle="tooltip" title="Instagram" class="fab fa-instagram" style="font-size:40px; margin:20px; color: red"></i></a>
                    <a  href="<?=$twitter_url?>" target="_blank"><i data-toggle="tooltip" title="Twitter" class="fab fa-twitter" style="font-size:40px; margin:20px; color: red"></i></a>
                    <a  href="<?=$discord_url?>" target="_blank"><i data-toggle="tooltip" title="Discord" class="fab fa-discord" style="font-size:40px; margin:20px; color: red"></i></a>
              </div>
                  </center>
                  
          </div>
          
          
         










        















        <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">
                  Noticias
                </h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">

                <?php
                  if(is_founder_or_more(rol($_SESSION['id']))){
                ?>
                <center>
                   <a href="?p=crear_noticia">
                    <button class="btn btn-danger"><i class="fas fa-file-alt"></i> &nbsp; Crear Noticia</button>
                  </a>
                  </center>
                <?php
                  }
                ?>

                <br>

                <?php
                  $q = $pdo->prepare("SELECT * FROM noticias ORDER BY pinned DESC, fecha DESC");
                  $q->execute();
                  $cont = 0;
                  while($r = $q->fetch()){
                    ?>
                      <div class="noticia_container">
                        <div class="title">
                          &nbsp; &nbsp; <?=$r['titulo']?><br>
                          <i onclick="$(this).parent().find('.text').toggle();" class="fas fa-arrow-circle-down" style="position: absolute; right: 10px; top: 15px; cursor: pointer;"></i>
  
                          <?php
                            if(is_manager_or_more(rol($_SESSION['id']))){
                              ?>
                              <a data-toggle="tooltip" title="Editar" href="?p=edit_noticia&id=<?=$r['id']?>" style="color:white"><i class="fas fa-edit" style="position: absolute; right: 65px; top: 15px; cursor: pointer;"></i></a>
                              
                              <?php
                              if($r['pinned'] == 1){     
                                ?>
                                <a data-toggle="tooltip" title="Unpin" href="?p=home&unpin=<?=$r['id']?>" style="color:white"><i class="fas fa-times" style="position: absolute; right: 40px; top: 15px; cursor: pointer;"></i></a>
                              <?php

                              }else{
                                ?>
                                <a data-toggle="tooltip" title="Pin" href="?p=home&pin=<?=$r['id']?>" style="color:white"><i class="fas fa-thumbtack" style="position: absolute; right: 40px; top: 15px; cursor: pointer;"></i></a>
                              <?php
                              }
                            }
                          ?>
                          <div class="text">
                          <?php
                              if(!empty($r['imagen']) && file_exists("./imgs_noticias/".$r['imagen'])){
                                ?>
                                <br>
                                  <center><img src="./imgs_noticias/<?=$r['imagen']?>" style="max-width: 80%"/></center><br><br>
                                <?php
                              }
                            ?>


                            <?=$r['texto']?><br>
                          

                            <br><br>
                            <span style="color: red; font-weight: bold;"><?=nombre_habbo($r['id_user'])?></span><br>
                            <?=keko_user($r['id_user'])?><br>
                            <small><?=date("d/m/Y h:i a", strtotime($r['fecha']))?></small><br><br>

                            <?php
                              $vl = $pdo->prepare("SELECT count(id) as cantidad FROM likes WHERE id_noticia = :id AND id_user = :idu");
                              $vl->execute([
                                $r['id'],
                                $_SESSION['id']
                              ]);

                              $rl = $vl->fetch();

                              if($rl['cantidad']>0){
                                $liked = true;
                              }else{
                                $liked = false;
                              }

                              if($liked){
                                echo "<span style='color:red'>";
                              }else{
                                echo "<span>";

                              }
                            ?>
                            <?=cant_likes($r['id'])?> Likes</span> 
                            <a href="?p=dashboard&p=home&like=<?=$r['id']?>">
                          <?php
                            if($liked){
                              ?>
                                <i class="fas fa-thumbs-down" data-toggle="tooltip" title="Quitar Me Gusta"></i>
                              <?php
                            }else{
                              ?>
                                
                                <i class="fas fa-thumbs-up" style="color: red" data-toggle="tooltip" title="Dar Me Gusta"></i>
                              <?php
                            }
                          ?></a>

                          <br>
                          <?=lista_likes($r['id'])?>
                          <br><br>

                          <?php
                            if($r['id_ganador']>0){
                          ?>

                          <center>

                            <div style="display:inline-block; background:rgba(255,0,0,0.2);border-radius: 10px; padding: 10px;">
                            ¡<b style="color:red"><?=nombre_habbo($r['id_ganador'])?></b> Es el ganador de este sorteo!<br>
                            <center><?=keko_user($r['id_ganador'],100,"wav",3)?></center>
                            <?php
                              if(is_admin_or_more(rol($_SESSION['id']))){
                                ?>
                                  <center><a href="?p=home&reroll=<?=$r['id']?>">Re-Roll</a></center>
                                <?php
                              }
                            ?>
                            </div>
                          </center>
                          <?php
                            }
                          ?>

                            <?php
                              if(is_founder_or_more(rol($_SESSION['id']))){
                                ?>
                                  <center>
                                    <a href="?p=home&eliminar=<?=$r['id']?>"><button class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar Noticia</button></a> 
                                    <?php
                                      if($r['id_ganador']==0){
                                    ?>
                                    &nbsp; <a href="?p=home&escoger_ganador=<?=$r['id']?>"><button class="btn btn-success"><i class="fas fa-gift"></i> Escoger Ganador</button></a>
                                    <?php
                                      }
                                    ?>
                                  </center>
                                <?php
                              }
                            ?>
                          </div>
                        </div>

                      </div>
                      <br>
                    <?php
                    $cont++;
                  }
                ?>
                  

</div>
                      

               
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!--/.col (right) -->
        </div>
































          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/postscribe/2.0.8/postscribe.min.js"></script>

    <div class="modal fade" id="likes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Likes de esta noticia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="res">

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<script>
$('.carousel').carousel()

function ver_likes(id){ 
        var parametros = {
          "id" : id
        };
        $.ajax({
          data : parametros,
          type : "post",
          url : "./ajax/lista_likes.php",
          success:function(response){
            $("#res").html(response);
          },
          beforeSend:function(){
            $("#res").html('<center><img src="./imgs/loading.gif" style="width:100px;"/></center>');
          }
        })

}
  </script>