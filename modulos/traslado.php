<?php
if(!is_admin_or_more(rol($_SESSION['id']))){
  alert("No tienes acceso a entrar a este modulo","./",0);
}
?>
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

if(isset($enviar2)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $user2 = clear($user2);
    $new_keko = clear($new_keko);

    $qu = $pdo->prepare("SELECT * FROM users WHERE habbo = :new_keko OR user = :new_kekox");
    $qu->execute([$new_keko,$new_keko]);



    if($qu->rowCount()>0){
      alert("El nombre de usuario o keko ya existe en nuestra base de datos, contacta a un administrador.","",0);
    }



    $q = $pdo->prepare("UPDATE users SET user = :new_keko, habbo = :new_kekox WHERE id = :user2");
    $q->execute([
        $new_keko,
        $new_keko,
        $user2
    ]);

    


    alert("Se ha trasladado a ".$new_keko." Satisfactoriamente.","?p=traslado",1);
  }else{
    alert("No tienes permisos para realizar esta acción","",0);
  }
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h3>Traslado</h3>
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
            <div class="card card-danger" style="min-height:459px">
              <div class="card-header">
                <h3 class="card-title">Realizar Traslado</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuarios a Trasladar</label>

                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Busque el usuario
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu menu2 menu-scroll">
                        <input class="form-control" id="myInput2" type="text" placeholder="Search..">
                        <?=lista_usuarios_pendientes_ascenso2()?>
                        </ul>
                    </div>

                  

                    <input type="text" style="position:fixed;left: -1000000px; top: -100000px" name="user2" value="" required id="user2"/>

                   
                  </div>

                  <i id="ocultar2">Seleccione un personaje a Trasladar<br></i>

                  <img src="./imgs/loading.gif" id="kekox2" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo2"></span><br>
                  <span id="rango_habbo2"></span>
          <br><br>
                  <div class="form-group" style="width:40%">
                  <input type="text" id="new_rol" style="display: none;" placeholder="Nombre de su nuevo personaje" name="new_keko" required class="form-control"/>
                   
                  </div>
                
                </div>
                <!-- /.card-body -->

                </center>

                <div class="card-footer">
                  <button type="submit" name="enviar2" class="btn btn-danger">Hacer Traslado</button>
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

function prepararUsuario2(id, nombre, rango){

  $("#new_rol").fadeIn();

  $("#ocultar2").fadeOut();

    $("#kekox2").css("opacity","1");

    $("#nombreHabbo2").html(nombre);
    $("#user2").val(id);

    $("#kekox2").attr("src","imgs/loading.gif");

    $("#rango_habbo2").html(rango);
    
    $("#tipo").fadeIn();


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox2").attr("src",url);
                });

}
</script>