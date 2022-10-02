
<?php

if(!is_admin_or_more(rol($_SESSION['id']))){
  alert("No tienes acceso a visualizar este modulo","./",0);
}

if(isset($enviar)){
      $user = clear($user);
      if(tiene_poder($_SESSION['id'],$user) || is_owner_or_more(rol($_SESSION['id']))){

      $motivo = clear($razon_degrado);


      $qu = $pdo->prepare("SELECT * FROM users WHERE id = :user");
      $qu->execute([$user]);

      $ru = $qu->fetch();

      $nuevo_rol = $new_rol;
      $antiguo_rol = $ru['rol'];

      if($nuevo_rol >= $antiguo_rol){
        alert("Debes seleccionar un rol inferior","",0);
      }

      $mision = $prefix_firma."-  ".nombre_rol_usuario($nuevo_rol)." -".firma($_SESSION['id'])." -".firma($ru['id']);

      $fecha = date("Y-m-d H:i:s");

      $q = $pdo->prepare("INSERT INTO degrados (id_dio, id_recibe, old_rol, new_rol, mision, created_at, motivo) VALUES (:id_dio, :id_recibe, :old_rol, :new_rol, :mision, :fecha, :motivo)");
      $q->execute([
          $_SESSION['id'],
          $user,
          $antiguo_rol,
          $nuevo_rol,
          $mision,
          $fecha,
          $motivo
      ]);

      $qf1 = $pdo->prepare("DELETE FROM ascensos WHERE id_recibe = :id ORDER BY id DESC LIMIT 1");
      $qf1->execute([$user]);

      $qf2 = $pdo->prepare("INSERT INTO ascensos (id_dio, id_recibe, old_rol, new_rol, created_at) VALUES (:id_dio, :id_recibe, :old_rol, :new_rol, NOW())");
      $qf2->execute([
          $_SESSION['id'],
          $user,
          $antiguo_rol,
          $nuevo_rol
      ]);

      $qf = $pdo->prepare("UPDATE users SET rol = :rol WHERE id = :id");
      $qf->execute([$nuevo_rol, $user]);

      alert("Se ha degradado a ".$ru['habbo']." Satisfactoriamente.","?p=degrado",1);

    }else{
      alert("No tienes permiso para degradar a esta persona","?p=degrado",1);
    }
    
}


if(isset($enviar2)){
    $user2 = clear($user2);
    
    if(tiene_poder($_SESSION['id'],$user)){

    $motivo = clear($razon_despido);

    $qu = $pdo->prepare("SELECT * FROM users WHERE id = :user");
    $qu->execute([$user2]);

    $ru = $qu->fetch();

    $antiguo_rol = $ru['rol'];

    $fecha = date("Y-m-d H:i:s");

    $qf = $pdo->prepare("UPDATE users SET firma = '' WHERE id = :id");
    $qf->execute([
      $user2
    ]);



    $q = $pdo->prepare("INSERT INTO degrados (id_dio, id_recibe, old_rol, new_rol, created_at, tipo, motivo) VALUES (:id_dio, :id_recibe, :old_rol, -1, :fecha, 1, :motivo)");
    $q->execute([
        $_SESSION['id'],
        $user2,
        $antiguo_rol,
        $fecha,
        $motivo
    ]);

    $qf = $pdo->prepare("UPDATE users SET rol = -1 WHERE id = :id");
    $qf->execute([$user2]);

    alert("Se ha despedido a ".$ru['habbo']." Satisfactoriamente.","?p=degrado",1);

  }else{
    alert("No tienes permiso para despedir a esta persona","?p=degrado",1);
  }
  
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Administrar Degrado / Despido</h3>
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
            <div class="card card-warning" >
              <div class="card-header">
                <h3 class="card-title">Degrado</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Seleccione el usuario</label>

                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Busque el usuario
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu menu1 menu-scroll">
                        <input class="form-control" id="myInput" type="text" placeholder="Search..">
                        <?=lista_usuarios_degrado()?>
                        </ul>
                    </div>


                    <input type="text" style="position:fixed;left: -1000000px; top: -100000px" name="user" value="" required id="user"/>

                  
                  </div>

                  <i id="ocultar">Seleccione un personaje a Degradar<br></i>

                  <img src="./imgs/loading.gif" id="kekox" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo"></span><br>
                  <span id="rango_habbo"></span>
                  <br><br>
                  
                  
                  <div class="form-group" style="width:40%">
                    <select id="new_rol" style="display: none;" required class="form-control" name="new_rol" id="exampleInputEmail1">
                        <option value="">Seleccione un nuevo Rango</option>
                        <?=options_roles()?>
                    </select>
                  </div>
                  
                  
                  <div class="form-group" id="razon_degrado" style="width:50%; display: none">
                    <label>Motivo</label><br>
                    <input type="radio" required name="razon_degrado" value="0"/> Robo<br>
                    <input type="radio" required name="razon_degrado" value="1"/> Petar<br>
                    <input type="radio" required name="razon_degrado" value="2"/> Acosar<br>
                    <input type="radio" required name="razon_degrado" value="3"/> Auto Ascenso<br>
                    <input type="radio" required name="razon_degrado" value="4"/> Troleo<br>
                    <input type="radio" required name="razon_degrado" value="5"/> Insulto<br>
                    <input type="radio" required name="razon_degrado" value="6"/> Desobediencia<br>
                    <input type="radio" required name="razon_degrado" value="7"/> Actividad Baja<br>
                    <input type="radio" required name="razon_degrado" value="8"/> Auto Degrado<br>
                    <input type="radio" required name="razon_degrado" value="9"/> Traslado Agencia<br>
                    <input type="radio" required name="razon_degrado" value="10"/> Renuncia<br>
                    <input type="radio" required name="razon_degrado" value="11"/> Doble Empleo<br>

                  </div>
                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                  <button type="submit" name="enviar" class="btn btn-warning">Degradar</button>
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
                <h3 class="card-title">Despido</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Seleccione el usuario</label>

                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Busque el usuario
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu menu2 menu-scroll">
                        <input class="form-control" id="myInput2" type="text" placeholder="Search..">
                        <?=lista_usuarios_despido()?>
                        </ul>
                    </div>


                    <input type="text" style="position:fixed;left: -1000000px; top: -100000px" name="user2" value="" required id="user2"/>

                   
                  </div>

                  <i id="ocultar2">Seleccione un personaje a Despedir<br></i>

                  <img src="./imgs/loading.gif" id="kekox2" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo2"></span><br>
                  <span id="rango_habbo2"></span>
          <br><br>
          <div class="form-group" id="razon_despido" style="width:50%; display: none">
                    <label>Motivo</label><br>
                    <input type="radio" required name="razon_despido" value="0"/> Robo<br>
                    <input type="radio" required name="razon_despido" value="1"/> Petar<br>
                    <input type="radio" required name="razon_despido" value="2"/> Acosar<br>
                    <input type="radio" required name="razon_despido" value="3"/> Auto Ascenso<br>
                    <input type="radio" required name="razon_despido" value="4"/> Troleo<br>
                    <input type="radio" required name="razon_despido" value="5"/> Insulto<br>
                    <input type="radio" required name="razon_despido" value="6"/> Desobediencia<br>
                    <input type="radio" required name="razon_despido" value="7"/> Actividad Baja<br>
                    <input type="radio" required name="razon_despido" value="8"/> Auto Degrado<br>
                    <input type="radio" required name="razon_despido" value="9"/> Traslado Agencia<br>
                    <input type="radio" required name="razon_despido" value="10"/> Renuncia<br>
                    <input type="radio" required name="razon_despido" value="11"/> Doble Empleo<br>

                  </div>
                
                
                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                  <button type="submit" name="enviar2" class="btn btn-danger">Despedir</button>
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

<br><br>
        <h3>Lista de degrados / despidos</h3>
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
                        <th>Motivo</th>
                        <th>Fecha Hora</th>
                        <th>Tipo</th>
                    </tr>
                </thead>

                <tbody>
                    <?=tabla_degrados()?>
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

  $("#ocultar").fadeOut();

    $("#kekox").css("opacity","1");

    $("#nombreHabbo").html(nombre);
    $("#user").val(id);

    $("#kekox").attr("src","imgs/loading.gif");

    $("#rango_habbo").html(rango);

    $("#cs").fadeIn();

    $("#razon_degrado").fadeIn();
  $("#new_rol").fadeIn();


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox").attr("src",url);
                });

}

function prepararUsuario2(id, nombre, rango){


  $("#ocultar2").fadeOut();

    $("#kekox2").css("opacity","1");

    $("#nombreHabbo2").html(nombre);
    $("#user2").val(id);

    $("#kekox2").attr("src","imgs/loading.gif");

    $("#rango_habbo2").html(rango);
    $("#razon_despido").fadeIn();


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox2").attr("src",url);
                });

}
</script>