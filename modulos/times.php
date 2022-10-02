
<?php
if(isset($enviar)){

    if(!working_on()){
      alert("Lo sentimos actualmente el trabajo esta deshabilitado","./",0);
    }

    $user = clear($user);
    $codigo = clear($codigo);

    if(verificar_codigo($user, $codigo)){
      $q = $pdo->prepare("SELECT habbo, especial FROM users WHERE id = :id");
      $q->execute([$user]);
      
      $ru = $q->fetch();

      // if(!esta_conectado($user)){
      //   alert("Este habbo no esta conectado o su perfil no es publico","",0);
      // }

      // if(!esta_conectado($_SESSION['id'])){
      //   alert("Debes estar conectado en habbo para poder ascender","",0);
      // }

      if(rol($user)<5){
        alert("No puedes tomar times a agentes / oficinistas / aspirantes","",0);
      }

      if(rol($_SESSION['id'])<54){
        if($ru['especial'] == 1){
          alert("No puedes tomar times especiales","",0);
        }
      }

      if(rol($user)>=47){
        alert("No puedes tomar time a un elite o superior","",0);
      }

      if(rol($_SESSION['id'])<33){
        alert("No puedes tomar times","",0);
      }
      
      if(!tiene_poder_time($_SESSION['id'], $user)){
        alert("No tienes permiso para tomarle time a este personaje","",0);
      }

      if($ru['especial'] == 1){
        if(rol($user)>46){
          alert("No puedes tomarle time a esta persona","",0);
        }
      }else{
        if(rol($user)>25){
          alert("No puedes tomarle time a esta persona","",0);
        }
      }

      

      if(es_clon($_SESSION['id'], $user)){
        alert("Se ha detectado que intentas ejecutar acciones con 2 personajes en la misma casa, esto esta PROHIBIDO, ten cuidado o podrias terminar baneado \ degradado \ despedido de ".$habbo_name,"",0);
      }

      $date = date("Y-m-d H:i:s");

      if(!is_timing_user($user)){
        $q = $pdo->prepare("INSERT INTO `times` (id_dio, id_recibe, is_timing, created_at) VALUES (:id_dio, :id_recibe, 1, :f)");
        $q->execute([
            $_SESSION['id'],
            $user,
            $date
        ]);

        
        $nc = generar_codigo();
        $qf = $pdo->prepare("UPDATE users SET codigo = :nc WHERE id = :id");
        $qf->execute([$nc, $user]);

        alert("Has empezado el time de ".$ru['habbo']." Satisfactoriamente.","",1);

      }else{
        alert("Otra persona esta llevando el time de este habbo.","",0);
      }
    }else{
      alert("El codigo de seguridad del Usuario es incorrecto","",0);
    }

}


if(isset($parar)){
  $parar = clear($parar);

  $date = date("Y-m-d H:i:s");

  $q = $pdo->prepare("UPDATE `times` SET  is_timing = 0, valid_timer = 1, ended_at = :f WHERE id = :id");
  $q->execute([$date, $parar]);

  $qs = $pdo->prepare("SELECT id_recibe FROM `times` WHERE id = :id");
  $qs->execute([$parar]);

  $r = $qs->fetch();

  $user = $r['id_recibe'];

  $nc = generar_codigo();
  $qf = $pdo->prepare("UPDATE users SET codigo = :nc WHERE id = :id");
  $qf->execute([$nc, $user]);
  

  
    alert("Has parado el time satisfactoriamente.","?p=times",1);


}

if(isset($parar_admin)){

  $parar_admin = clear($parar_admin);

  $date = date("Y-m-d H:i:s");

  $q = $pdo->prepare("UPDATE `times` SET  is_timing = 0, valid_timer = 0, ended_at = :f WHERE id = :id");
  $q->execute([$date, $parar_admin]);

  $qs = $pdo->prepare("SELECT id_recibe FROM `times` WHERE id = :id");
  $qs->execute([$parar_admin]);

  $r = $qs->fetch();

  $user = $r['id_recibe'];

  $nc = generar_codigo();
  $qf = $pdo->prepare("UPDATE users SET codigo = :nc WHERE id = :id");
  $qf->execute([$nc, $user]);
  

  
    alert("Has parado el time satisfactoriamente. ¡Este time no será valido para la persona que empezó el time!","?p=times",1);

}

if(isset($eliminar_time)){
  if(is_manager_or_more(rol($_SESSION['id']))){
    $eliminar_time = clear($eliminar_time);
    $q = $pdo->prepare("DELETE FROM times WHERE id = :id");
    $q->execute([
      $eliminar_time
    ]);
    alert("Se ha eliminado el time.","?p=times",1);
  }else{
    alert("No tienes permisos para realizar esta accion","?p=times",0);
  }
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Administrar Time</h3>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row " style="display: flex; justify-content: center;">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Información del time</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuarios Pendientes por Tomar time</label>

                    <div>
                      <input style="width: 50%;" class="form-control" id="search" placeholder="Busca al usuario..."/>
                      <div id="output" style="display:none;"></div>
                    </div>


                    <input type="text" style="position:fixed;left: -1000000px; top: -100000px" name="user" value="" required id="user"/>

                  </div>

                  <i id="ocultar">Seleccione un personaje a tomar Time<br></i>

                  <img src="./imgs/loading.gif" id="kekox" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo"></span>
                  <input type="text" maxlength="5" class="form-control" style="width: 40%; color:red; font-weight: bold; text-align:center; display:none;" id="cs" name="codigo" placeholder="Codigo de Seguridad del Usuario"/>
                
                
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" name="enviar" class="btn btn-danger">Tomar Time</button>
                </div>
</center>
              </form>
            </div>
            <!-- /.card -->

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->

<br><br>
<h3>Lista de times en curso</h3><br>
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dt" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dio Time</th>
                        <th>Recibio Time</th>
                        <th>Fecha y Hora Inicio</th>
                        <th>Tiempo Transcurrido</th>
                        <th>Tiempo Total Trabajado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?=tabla_times_curso()?>
                </tbody>
            </table>
            </div>
        </div>


        <h3>Lista de times Realizados</h3><br>
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dt" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dio Time</th>
                        <th>Recibio Time</th>
                        <th>Fecha y Hora Inicio</th>
                        <th>Fecha y Hora Culminacion</th>
                        <th>Tiempo Transcurrido</th>
                        <th>Tiempo Total Trabajado</th>
                        <th>Status</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?=tabla_times_realizados()?>
                </tbody>
            </table>
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

function pararTime(id,habbo){
  Swal.fire({
    icon: "warning",
  title: '¿Estas seguro de parar el time de <b style="color:red">'+habbo+'</b>?',
  showCancelButton: true,
  confirmButtonText: 'Si',
  cancelButtonText: 'No',
}).then((result) => {
  if (result.isConfirmed) {
    window.location="?p=times&parar="+id;
  }
})
}

function pararTimeAdmin(id,habbo){
  Swal.fire({
    icon: "warning",
  title: '¿Estas seguro de parar el time de <b style="color:red">'+habbo+'</b> e invalidarlo para el que asignó el time?',
  showCancelButton: true,
  confirmButtonText: 'Si',
  cancelButtonText: 'No',
}).then((result) => {
  if (result.isConfirmed) {
    window.location="?p=times&parar_admin="+id;
  }
})
}


$(document).ready(function(){
  // $("#myInput").on("keyup", function() {
  //   var value = $(this).val().toLowerCase();
  //   $(".dropdown-menu li").filter(function() {
  //     $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
  //   });
  // });

  $("#search").keyup(function(){
          var query = $(this).val();
          if (query.length > 2) {
            $.ajax({
              url: 'ajax/time.php',
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
});

function prepararUsuario(id, nombre){

  
  $("#search").val('');
  $("#output").fadeOut();

  $("#ocultar").fadeOut();
  
  $("#kekox").attr("src","imgs/loading.gif");

  $("#cs").fadeIn();

    $("#kekox").css("opacity","1");

    $("#nombreHabbo").html(nombre);
    $("#user").val(id);


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox").attr("src",url);
                });

}

function borrarTime(id){
  Swal.fire({
    icon: "warning",
    title: '¿Estas seguro de Eliminar definitivamente este time time?',
    showCancelButton: true,
    confirmButtonText: 'Si',
    cancelButtonText: 'No',
  }).then((result) => {
    if (result.isConfirmed) {
      window.location="?p=times&eliminar_time="+id;
    }
  })
}
</script>