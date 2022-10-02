<?php

if(isset($enviar)){
    $opassword = clear($opassword);
    $eopassword = md5($opassword);

    $npassword = clear($npassword);
    $cnpassword = clear($cnpassword);

    $check = $pdo->prepare("SELECT * FROM users WHERE password = :eopassword AND id = :id");
    $check->execute([$eopassword, $_SESSION['id']]);

    if($check->rowCount() == 0){
        alert("Tu antigua contraseña no es correcta","",0);
    }

    if($npassword != $cnpassword){
        alert("Las nuevas contraseñas no coinciden","",0);
    }

    $enpassword = md5($npassword);

    $q = $pdo->prepare("UPDATE users set password = :pw WHERE id = :id");
    $q->execute([
        $enpassword,
        $_SESSION['id']
    ]);

    alert("Se ha cambiado la contraseña","?p=perfil",1);
}

if(isset($enviar2)){
  $user = clear($user);

  $q = $pdo->prepare("UPDATE users SET user = :user WHERE id = :id");
  $q->execute([
    $user,
    $_SESSION['id']
  ]);

  alert("Has modificado tu nombre de usuario","",1);
}

$qh = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$qh->execute([$_SESSION['id']]);
$rh = $qh->fetch();
 ?>
 <!-- Content Header (Page header) -->

<br><br>

<form method="post" action="">
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row " style="display: flex; justify-content: center;">
          <!-- left column -->
          <div class="col-md-8">
            <!-- general form elements -->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Modificar Cuenta</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">
                <div class="form-group">
                    <center>
                        <?=keko_user($_SESSION['id'],100,'sit',2)?>
                    </center>

                    <h2>Cambiar contraseña</h2>
                    <br><br>
                    <div class="form-group">
                        <input type="password" name="opassword" class="form-control" placeholder="Antigua contraseña"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="npassword" class="form-control" placeholder="Nueva contraseña"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="cnpassword" class="form-control" placeholder="Confirmar Nueva contraseña"/>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="enviar" class="btn btn-success float-right">Cambiar Contraseña</button>
                        <button type="reset" onclick="window.location='?p=pagos'" class="btn btn-danger float-left">Regresar</button>
                    </div>

                    
                </div>
</form>
<br><br>
<br><br>
<form method="post" action="">


                <div class="form-group">
                  
                <h2>Cambiar Nombre de Usuario</h2>
                   
                    <div class="form-group">
                        <input type="text" name="user" class="form-control" placeholder="Nombre de usuario" value="<?=$rh['user']?>"/>
                    </div>
                   
                    <div class="form-group">
                        <button type="submit" name="enviar2" class="btn btn-success float-right">Cambiar Nombre de Usuario</button>
                        <button type="reset" onclick="window.location='?p=pagos'" class="btn btn-danger float-left">Regresar</button>
                    </div>

                    
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
</form>

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