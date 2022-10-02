<style>
#myInput {
    padding: 20px;
    margin-top: -6px;
    border: 0;
    border-radius: 0;
    background: #f1f1f1;
  }

  .dropdown-menu li a{
      padding-left:10px;
      color: #333;
  }
    </style>

<?php
$q = $pdo->prepare("SELECT * FROM ascensos WHERE id_dio = :id ORDER BY id DESC LIMIT 1");
$q->execute([$_SESSION['id']]);
$r = $q->fetch();

$qh = $pdo->prepare("SELECT * FROM users WHERE id = :id_recibe");
$qh->execute([$r['id_recibe']]);
$rh = $qh->fetch();

if(is_tecnico_or_more(rol($rh['id']))){
  $mision = $prefix_firma."-  ".nombre_rol_usuario($rh['id'])." -".firma($_SESSION['id'])." -".firma($rh['id']);
}else{
  $mision = $prefix_firma."-  ".nombre_rol_usuario($rh['id'])." -".firma($_SESSION['id']);
}

 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-12">
          <div class="col-sm-12">
            <h3><center>Mi Ultimo Ascenso</center></h3>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row " style="display: flex; justify-content: center;">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Información del ascenso</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <div class="card-body">
                  <div class="form-group">
                    <center>
                        <?=keko_user($r['id_recibe'])?>
                    </center>
                    <center>
                        <label><?=$rh['habbo']?></label>
                    </center>

                    <center>Nueva Mision</center>
                    <center style="font-size: 20px; color: red;" id="firma">
                        <?=$mision?>
                    </center>

                    <br>
                    
                    <center>
                        <button type="reset" onclick="copiar()" class="btn btn-success">Copiar Mision</button>
                    </center>

                    <input style="position:fixed;left:-100000px;right:10000px;" id="temp"/>


                    
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="reset" onclick="window.history.back();" name="enviar" class="btn btn-primary">Regresar</button>
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