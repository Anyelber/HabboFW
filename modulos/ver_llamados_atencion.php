<?php

$id = clear($id);


$qh = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$qh->execute([$id]);
$rh = $qh->fetch();


if(isset($pagar_save) && $pagar_save == 1){
  $fecha = date("Y-m-d H:i:s");


  $q = $pdo->prepare("INSERT INTO pagas (id_dio, id_recibe,  fecha, status) VALUES (:idd, :idr, :fecha, 1)");
  $q->execute([
      $_SESSION['id'],
      $id,
      $fecha
  ]);

  $qf = $pdo->prepare("UPDATE users SET save_acumulado = 0, tipo_acumulado = 0 WHERE id = :id");
  $qf->execute([
    $id
  ]);


  alert("Se le ha pagado a la persona satisfactoriamente","?p=ver_detalles_habbo&id=".$id,1);
}

$q = $pdo->prepare("SELECT id_dio FROM ascensos WHERE id_recibe = :id ORDER BY id DESC LIMIT 1");
$q->execute([
  $rh['id']
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
                <h3 class="card-title">Llamados de atención de <?=$rh['habbo']?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <div class="card-body">
                  <div class="form-group">
                   
                    <center>
                        <?=keko_user($id,100)?>
                        <br><br>

                        <center>Mision</center>
                    <center style="font-size: 20px; color: red;" id="firma">
                        <?=$mision?>
                    </center>

                    

                 

                    <br><br>

                   
                    

                    <input style="position:fixed;left:-100000px;right:10000px;" id="temp"/>
                    </center>
                    <label class="badge badge-primary">Llamados de atencion registrados</label>
                    <table class="table table-striped dt">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dió</th>
                                <th>Texto</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Pruebas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=tabla_atencion_user($id)?>
                        </tbody>
                    </table>

                    
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="reset" onclick="window.history.back();" name="enviar" class="btn btn-danger">Regresar</button>
                </div>
              </form>
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