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
                <h3 class="card-title">Detalles de <?=$rh['habbo']?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
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

                          <br>
                          <center>
                          <a data-toggle="tooltip" title="Pagar save con creditos" href="?p=ver_detalles_habbo&id=<?=$id?>&pagar_save=1"><i class="fas fa-check"></i></a>
                          &nbsp; &nbsp;
                          <a data-toggle="tooltip" title="Pagar save con ascenso" href="?p=pagar_save_ascenso&id=<?=$id?>"><i class="fas fa-crown"></i></a>
                          </center>
                        </div>
                      
                        <?php
                      }
                    ?>
                    <center>
                        <?=keko_user($id,200)?>
                        <br><br>

                        <center>Mision</center>
                    <center style="font-size: 20px; color: red;" id="firma">
                        <?=$mision?>
                    </center>

                   

                    

                    <br>
                    
                    <center>
                        <button type="reset" onclick="copiar()" class="btn btn-success">Copiar Mision</button>
                    </center>

                    <br><br>

                    <center>

<?php
if(status_pago($id) >= 1){
?>
<img style="width: 50px;" src="./imgs/paga.png"/>
<?php
}

if(status_pago($id) == 2){
?>
<img style="width: 50px;" src="./imgs/boni.png"/>
<?php 
}

if(tiene_save($id)){
?>
<img style="width: 50px;" src="./imgs/save.png"/>
<?php
}

if(tiene_fila($id)){
?>
<img style="width: 50px;" src="./imgs/fila.png"/>
<?php
}

if(tiene_vip($id)){
?>
<img style="width: 50px;" src="./imgs/vip.png"/>
<?php
}

if(tiene_pase($id)){
?>
<img style="width: 50px;" src="./imgs/pase.png"/>
<?php
}
?>
</center>

<br><br>

<center>
  <h2>Proximo Ascenso</h2><br>
  <span style="position:relative; top: -30px;">
  <?php
    if(requiere_ascenso($id)){
      if(is_admin_or_more(rol($id))){
        echo "--";
      }else{ 
        echo "<span class='badge badge-success'>Ascenso disponible</span>";
      }
    }else{
      echo "<span class='badge badge-danger'>".proximo_ascenso($id)."</span>";
    }
  ?>
</span>
</center>

<center>
  <h2>¿Foro Inactivo?</h2><br>
  <span style="position:relative; top: -30px;">
  <?php
    if(es_especial($id)){
      echo "<span class='badge badge-success'>SI</span>";
    }else{
      echo "<span class='badge badge-danger'>NO</span>";
    }
  ?>
</span>
</center>
                    

                    <input style="position:fixed;left:-100000px;right:10000px;" id="temp"/>
                    </center>
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
                            <?=tabla_ascensos_recibidos_user($id)?>
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
                            <?=tabla_ascendidos_user($id)?>
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
                            <?=tabla_horas_user($id)?>
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
                            <?=tabla_times_user($id)?>
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