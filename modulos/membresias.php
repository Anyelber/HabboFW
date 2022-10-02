<style>
  #myInput {
    padding: 20px;
    margin-top: -6px;
    border: 0;
    border-radius: 0;
    background: #f1f1f1;
  }

  #myInput2 {
    padding: 20px;
    margin-top: -6px;
    border: 0;
    border-radius: 0;
    background: #f1f1f1;
  }

.dropdown-menu li a{
    padding-left:10px;
    color: #333;
    display:block;
}

.dropdown-menu2 li a{
    padding-left:10px;
    color: #333;
    display:block;
}
    </style>

<?php

if(!is_admin_or_more(rol($_SESSION['id']))){
  alert("No tienes acceso a este modulo","./",0);
}

if(isset($enviar1)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $user1 = clear($user1);

    if(!tiene_save($user1)){

        $fecha = date("Y-m-d");

        $fecha_exp = date("Y-m-d",strtotime(date("Y-m-d")." +1 month"));

        $q = $pdo->prepare("INSERT INTO saves (id_dio, id_recibe, fecha, fecha_exp, status) VALUES (:idd, :idr, :fecha, :fecha_exp, 1)");
        $q->execute([
            $_SESSION['id'],
            $user1,
            $fecha,
            $fecha_exp
        ]);
    }else{
        alert("Este usuario ya tiene un save","",0);
    }
    


    alert("Se ha dado save a ".$ru['habbo']." Satisfactoriamente.","?p=membresias",1);
  }else{
    alert("No tienes permisos para realizar esta acción","",0);
  }
}



if(isset($enviar2)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $user2 = clear($user2);

    if(!tiene_fila($user2)){

        $fecha = date("Y-m-d");

        $fecha_exp = date("Y-m-d",strtotime(date("Y-m-d")." +1 month"));

        $q = $pdo->prepare("INSERT INTO saves (id_dio, id_recibe, fecha, fecha_exp, status, tipo) VALUES (:idd, :idr, :fecha, :fecha_exp, 1, 1)");
        $q->execute([
            $_SESSION['id'],
            $user2,
            $fecha,
            $fecha_exp
        ]);
    }else{
        alert("Este usuario ya tiene Fila","",0);
    }
    


    alert("Se ha dado Fila a ".$ru['habbo']." Satisfactoriamente.","?p=membresias",1);
  }else{
    alert("No tienes permisos para realizar esta acción","",0);
  }
}



if(isset($enviar3)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $user3 = clear($user3);

    if(!tiene_vip($user3)){

        $fecha = date("Y-m-d");

        $q = $pdo->prepare("INSERT INTO saves (id_dio, id_recibe, fecha, status, tipo) VALUES (:idd, :idr, :fecha, 1, 2)");
        $q->execute([
            $_SESSION['id'],
            $user3,
            $fecha,
        ]);
    }else{
        alert("Este usuario ya tiene VIP","",0);
    }
    


    alert("Se ha dado VIP a ".$ru['habbo']." Satisfactoriamente.","?p=membresias",1);
  }else{
    alert("No tienes permisos para realizar esta acción","",0);
  }
}

if(isset($darpase)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $user4 = clear($user4);

    if(!tiene_pase($user4)){

        $fecha = date("Y-m-d");
        
        $fecha_exp = date("Y-m-d",strtotime(date("Y-m-d")." +1 month"));

        $q = $pdo->prepare("INSERT INTO saves (id_dio, id_recibe, fecha, fecha_exp, status, tipo) VALUES (:idd, :idr, :fecha, :fecha_exp, 1, 3)");
        $q->execute([
            $_SESSION['id'],
            $user4,
            $fecha,
            $fecha_exp
        ]);
    }else{
        alert("Este usuario ya tiene PASE","",0);
    }
    


    alert("Se ha dado PASE a ".$ru['habbo']." Satisfactoriamente.","?p=membresias",1);
  }else{
    alert("No tienes permisos para realizar esta acción","",0);
  }
}

if(isset($eliminar)){
    if(is_admin_or_more(rol($_SESSION['id']))){
        $eliminar = clear($eliminar);

        $fecha = date("Y-m-d");

        $q = $pdo->prepare("UPDATE saves SET deleted_by = :idu, status = 0, fecha_cierre = :fecha WHERE id = :eliminar");
        $q->execute([
            $_SESSION['id'],
            $fecha,
            $eliminar
        ]);



        alert("Se ha eliminado el save satisfactoriamente","?p=membresias",1);
        
    }else{
        alert("No tienes permisos para realizar esta acción","",0);
    }
}

if(isset($eliminar_vip)){
    if(is_admin_or_more(rol($_SESSION['id']))){
        $eliminar_vip = clear($eliminar_vip);

        $fecha = date("Y-m-d");

        $q = $pdo->prepare("UPDATE saves SET deleted_by = :idu, status = 0, fecha_cierre = :fecha WHERE id = :eliminar_vip");
        $q->execute([
            $_SESSION['id'],
            $fecha,
            $eliminar_vip
        ]);



        alert("Se ha eliminado el VIP satisfactoriamente","?p=membresias",1);
        
    }else{
        alert("No tienes permisos para realizar esta acción","",0);
    }
}

if(isset($extender)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $extender = clear($extender);

    $q = $pdo->prepare("SELECT fecha_exp FROM saves WHERE id = :id");
    $q->execute([$extender]);

    $r = $q->fetch();

    $new_date = date("Y-m-d",strtotime($r['fecha_exp']." +1 month"));
    
    $q = $pdo->prepare("UPDATE saves SET fecha_exp = :new_date, extendido_por = :ep WHERE id = :id");
    $q->execute([
      $new_date,
      $_SESSION['id'],
      $extender
    ]);

    alert("Se ha extendido la fecha del Save","?p=membresias",1);
  }
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h3>Administrar Membresias</h3>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <div class="row" style="display: flex; justify-content: center; flex-wrap: wrap">
          <!-- left column -->
            <!-- general form elements -->
        

        <?php
        if(is_admin_or_more(rol($_SESSION['id']))){
          ?>














          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-warning" style="min-height:459px">
              <div class="card-header">
                <h3 class="card-title">Dar Save</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuarios a dar Save</label>


                   
                    <div>
                      <input style="width: 100%" class="form-control" id="search1" placeholder="Busca al usuario..."/>
                      <div id="output1" style="display:none;"></div>
                    </div>


                    <input type="text" style="position:fixed;left: -1000000px; top: -100000px" name="user1" value="" required id="user1"/>

                   
                  </div>

                  <i id="ocultar1">Seleccione un personaje a Ascender<br></i>

                  <img src="./imgs/loading.gif" id="kekox1" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo1"></span><br>
                  <span id="rango_habbo2"></span>
                 
                
                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                  <button type="submit" name="enviar1" class="btn btn-success">Dar Save</button>
                </div>
                
              </form>
            </div>
            <!-- /.card -->

          </div>





















          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary" style="min-height:459px">
              <div class="card-header">
                <h3 class="card-title">Dar Fila</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuarios a dar Fila</label>


                   
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
                 
                
                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                  <button type="submit" name="enviar2" class="btn btn-success">Dar Fila</button>
                </div>
                
              </form>
            </div>
            <!-- /.card -->

          </div>


























          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-danger" style="min-height:459px">
              <div class="card-header">
                <h3 class="card-title">Dar VIP</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuarios a dar VIP</label>


                   
                    <div>
                      <input style="width: 100%" class="form-control" id="search3" placeholder="Busca al usuario..."/>
                      <div id="output3" style="display:none;"></div>
                    </div>


                    <input type="text" style="position:fixed;left: -1000000px; top: -100000px" name="user3" value="" required id="user3"/>

                   
                  </div>

                  <i id="ocultar3">Seleccione un personaje a Ascender<br></i>

                  <img src="./imgs/loading.gif" id="kekox3" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo3"></span><br>
                  <span id="rango_habbo2"></span>
                 
                
                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                  <button type="submit" name="enviar3" class="btn btn-success">Dar VIP</button>
                </div>
                
              </form>
            </div>
            <!-- /.card -->

          </div>


















          

          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-success" style="min-height:459px">
              <div class="card-header">
                <h3 class="card-title">Dar PASE</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuarios a dar PASE</label>


                   
                    <div>
                      <input style="width: 100%" class="form-control" id="search4" placeholder="Busca al usuario..."/>
                      <div id="output4" style="display:none;"></div>
                    </div>


                    <input type="text" style="position:fixed;left: -1000000px; top: -100000px" name="user4" value="" required id="user4"/>

                   
                  </div>

                  <i id="ocultar4">Seleccione un personaje a Ascender<br></i>

                  <img src="./imgs/loading.gif" id="kekox4" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo4"></span><br>
                  <span id="rango_habbo2"></span>
                 
                
                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                  <button type="submit" name="darpase" class="btn btn-success">Dar PASE</button>
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
    </div>

    <br><br>
        <h3>Lista de Usuarios con VIP</h3>
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dt" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Dio</th>
                        <th>Recibio</th>
                        <th>Fecha Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?=tabla_users_vip()?>
                </tbody>
            </table>
            </div>
        </div>

    <br><br>

    <br><br>
        <h3>Lista de Usuarios con Save / Fila / PASE</h3>
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dt" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Dio</th>
                        <th>Recibio</th>
                        <th>Fecha Hora</th>
                        <th>Dia de Vencimiento</th>
                        <th>Extendido Por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?=tabla_users_save()?>
                </tbody>
            </table>
            </div>
        </div>

    <br><br>
        <h3>Lista de Saves / Filas / PASE / VIP Expirados</h3>
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dt" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Dio el Save</th>
                        <th>Recibio el Save</th>
                        <th>Fecha del Save</th>
                        <th>Fecha de Expiración</th>
                        <th>Extendido Por</th>
                        <th>Eliminado por</th>
                    </tr>
                </thead>

                <tbody>
                    <?=tabla_users_save_antiguos()?>
                </tbody>
            </table>
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->


<script>


$(document).ready(function(){
  $("#myInput2").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".menu2 li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

$(document).ready(function(){
  $("#myInput1").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".menu1 li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});


$(document).ready(function(){
  $("#myInput3").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".menu3 li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});





function prepararUsuario1(id, nombre, rango){
  $("#search1").val('');
  $("#output1").fadeOut();

  $("#new_rol").fadeIn();

  $("#ocultar1").fadeOut();

    $("#kekox1").css("opacity","1");

    $("#nombreHabbo1").html(nombre);
    $("#user1").val(id);

    $("#kekox1").attr("src","imgs/loading.gif");


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox1").attr("src",url);
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


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox2").attr("src",url);
                });

}

function prepararUsuario3(id, nombre, rango){
  $("#search3").val('');
  $("#output3").fadeOut();

  $("#new_rol").fadeIn();

  $("#ocultar3").fadeOut();

    $("#kekox3").css("opacity","1");

    $("#nombreHabbo3").html(nombre);
    $("#user3").val(id);

    $("#kekox3").attr("src","imgs/loading.gif");


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox3").attr("src",url);
                });

}

function prepararUsuario4(id, nombre, rango){
  $("#search4").val('');
  $("#output4").fadeOut();

  $("#new_rol").fadeIn();

  $("#ocultar4").fadeOut();

    $("#kekox4").css("opacity","1");

    $("#nombreHabbo4").html(nombre);
    $("#user4").val(id);

    $("#kekox4").attr("src","imgs/loading.gif");


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox4").attr("src",url);
                });

}


$("#search1").keyup(function(){
          var query = $(this).val();
          if (query.length > 2) {
            $.ajax({
              url: 'ajax/dar_save.php',
              method: 'POST',
              data: {query:query},
              success: function(data){
 
                $('#output1').html(data);
                $('#output1').css('display', 'block');
 
                // $("#search").focusout(function(){
                //     $('#output').css('display', 'none');
                // });
                // $("#search").focusin(function(){
                //     $('#output').css('display', 'block');
                // });
              },
              beforeSend:function(){
                $("#output1").html('<img src="imgs/loading.gif" style="width: 50px;"/>');
              }
            });
          } else {
          $('#output1').css('display', 'none');
        }
      });

$("#search2").keyup(function(){
          var query = $(this).val();
          if (query.length > 2) {
            $.ajax({
              url: 'ajax/dar_fila.php',
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

$("#search3").keyup(function(){
          var query = $(this).val();
          if (query.length > 2) {
            $.ajax({
              url: 'ajax/dar_vip.php',
              method: 'POST',
              data: {query:query},
              success: function(data){
 
                $('#output3').html(data);
                $('#output3').css('display', 'block');
 
                // $("#search").focusout(function(){
                //     $('#output').css('display', 'none');
                // });
                // $("#search").focusin(function(){
                //     $('#output').css('display', 'block');
                // });
              },
              beforeSend:function(){
                $("#output3").html('<img src="imgs/loading.gif" style="width: 50px;"/>');
              }
            });
          } else {
          $('#output3').css('display', 'none');
        }
      });

$("#search4").keyup(function(){
          var query = $(this).val();
          if (query.length > 2) {
            $.ajax({
              url: 'ajax/dar_pase.php',
              method: 'POST',
              data: {query:query},
              success: function(data){
 
                $('#output4').html(data);
                $('#output4').css('display', 'block');
 
                // $("#search").focusout(function(){
                //     $('#output').css('display', 'none');
                // });
                // $("#search").focusin(function(){
                //     $('#output').css('display', 'block');
                // });
              },
              beforeSend:function(){
                $("#output4").html('<img src="imgs/loading.gif" style="width: 50px;"/>');
              }
            });
          } else {
          $('#output4').css('display', 'none');
        }
      });
</script>