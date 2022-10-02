
<?php
if(isset($enviar)){

  if(!working_on()){
    alert("Lo sentimos actualmente el trabajo esta deshabilitado","./",0);
  }
    $user = clear($user);
    $codigo = clear($codigo);

    if(verificar_codigo($user,$codigo)){

      // if(!esta_conectado($user)){
      //   alert("Este habbo no esta conectado o su perfil no es publico","",0);
      // }

      // if(!esta_conectado($_SESSION['id'])){
      //   alert("Debes estar conectado en habbo para poder ascender","",0);
      // }

      if($user == $_SESSION['id']){
        alert("No puedes ascenderte a ti mismo","",0);
      }
      
      if(es_clon($_SESSION['id'], $user)){
        alert("Se ha detectado que intentas ejecutar acciones con 2 personajes en la misma casa, esto esta PROHIBIDO, ten cuidado o podrias terminar baneado \ degradado \ despedido de ".$habbo_name,"",0);
      }

      if(!requiere_ascenso($user)){
        alert("Esta persona aun no puede ascender","",0);
      }

      $qu = $pdo->prepare("SELECT * FROM users WHERE id = :user");
      $qu->execute([$user]);

      $ru = $qu->fetch();

      $nuevo_rol = $ru['rol']+1;
      $antiguo_rol = $ru['rol'];

      $mision = $prefix_firma."-  ".nombre_rol_usuario($nuevo_rol)." -".firma($_SESSION['id'])." -".firma($ru['id']);


      if(!tiene_poder_ascenso($_SESSION['id'],$user)){
        alert("No tienes permiso para ascender a esta persona","",0);
      }

      
      $nc = generar_codigo();

      $q = $pdo->prepare("INSERT INTO ascensos (id_dio, id_recibe, old_rol, new_rol, created_at) VALUES (:id_dio, :id_recibe, :old_rol, :new_rol, :fecha_actual)");
      $q->execute([
          $_SESSION['id'],
          $user,
          $antiguo_rol,
          $nuevo_rol,
          $fecha_actual
      ]);

      $qf = $pdo->prepare("UPDATE users SET rol = :rol, codigo = :nc WHERE id = :id");
      $qf->execute([$nuevo_rol, $nc, $user]);

      alert("Se ha ascendido a ".$ru['habbo']." Satisfactoriamente.","?p=mi_ultimo_ascenso",1);
    }else{
      alert("El codigo de seguridad del usuario que has ingresado no es valido. Intentalo de nuevo.","",0);
    }
}


if(isset($enviar2)){
  if(is_manager_or_more(rol($_SESSION['id']))){

    if(!can_merito($user2)){
      alert("Esta persona no ha cumplido sus 25 dias para ascenso de merito","",0);
    }

    $user2 = clear($user2);
    $new_rol = clear($new_rol);

    $qu = $pdo->prepare("SELECT * FROM users WHERE id = :user");
    $qu->execute([$user2]);

    $ru = $qu->fetch();

    $antiguo_rol = $ru['rol'];

    if($antiguo_rol >= $new_rol){
      alert("Debes seleccionar un rol superior al actual (".nombre_rol($ru['rol']).")","",0);
    }



    $q = $pdo->prepare("INSERT INTO ascensos (id_dio, id_recibe, old_rol, new_rol, created_at, tipo) VALUES (:id_dio, :id_recibe, :old_rol, :new_rol, NOW(), 1)");
    $q->execute([
        $_SESSION['id'],
        $user2,
        $antiguo_rol,
        $new_rol
    ]);

    $qf = $pdo->prepare("UPDATE users SET rol = :rol WHERE id = :id");
    $qf->execute([$new_rol, $user2]);

    alert("Se ha ascendido a ".$ru['habbo']." Satisfactoriamente.","?p=mi_ultimo_ascenso",1);
  }else{
    alert("No tienes permisos para realizar esta acción","",0);
  }
}

if(isset($devolver)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $devolver = clear($devolver);
    $q = $pdo->prepare("UPDATE ascensos SET pagado = 0 WHERE id = :id");
    $q->execute([
      $devolver
    ]);

    alert("Se ha devuelto este ascenso como No Pagado","?p=ascensos",1);
  }
}

if(isset($revocar)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $revocar = clear($revocar);
    $q = $pdo->prepare("UPDATE ascensos SET pagado = 1 WHERE id = :id");
    $q->execute([
      $revocar
    ]);

    alert("Se ha devuelto este ascenso como No Pagado","?p=ascensos",1);
  }
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Administrar Ascensos</h3>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <div class="row" <?php if(!is_manager_or_more(rol($_SESSION['id']))){ echo 'style="display: flex; justify-content: center;"'; }?>>
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-danger" style="min-height:459px">
              <div class="card-header">
                <h3 class="card-title">Ascenso Común</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuarios Pendientes por Ascender</label>

                    
                    <div>
                      <input style="width: 100%" class="form-control" id="search" placeholder="Busca al usuario..."/>
                      <div id="output" style="display:none;"></div>
                    </div>


                    <input type="text" style="position:fixed;left: -1000000px; top: -100000px" name="user" value="" required id="user"/>

                  
                  </div>

                  <i id="ocultar">Seleccione un personaje a Ascender<br></i>

                  <img src="./imgs/loading.gif" id="kekox" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo"></span><br>
                  <span id="rango_habbo"></span>
                  <input type="text" maxlength="5" class="form-control" style="width: 40%; color:red; font-weight: bold; text-align:center; display:none;" id="cs" name="codigo" placeholder="Codigo de Seguridad del usuario"/>
                
                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                  <button type="submit" name="enviar" class="btn btn-danger">Ascender</button>
                </div>
                
              </form>
            </div>
            <!-- /.card -->

          </div>








        <?php
        if(is_manager_or_more(rol($_SESSION['id']))){
          ?>





          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-danger" style="min-height:459px">
              <div class="card-header">
                <h3 class="card-title">Ascenso Merito</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuarios Pendientes por Ascender</label>


                   
                    <div>
                      <input style="width: 100%" class="form-control" id="search2" placeholder="Busca al usuario..."/>
                      <div id="output2" style="display:none;"></div>
                    </div>


                    <input type="text" style="position:fixed;left: -1000000px; top: -100000px" name="user2" value="" required id="user2"/>

                   
                  </div>

                  <i id="ocultar2">Seleccione un personaje a Ascender<br></i>

                  <img src="./imgs/loading.gif" id="kekox2" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo2"></span><br>
                  <span id="rango_habbo2"></span>
          <br><br>
                  <div class="form-group" style="width:40%">
                    <select id="new_rol" style="display: none;" required class="form-control" name="new_rol" id="exampleInputEmail1">
                        <option value="">Seleccione un nuevo Rango</option>
                        <?=options_roles()?>
                    </select>
                  </div>
                
                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                  <button type="submit" name="enviar2" class="btn btn-success">Ascender por Merito</button>
                </div>
                
              </form>
            </div>
            <!-- /.card -->

          </div>


          <?php
        }
          ?>






          
          <!--/.col (right) -->
        </div>
        <!-- /.row -->

        <!-- <br><br>
        <h3>Lista de ascensos Pendientes</h3>
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dts" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Habbo</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
            </div>
        </div> -->

        <br><br>
        <h3>Lista de ascensos Realizados</h3>
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dt" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dio Ascenso</th>
                        <th>Recibio Ascenso</th>
                        <th>Antiguo Rango</th>
                        <th>Nuevo Rango</th>
                        <th>Fecha Hora</th>
                        <th>Status</th>
                        <th>Tipo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?=tabla_ascensos()?>
                </tbody>
            </table>
            </div>
        </div>



       
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->


<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".menu1 li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});


$(document).ready(function(){
  $("#myInput2").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".menu2 li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
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

function prepararUsuario2(id, nombre, rango){
  $("#search2").val('');
  $("#output2").fadeOut();

  $("#new_rol").fadeIn();

  $("#ocultar2").fadeOut();

    $("#kekox2").css("opacity","1");

    $("#nombreHabbo2").html(nombre);
    $("#user2").val(id);

    $("#kekox2").attr("src","imgs/loading.gif");

    $("#rango_habbo2").html(rango);


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox2").attr("src",url);
                });

}

$(document).ready(function() {
    // $('.dts').DataTable( {
    //     "processing": true,
    //     "serverSide": false,
    //     "ajax": "ajax/ascensos_pendientes.php"
    // } );

    $("#search").keyup(function(){
          var query = $(this).val();
          if (query.length > 2) {
            $.ajax({
              url: 'ajax/ascenso_comun.php',
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

      

    $("#search2").keyup(function(){
          var query = $(this).val();
          if (query.length > 2) {
            $.ajax({
              url: 'ajax/ascenso_merito.php',
              method: 'POST',
              data: {query:query},
              success: function(data){
 
                $('#output2').html(data);
                $('#output2').css('display', 'block');
 
                // $("#search").focusout(function(){
                //     $('#output').css('display', 'none');
                // });
                // $("#search").focusin(function(){
                //     $('#output').css('display', 'block');
                // });
              },
              beforeSend:function(){
                $("#output2").html('<img src="imgs/loading.gif" style="width: 50px;"/>');
              }
            });
          } else {
          $('#output2').css('display', 'none');
        }
      });
} );

</script>