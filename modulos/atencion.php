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
    $texto = clear($texto);
    $tipo = clear($tipo);

    $fecha = date("Y-m-d H:i:s");

    $q = $pdo->prepare("INSERT INTO atencion (id_dio, id_recibe, fecha, texto, tipo) VALUES (:id_dio, :id_recibe, :fecha, :texto, :tipo)");
    $q->execute([
        $_SESSION['id'],
        $user2,
        $fecha,
        $texto,
        $tipo
    ]);

    $qa = $pdo->prepare("SELECT id FROM atencion WHERE id_dio = :id ORDER BY id DESC LIMIT 1");
    $qa->execute([
      $_SESSION['id']
    ]);

    $ra = $qa->fetch();





    for($a = 0; $a < count($_FILES['imagenes']['name']); $a++){
      $file = pathinfo($_FILES['imagenes']['name'][$a]);
      $extension = $file['extension'];
  
      $permitidas = array("jpg","jpeg","png");
  
      if(!in_array(strtolower($extension),$permitidas)){
        alert("Solo puedes subir imagenes (jpg, jpeg, png). Intentalo e nuevo.","",0);
      }
  
  
      $imagen = md5(date("d-m-Y h_i a s ").rand(0,9999).rand(0,99999)).".png";
     
  
      move_uploaded_file($_FILES['imagenes']['tmp_name'][$a], "imgs_atencion/".$imagen);
      
      $qt = $pdo->prepare("INSERT INTO imagenes_atencion (id_atencion, imagen) VALUES (:id_atencion, :imagen)");
      $qt->execute(array(":id_atencion"=>$ra['id'],":imagen"=>$imagen));
     
  
    }

   
    alert("Se ha Llamado la atencion a ".$ru['habbo']." Satisfactoriamente.","?p=atencion",1);
  }else{
    alert("No tienes permisos para realizar esta acción","./",0);
  }
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h3>Llamados de atención</h3>
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
                <h3 class="card-title">Llamado de atencion</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="" enctype="multipart/form-data">
                <center>
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuarios</label>

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

                  <i id="ocultar2">Seleccione un personaje a Llamar la atencion<br></i>

                  <img src="./imgs/loading.gif" id="kekox2" style="width: 100px; opacity: 0"/>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo2"></span><br>
                  <span id="rango_habbo2"></span>
          <br><br>
                  <div class="form-group" style="width:40%">
                   <textarea id="texto" style="display: none;" name="texto" class="form-control" placeholder="Describa la situación"></textarea>
                  </div>
                
                </div>
                <!-- /.card-body -->
                <div class="form-group" id="tipo" style="width:50%; display: none">
                    <label>Motivo</label><br>
                    <input type="radio" required name="tipo" value="0"/> No respetar autoridad<br>
                    <input type="radio" required name="tipo" value="1"/> No respetar compañeros<br>
                    <input type="radio" required name="tipo" value="2"/> Incumplimiento de reglas<br>
                    <input type="radio" required name="tipo" value="3"/> Acoso<br>
                    <input type="radio" required name="tipo" value="4"/> Spam<br>

                  </div>

                <div class="form-group" id="pruebas" style="width:50%; display: none">
                    <label>Imagenes [Pruebas] (Opcional)</label><br>
                    <input type="file" name="imagenes[]" multiple/>
                    

                  </div>

                </center>

                <div class="card-footer">
                  <button type="submit" name="enviar2" class="btn btn-danger">Llamar la atencion</button>
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
        <h3>Lista de Llamados de atencion</h3>
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dtc" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dio Llamado</th>
                        <th>Recibio Llamado</th>
                        <th>Cantidad de Llamados</th>
                        <th>Fecha Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?=tabla_atencion()?>
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

  $(".dtc").DataTable({
    order : [[
      0, "desc"
    ]]
  })
});

function prepararUsuario2(id, nombre, rango){

  $("#new_rol").fadeIn();

  $("#ocultar2").fadeOut();

    $("#kekox2").css("opacity","1");

    $("#nombreHabbo2").html(nombre);
    $("#user2").val(id);

    $("#kekox2").attr("src","imgs/loading.gif");

    $("#rango_habbo2").html(rango);

    $("#texto").fadeIn();
    
    $("#tipo").fadeIn();
    
    $("#pruebas").fadeIn();


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox2").attr("src",url);
                });

}
</script>