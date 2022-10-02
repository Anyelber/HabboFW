<?php

if(!is_admin_or_more(rol($_SESSION['id']))){
  alert("No tienes permiso para acceder a este modulo","./",0);
}

$id = clear($id);
$q = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$q->execute([$id]);

$r = $q->fetch();

if(isset($enviar)){

    $npassword = clear($npassword);
    $cnpassword = clear($cnpassword);


    if($npassword != $cnpassword){
        alert("Las nuevas contraseñas no coinciden","",0);
    }

    $enpassword = md5($npassword);

    $q = $pdo->prepare("UPDATE users set password = :pw WHERE id = :id");
    $q->execute([
        $enpassword,
        $r['id']
    ]);

    alert("Se ha cambiado la contraseña","?p=ver_detalles_habbo&id=".$id,1);
}

if(isset($foro)){

  if(rol($id)>46){
    if($especial == 1){
      alert("No puedes asignar a Presidente A+ como foro inactivo","",0);
    }
  }

  $especial = clear($especial);
  $qf = $pdo->prepare("UPDATE users SET especial = :especial WHERE id = :id");
  $qf->execute([
    $especial,
    $id
  ]);

  if($especial == 1){
    alert("Se ha cambiado a ".$r['habbo']." a Foro Inactivo","?p=usuarios",1);
  }else{
    alert("Se ha quitado a ".$r['habbo']." de Foro Inactivo","?p=usuarios",1);
  }


  
}
if(isset($cfirma)){
  $firma = clear($firma);

  $qv = $pdo->prepare("SELECT id FROM users WHERE firma = :firma");
  $qv->execute([
    $firma
  ]);

  if($qv->rowCount()>0){
    alert("Esta firma ya está en uso, porfavor intenta colocar otra firma","",0);
  }

  $q = $pdo->prepare("UPDATE users SET firma = :firma WHERE id = :id");
  $q->execute([
    $firma,
    $id
  ]);

  alert("Se ha cambiado la firma","",1);
}

$qh = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$qh->execute([$_SESSION['id']]);
$rh = $qh->fetch();
 ?>
 <!-- Content Header (Page header) -->

<br><br>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row " style="display: flex; justify-content: center;">
          <!-- left column -->
          <div class="col-md-8">
            <!-- general form elements -->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Editar perfil de <?=nombre_habbo($r['id'])?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">
                  <div class="form-group">
                    <form method="post" action="">
                    <center>
                        <?=keko_user($r['id'],100,'sit',2)?><br>
                        
                        Usuario: <span style="color:red"><?=$r['user']?></span>
                    </center>
                    <br><br>
                    
                    <label>Cambio de Contraseña</label>
                    <div class="form-group">
                        <input type="password" name="npassword" class="form-control" placeholder="Nueva contraseña"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="cnpassword" class="form-control" placeholder="Confirmar Nueva contraseña"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="enviar" class="btn btn-success float-right">Cambiar Contraseña</button>
                    </div>
                    </form>

                    <br><br><br>

                    <form method="post" action="">
                    <label>Cambio de Firma</label>
                    <div class="form-group">
                        <input type="text" value="<?=$r['firma']?>" name="firma" class="form-control" placeholder="Nueva Firma"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="cfirma" class="btn btn-success float-right">Cambiar Firma</button>
                    </div>
                    </form>

                    <br><br><br>

                    <form method="post" action="">
                    <label>¿Foro Inactivo?</label>
                    <div class="form-group">
                       <select class="form-control" name="especial">
                        <option <?php if($r['especial'] == 1) echo "selected"; ?> value="1">SI</option>
                        <option <?php if($r['especial'] == 0) echo "selected"; ?> value="0">No</option>
                      </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="foro" class="btn btn-success float-right">Cambiar Información</button>
                        <button type="reset" onclick="window.location='?p=pagos'" class="btn btn-danger float-left">Regresar</button>
                    </div>
                    </form>


                    
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".dropdown-menu li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

function prepararUsuario(id, nombre){

    $("#kekox").css("opacity","1");

    $("#nombreHabbo").html(nombre);
    $("#user").val(id);


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox").attr("src",url);
                });

}

function copiar() {
  var $temp = $("#temp");
  $temp.val("<?=$mision?>").select();
  document.execCommand("copy");
  $temp.remove();

}
</script>