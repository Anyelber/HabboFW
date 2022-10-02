<?php

include "./configs/configs.php";
include "./configs/functions.php";
require_once('anti_ddos/start.php'); 
check_conectado();
check_saves();

if(isset($guardarFirma)){
  $firma = clear($firma);

  if (!ctype_alnum($firma)) {
    echo "<center><br><br><br><br><br><i style='color:red'>La firma solo puede contener letras y numeros</i> <a href=''>Regresar</a></center>";
    die();
  }

  if(strlen($firma)!=3){
    echo "<center><br><br><br><br><br><i style='color:red'>La firma solo puede tener 3 caracteres, minimo 3, maximo 3</i> <a href=''>Regresar</a></center>";
    die();

  }

  $check = $pdo->prepare("SELECT id FROM users WHERE firma = :firma");
  $check->execute([
    $firma
  ]);

  if($check->rowCount()>0){
    echo "<center><br><br><br><br><br><i style='color:red'>Esta firma ya esta en uso, porfavor vuelvelo a intentar</i> <a href=''>Regresar</a></center>";
    die();
  }

  $firma = strtoupper($firma);

  $q = $pdo->prepare("UPDATE users SET firma = :firma WHERE id = :id");
  $q->execute([
    $firma,
    $_SESSION['id']
  ]);

  alert("Se ha creado tu firma satisfactoriamente, ahora puedes continuar usando el foro web.","./",1);
}

if(esta_despedido($_SESSION['id'])){
  echo "<div style='height: 100vh; font-family: helvetica; display: flex;justify-content: center; align-items: center; flex-wrap: wrap;'><img src='https://www.habbo.es/habbo-imaging/avatarimage?user=".nombre_habbo($_SESSION['id'])."&direction=3&head_direction=3&gesture=sad&size=l&action=wav'/><div style='width:100%;'>&nbsp;</div>Lo sentimos has sido despedido de ".$habbo_name." &nbsp; <a href='login.php'>Regresar</a></div>";
  session_destroy();
  die();
}

if(in_array($_SERVER['REMOTE_ADDR'], $black_list)){
  echo "<div style='height: 100vh; font-family: helvetica; display: flex;justify-content: center; align-items: center; flex-wrap: wrap;'><img src='https://www.habbo.es/habbo-imaging/avatarimage?user=".nombre_habbo($_SESSION['id'])."&direction=3&head_direction=3&gesture=sad&size=l&action=wav'/><div style='width:100%;'>&nbsp;</div>Lo sentimos has sido baneado permanentemente de ".$habbo_name." &nbsp; <a href='login.php'>Regresar</a></div>";
  session_destroy();
  die();
}

if(!isset($p)){
  $p = "home";
}

if(isset($turn_on_work) && $turn_on_work == 1){
  if(is_founder_or_more(rol($_SESSION['id']))){
    $q = $pdo->prepare("UPDATE configs SET valor = 1 WHERE tipo = 'work'");
    $q->execute();
    alert("Se ha activado el trabajo nuevamente","./",1);
  }
}

if(isset($turn_off_work) && $turn_off_work == 1){
  if(is_founder_or_more(rol($_SESSION['id']))){
    $q = $pdo->prepare("UPDATE configs SET valor = 0 WHERE tipo = 'work'");
    $q->execute();

    $fecha_actual = date("Y-m-d H:i:s");

    $q2 = $pdo->prepare("UPDATE times SET is_timing = 0, valid_timer = 1, ended_at = :fecha_actual WHERE is_timing = 1");
    $q2->execute([$fecha_actual]);

    $qu = $pdo->prepare("SELECT id FROM users");
    $qu->execute();

    while($ru = $qu->fetch()){
      if(status_pago($ru['id']) == 0){
        $fecha = date("Y-m-d H:i:s");

        $qr = $pdo->prepare("INSERT INTO pagas (id_dio, id_recibe, ascensos, times, horas, fecha, status) VALUES (:idd, :idr, 0, 0, 0, :fecha, 0)");
        $qr->execute([
            $_SESSION['id'],
            $ru['id'],
            $fecha
        ]);
        reiniciar_user($ru['id']);
      }
    }

    alert("Se ha deshabilitado el trabajo y se han reiniciado los usuarios que no cumplieron paga.","./",1);
  }
}


if($p == "chat"){
  

  if(isset($user)){
    $q = $pdo->prepare("UPDATE chat SET status = 1 WHERE sender = :ids AND receiver = :idu");
    $q->execute([
      $user,
      $_SESSION['id']
    ]);
  }
}
?>
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=$habbo_name?> Administrativo</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <link rel="stylesheet" href="searchers.css">
  <meta name="description" contet="<?=$habbo_name?> es la mejor agencia de Habbo que podras encontrar, disfruta de nuestras instalaciones y de nuestro foro web"/>
	<meta name="keyword" contet="anyslehider,habbo,foro web,web"/>
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<style>
  .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
    background-color: #dc3545 !important;
    color: #fff;
}



</style>

<body class="hold-transition sidebar-mini">

<?php
if(is_tecnico_or_more(rol($_SESSION['id'])) && !tiene_firma($_SESSION['id'])){

  ?>
    <form method="post" action="">
      <div style="height: 100vh; width: 100%; display: flex;justify-content:center; align-items:center;">
      <div style="text-align:center;;">
        <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=nombre_habbo($_SESSION['id'])?>&direction=3&head_direction=3&gesture=sml&size=l&action=wav"/><br><br><br>
        <p style="font-size: 20px; font-weight: bold;"><b style="font-size: 30px;">¡Hola!</b><br>Has avanzado hasta Tecnico y aun no tienes una firma asi que debes definirla justo ahora</p>
        <div class="form-group">
          <input class="form-control" id="firma" name="firma" placeholder="Se paciente y escribe una firma que esté disponible, si no esta disponible intentalo de nuevo."/>
        </div>
        <div class="form-group">
          <button type="submit" name="guardarFirma" class="btn btn-success"><i class="fas fa-check"></i> Establecer mi firma</button>
        </div>
      </div>
      <div>
    </form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
 $('#firma').blur(function() {
    
  var firma = $("#firma").val();
  var parametros = {
    "firma" : firma
  };

  $.ajax({
    data : parametros,
    url : "./ajax/check_firma.php",
    success:function(response){
      if(response == 0){
        Swal.fire(
          '¡Esta firma ya esta en uso!',
          'Trata con otra diferente',
          'danger'
        )
        $("#firma").val('');
        
      }
    }
  });
 });
  </script>
  <?php
  die();
}
?>
<div class="wrapper">


  

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <ul class="navbar-nav" style="margin-left: 20px;">
      <li class="nav-item">
      <!--<script src="//myradiostream.com/embed/alessandrinho10" ></script>-->
      <!--iframe src="https://s20.myradiostream.com/16730/;?type=http&nocache=1638909399" allow="autoplay" gesture="media"></iframe>-->
        <!--<iframe allowtransparency="true" allow="autoplay" id="audio" style="    height: 161px;
    border: none;
    width: 400px;
    position: absolute;
    top: -105px;
    background:none;
    filter: invert(1)"></iframe>-->

<?php  

$online = "Online"; // Displays when stream is online
$offline['server'] = "Offline (No Server Connection)"; // Displays when server is offline
$offline['source'] = "Offline (No Source)"; // Displays when server is online with no source
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $my_radio_stream_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$d = curl_exec($ch);

curl_close($ch);

$d = str_replace('</body></html>', "", $d);
$split = explode(',', $d);
if (empty($d)) {
  $status = $offline['server'];
  $fstatus = 0;
  ?>
  <div id="radio" onclick="window.open('<?=$radio_url?>','popUpWindow','height=500,width=400,left=100,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes')" style="cursor:pointer;background: #b00; background-image: url(./imgs/waves.gif); border-radius: 10px; padding-left: 30px;padding-right:30px;color: white; font-weight: bold; padding-top: 10px; padding-bottom: 10px;">RADIO ON &nbsp; <small>Click Aqui</small></div>
    <!--<div id="radio" style="cursor:pointer;background: #333; border-radius: 10px; padding-left: 30px;padding-right:30px;color: white; font-weight: bold; padding-top: 10px; padding-bottom: 10px;">RADIO OFF</div>-->
   <?php
} else { 
	if ($split[1] == "0") {
    $status = $offline['source'];
    $fstatus = 0;
    ?>
    <div id="radio" onclick="window.open('<?=$radio_url?>','popUpWindow','height=500,width=400,left=100,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes')" style="cursor:pointer;background: #b00; background-image: url(./imgs/waves.gif); border-radius: 10px; padding-left: 30px;padding-right:30px;color: white; font-weight: bold; padding-top: 10px; padding-bottom: 10px;">RADIO ON &nbsp; <small>Click Aqui</small></div>
      <!--<div id="radio" style="cursor:pointer;background: #333; border-radius: 10px; padding-left: 30px;padding-right:30px;color: white; font-weight: bold; padding-top: 10px; padding-bottom: 10px;">RADIO OFF</div>-->
   <?php
	    } else {    
        $status = $online; 
        $fstatus = 1;   
    ?>
      <div id="radio" onclick="window.open('<?=$radio_url?>','popUpWindow','height=500,width=400,left=100,top=100,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes')" style="cursor:pointer;background: #b00; background-image: url(./imgs/waves.gif); border-radius: 10px; padding-left: 30px;padding-right:30px;color: white; font-weight: bold; padding-top: 10px; padding-bottom: 10px;">RADIO ON &nbsp; <small>Click Aqui</small></div>
    <?php
	}
}
?>

<?php

$fstatus = 1;

if($fstatus == 1){
  ?>
  <style>
    #radio{
      animation: mymove 2s;
      animation-iteration-count: infinite;
    }

    @keyframes mymove {
      0% { transform: scale(1)}
      50% {transform: scale(1.3)}
      100% {transform: scale(1)}
    }
  </style>
  <?php
}
?>

     
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto" >

    <?php
    if(is_founder_or_more(rol($_SESSION['id']))){
      if(date("l") == "Sunday" || date("l") == "Wednesday"){
        if(date("H")>=14 && date("H")<=15){
    ?>
      <li class="nav-item" style="margin-right: 20px;">
        <?php

        if(working_on()){
          ?>
            <a onclick="turn_off()" class="btn btn-danger">Deshabilitar Trabajo</a>
          <?php

        }else{
          ?>
            <a onclick="turn_on()" class="btn btn-success">Habilitar Trabajo</a>
          <?php
          
        }

      ?>

      </li>
      <?php
        }else{
          if(!working_on()){
            ?>
              <a onclick="turn_on()" class="btn btn-success">Habilitar Trabajo</a>
            <?php
          }
        }
      }
    }
      ?>

    <li class="nav-item">
        <div id="time_actual" style="display:flex; align-items:center;font-weight:bold;height: 100%;margin-right: 25px;"><?=date("h:i a")?></div>
      </li>
      

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <?php
            if(cantidad_mensajes_no_leidos($_SESSION['id'])>0){
              ?>
                <span class="badge badge-danger navbar-badge"><?=cantidad_mensajes_no_leidos($_SESSION['id'])?></span>
              <?php
            }
          ?>
          <!--<span class="badge badge-danger navbar-badge">3</span>-->
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <?php
            $qc = $pdo->prepare("SELECT sender, text, fecha FROM chat WHERE receiver = :idu AND status = 0");
            $qc->execute([
              $_SESSION['id']
            ]);

            $cont_mensajes = 0;

            while($rc = $qc->fetch()){
              if(!is_null($rc['sender'])){
                $cont_mensajes++;
              ?>
                 <a href="?p=chat&user=<?=$rc['sender']?>" class="dropdown-item">
                    <div class="media">
                      <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=nombre_habbo($rc['sender'])?>&direction=3&head_direction=3&gesture=sml&size=l&action=&headonly=true" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                      <div class="media-body">
                        <h3 class="dropdown-item-title">
                          <?=nombre_habbo($sender)?>
                          <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                        </h3>
                        <p class="text-sm">
                          <?php 
                          if(strlen($rc['text'])>40){
                            for($a=0;$a<40;$a++){
                              echo $rc['text'][$a];
                            }
                            echo " ...";
                          }else{
                            echo $rc['text'];
                          }
                          ?>
                        </p>

                        <?php
                          $fecha1 = new DateTime($rc['fecha']);
                          $fecha2 = new DateTime(date("Y-m-d H:i:s"));

                          $dif = $fecha1->diff($fecha2);

                          if($dif->s >= 0 ){
                            if($dif->s == 1 || $dif->s == 0){
                              $tiempo = "Segundo";
                              $cant_tiempo = 1;
                            }else{
                              $tiempo = "Segundos";
                              $cant_tiempo = $dif->s;
                            }
                          }

                          if($dif->i > 0 ){
                            if($dif->i == 1){
                              $tiempo = "Minuto";
                              $cant_tiempo = 1;
                            }else{
                              $tiempo = "Minutos";
                              $cant_tiempo = $dif->i;
                            }
                          }

                          if($dif->h > 0 ){
                            if($dif->h == 1){
                              $tiempo = "Hora";
                              $cant_tiempo = 1;
                            }else{
                              $tiempo = "Horas";
                              $cant_tiempo = $dif->h;
                            }
                          }

                          if($dif->m > 0 ){
                            if($dif->m == 1){
                              $tiempo = "Mes";
                              $cant_tiempo = 1;
                            }else{
                              $tiempo = "Meses";
                              $cant_tiempo = $dif->m;
                            }
                          }

                          if($dif->y > 0 ){
                            if($dif->y == 1 ){
                              $tiempo = "Año";
                              $cant_tiempo = 1;
                            }else{
                              $tiempo = "Años";
                              $cant_tiempo = $dif->y;
                            }
                          }
                        ?>

                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> Hace <?=$cant_tiempo?> <?=$tiempo?></p>
                      </div>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
              <?php
              }
            }
          ?>
         
         
          
          <div class="dropdown-divider"></div>
          <a href="?p=chat" class="dropdown-item dropdown-footer">Ver todos los Mensajes.</a>
        </div>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <?php
            if(cantidad_solicitudes_amistad_pendiente($_SESSION['id'])>0){
              ?>
                <span class="badge badge-danger navbar-badge"><?=cantidad_solicitudes_amistad_pendiente($_SESSION['id'])?></span>
              <?php
            }
          ?>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-header"><?=cantidad_solicitudes_amistad_pendiente($_SESSION['id'])?> Notificaciones</span>
          <div class="dropdown-divider"></div>

          <?php
            if(cantidad_solicitudes_amistad_pendiente($_SESSION['id'])>0){
              ?>
                <a href="?p=amistades" class="dropdown-item">
                  <i class="fas fa-user-check mr-2"></i> <?=cantidad_solicitudes_amistad_pendiente($_SESSION['id'])?>nuevas solicitudes de amistad
                  
                </a>
              <?php
            }
          ?>
          <!--<a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>-->
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">Ver todas las Notificaciones.</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="./" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><?=$habbo_name?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 " style="text-align:center">
        <img src="" id="keko" style="width: 100px; height:auto;"/><br>
        <?=keko_user($_SESSION['id'],120)?><br>
        <div class="info">
          <b style="text-transform: uppercase;color: white;"><?=nombre_habbo($_SESSION['id'])?></b><br>
          <span style="color: #aeaeae"><?=nombre_rol_usuario($_SESSION['id'])?></span><br>
          <small style="color: #aaa">CS:</small> <b style="color: red" data-toggle="tooltip" title="Codigo de Seguridad"><?=codigo($_SESSION['id'])?></b> <i style="color: red;margin-left:5px;cursor:pointer;" data-toggle="tooltip" title="Copiar Codigo" onclick="copiarcv('<?=codigo($_SESSION['id'])?>')" class="fa fa-copy"></i>
          <br><br>
          <b style="color:white">Mis Placas</b>
          <br>
          <div>
            <?php
            if(status_pago($_SESSION['id']) >= 1){
              ?>
                <img src="./imgs/paga.png"/>
              <?php
            }

            if(status_pago($_SESSION['id']) == 2){
              ?>
                <img src="./imgs/boni.png"/>
              <?php 
            }

            if(tiene_save($_SESSION['id'])){
              ?>
                <img src="./imgs/save.png"/>
              <?php
            }

            if(tiene_fila($_SESSION['id'])){
              ?>
                <img src="./imgs/fila.png"/>
              <?php
            }

            if(tiene_vip($_SESSION['id'])){
              ?>
                <img src="./imgs/vip.png"/>
              <?php
            }

            if(tiene_pase($_SESSION['id'])){
              ?>
                <img src="./imgs/pase.png"/>
              <?php
            }
            ?>
            <!--<img src="./imgs/fila.png"/>-->
            <!--<img src="./imgs/vip.png"/>-->
          </div>
        </div><br>
        <div class="info">
        </div>
      </div>

      <!-- SidebarSearch Form -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

          <li class="nav-item">
            <a href="./dashboard.php" class="nav-link <?php if($p == "" || $p == "home") echo "active"; ?>">
            &nbsp; 
            <i class="fab fa-angellist"></i>
              <p>
                &nbsp; Espacio <?=$habbo_name?>
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="?p=amistades" class="nav-link <?php if($p == "amistades") echo "active"; ?>">
            &nbsp; 
            <i class="fas fa-user-check"></i>
              <p>
                &nbsp; Mis Amigos
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="?p=chat" class="nav-link <?php if($p == "chat") echo "active"; ?>">
            &nbsp; 
            <i class="fas fa-envelope"></i>
              <p>
                &nbsp; Chat
                <!-- <span class="right badge badge-danger">New</span> -->
              </p>
            </a>
          </li>

          <li class="nav-item">
          <a href="?p=compra_rangos" class="nav-link <?php if($p == "compra_rangos") echo "active"; ?>">
            &nbsp; 
            <i class="fas fa-money-bill"></i>
              <p>
                &nbsp; Compra Rangos
              </p>
            </a>
          </li>

          <li class="nav-item">
          <a href="?p=social_coca" class="nav-link <?php if($p == "social_coca") echo "active"; ?>">
            &nbsp; 
            <i class="fas fa-globe-americas"></i>
              <p>
                &nbsp;Social <?=$habbo_name?>
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="?p=info" class="nav-link <?php if($p == "info") echo "active"; ?>">
            &nbsp; 
            <i class="fas fa-info-circle"></i>
              <p>
                &nbsp; Información <?=$habbo_name?>
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

        
          <?php
            if(is_admin_or_more(rol($_SESSION['id']))){
          ?>
          <li class="nav-item">
            <a href="?p=estadisticas" class="nav-link <?php if($p == "estadisticas") echo "active"; ?>">
            <i class="nav-icon fas fa-trophy"></i>
              <p>
                Mejores Trabajadores
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>
          <?php
            }
          ?>

          
          <!--
          <li class="nav-item">
            <a href="?p=roles" class="nav-link <?php if($p == "roles") echo "active"; ?>"">
            <i class="nav-icon fas fa-hat-cowboy"></i>
              <p>
                Roles
                
              </p>
            </a>
          </li>
          -->

          <li class="nav-item">
            <a href="?p=capacitacion" class="nav-link <?php if($p == "capacitacion") echo "active"; ?>">
            <i class="nav-icon fas fa-play"></i>
              <p>
                Tutoriales / Capacitación
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

          <?php
          if(rol($_SESSION['id'])>=12){
          ?>
          
          <li class="nav-item">
            <a href="?p=usuarios" class="nav-link <?php if($p == "usuarios") echo "active"; ?>">
            <i class="nav-icon fas fa-users"></i>
              <p>
                Usuarios
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

          <?php
            if(!es_especial($_SESSION['id']) && rol($_SESSION['id'])>=26){
          ?>

          <li class="nav-item">
            <a href="?p=ascensos" class="nav-link <?php if($p == "ascensos") echo "active"; ?>">
            <i class="nav-icon fas fa-check-double "></i>
              <p>
                Ascensos
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

          <?php
            }
          ?>

          
          <?php
          if(rol($_SESSION['id'])>=33 && !es_especial($_SESSION['id'])){
          ?>

          <li class="nav-item">
            <a href="?p=times" class="nav-link <?php if($p == "times") echo "active"; ?>">
            <i class="nav-icon fas fa-tachometer-alt "></i>
              <p>
                Times
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>


          <?php
          
          }
          }
            if(is_admin_or_more(rol($_SESSION['id']))){
          ?>

        <li class="nav-item">
          <a href="?p=verificar_pagos" class="nav-link <?php if($p == "verificar_pagos") echo "active"; ?>">
          <i class="nav-icon fas fa-money-bill-wave "></i>
            <p>
              Verificar Pagos
              <!--<span class="right badge badge-danger">New</span>-->
            </p>
          </a>
        </li>

                  

        <li class="nav-item">
          <a href="?p=pagos" class="nav-link <?php if($p == "pagos") echo "active"; ?>">
          <i class="nav-icon fas fa-hand-holding-usd"></i>
            <p>
              Pagos
              <!--<span class="right badge badge-danger">New</span>-->
            </p>
          </a>
        </li>

        <?php

            }
        if(is_admin_or_more(rol($_SESSION['id']))){
          ?>

          <li class="nav-item">
            <a href="?p=ascenso_admin" class="nav-link <?php if($p == "ascenso_admin") echo "active"; ?>">
            <i class="nav-icon fas fa-check"></i>
              <p>
                Ascenso Libre
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="?p=time_admin" class="nav-link <?php if($p == "time_admin") echo "active"; ?>">
            <i class="nav-icon fas fa-clock"></i>
              <p>
                Time Libre
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="?p=degrado" class="nav-link <?php if($p == "degrado") echo "active"; ?>">
            <i class="nav-icon fas fa-user-slash"></i>
              <p>
                Degrado / Despido
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="?p=atencion" class="nav-link <?php if($p == "atencion") echo "active"; ?>">
            <i class="nav-icon fas fa-ban"></i>
              <p>
                Llamados de atención
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="?p=clones" class="nav-link <?php if($p == "clones") echo "active"; ?>">
            <i class="nav-icon fas fa-users"></i>
              <p>
                Detector de Clones
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

          

          <li class="nav-item">
            <a href="?p=membresias" class="nav-link <?php if($p == "membresias") echo "active"; ?>">
            <i class="nav-icon fas fa-coins"></i>
              <p>
                Membresias
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>

                    

          <li class="nav-item">
            <a href="?p=peticiones" class="nav-link <?php if($p == "peticiones") echo "active"; ?>">
            <i class="nav-icon fas fa-question"></i>
              <p>
                Peticiones FW.
              </p>
            </a>
          </li>

        <?php
        }
        ?>

        




          <li class="nav-item">
            <a href="?p=sugerencias" class="nav-link <?php if($p == "sugerencias") echo "active"; ?>">
            <i class="nav-icon fas fa-list"></i>
              <p>
                Quejas / Sugerencias
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>




        <li class="nav-item">
            <a href="?p=perfil" class="nav-link <?php if($p == "perfil") echo "active"; ?>">
            <i class="nav-icon fas fa-user"></i>
              <p>
                Mi Perfil
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>
          

          

          <li class="nav-item">
            <a href="?p=salir" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>
                Salir
                <!--<span class="right badge badge-danger">New</span>-->
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
<!-- 
  <div style="position:fixed;width: 100%; height: 100vh; background: rgba(255,255,255,0.5);">

  </div> -->

  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
   <?php
    if(file_exists("./modulos/".$p.".php")){
      include "./modulos/".$p.".php";
    }else{
      echo "<br> &nbsp; &nbsp;  &nbsp; &nbsp; Modulo no existente";
    }
   ?>
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <!-- Default to the left -->
    <strong>Copyright &copy; 2021 <a style="color: #dc3545" href="https://www.instagram.com/anyslehider" target="_blank">AnySlehider</a>.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>

<input style="position:fixed;left:-100000px;right:10000px;" id="temp"/>

<?php
check_msg();
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>

  
function copiarcv(cv) {
  var $temp = $("#temp");
  $temp.val(cv).select();
  document.execCommand("copy");
  $temp.remove();

  Swal.fire(
    '',
    'Codigo de Seguridad Copiado',
    'success'
  );

}

  $('.dt').each(function(){
      $(this).DataTable({
        "order": [[ 0, "desc" ]]
      });
  });

  $(document).ready(function(){
    $("body").focus();
    $("body").click();
    $("[data-toggle='tooltip']").each(function(){
      $(this).tooltip();
    });
    $("input").each(function(){
      $(this).attr("autocomplete","off");
    });

    //var myaudio = document.getElementById("audio").autoplay = true;
      

    
  });
  



  </script>

<?php
$serverTime =  time() * 1000 ;
$timezone = date('O');
?>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
  $(document).ready(function() {
    setInterval(timestamp, 30000);
});

function timestamp() {
    $.ajax({
        url: './ajax/timestamp.php',
        success: function(data) {
            $('#time_actual').html(data);
        },
    });
}

function turn_off(){
  Swal.fire({
    icon: "warning",
  title: '¿Estas seguro de Deshabilitar los modulos de trabajo?',
  text: '!Ten en cuenta que se reiniciaran las personas que no pudieron cumplir pago y no se le permitira trabajar a nadie!',
  showCancelButton: true,
  confirmButtonText: 'Si',
  cancelButtonText: 'No',
}).then((result) => {
  if (result.isConfirmed) {
    window.location="?turn_off_work=1";
  }
})
}

function turn_on(){
  Swal.fire({
    icon: "warning",
  title: '¿Estas seguro de Habilitar los modulos de trabajo?',
  showCancelButton: true,
  confirmButtonText: 'Si',
  cancelButtonText: 'No',
}).then((result) => {
  if (result.isConfirmed) {
    window.location="?turn_on_work=1";
  }
})

}




</script>