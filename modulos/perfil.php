<?php

$qh = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$qh->execute([$_SESSION['id']]);
$rh = $qh->fetch();

$q = $pdo->prepare("SELECT * FROM ascensos WHERE id_recibe = :id ORDER BY id DESC LIMIT 1");
$q->execute([
  $_SESSION['id']
]);

$r =$q->fetch(); 


if(is_tecnico_or_more(rol($rh['id']))){
  $mision = $prefix_firma."-  ".nombre_rol_usuario($rh['id'])." -".firma($r['id_dio'])." -".firma($rh['id']);
}else{
  $mision = $prefix_firma."-  ".nombre_rol_usuario($rh['id'])." -".firma($r['id_dio']);
}
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
                <h3 class="card-title">Mi Perfil</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">
                  <div class="form-group">
                  <?php
                      if($rh['save_acumulado'] > 0){
                        if($rh['tipo_acumulado'] == 1){
                          $color = "green";
                          $titulo = "Este Usuario acumula Paga";
                        }else{
                          $color = "gold";
                          $titulo = "Este Usuario acumula Paga + Boni";
                        }
                        ?>
                        <div data-toggle="tooltip" title="<?=$titulo?>" style="color: <?=$color?>; display: flex; position: relative; display: inline-block; align-items:center; justify-content:center;">
                          <i class="far fa-circle fa-5x" ></i>
                          
                          <span style="font-size: 40px; color: #333; position:absolute; top: 10%; left: 38%;"><?=$rh['save_acumulado']?></span>
                        </div>
                      
                        <?php
                      }
                    ?>
                    <a href="?p=change_password"><button type="reset" class="btn btn-default"><i class="fas fa-edit"></i> Modificar Cuenta</button></a>
                    <center>
                        <?=keko_user($_SESSION['id'],200)?>
                        <br><br>

                        <center>Mision</center>
                        <center style="font-size: 20px; color: red;" id="firma">
                        <?=$mision?>
                        </center>

                        <br>

                        <center>
                        <button type="reset" onclick="copiar()" class="btn btn-success">Copiar Mision</button>
                        </center>


                        <input style="position:fixed;left:-100000px;right:10000px;" id="temp"/>
                    </center>
                    
                    <br><br>
                    <label class="badge badge-primary">Ascensos Recibidos</label>
                    <table class="table table-striped dt">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Avatar</th>
                                <th>Nombre Habbo</th>
                                <th>Ascenso</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=tabla_ascensos_recibidos_user($_SESSION['id'])?>
                        </tbody>
                    </table>
                    <br><br>
                    <label class="badge badge-success">Ascensos Realizados</label>
                    <table class="table table-striped dt">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Avatar</th>
                                <th>Nombre Habbo</th>
                                <th>Ascenso</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=tabla_ascendidos_user($_SESSION['id'])?>
                        </tbody>
                    </table>
                    <br><br>
                    <label class="badge badge-primary">Horas Trabajadas</label>
                    <table class="table table-striped dt">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Avatar</th>
                                <th>Nombre Habbo Asigna</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Tiempo Transcurrido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=tabla_horas_user($_SESSION['id'])?>
                        </tbody>
                    </table>
                    <br><br>
                    <label class="badge badge-success">Times Realizados</label>
                    <table class="table table-striped dt">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Avatar</th>
                                <th>Nombre Habbo</th>
                                <th>Fecha</th>
                                <th>Tiempo Transcurrido</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=tabla_times_user($_SESSION['id'])?>
                        </tbody>
                    </table>
                   

                    
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="reset" onclick="window.location='?p=pagos'" name="enviar" class="btn btn-danger">Regresar</button>
                </div>
            </div>
            <!-- /.card -->

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

  Swal.fire(
    '',
    'Mision Copiada',
    'success'
  );

}
</script>