<?php

if(isset($enviar)){
  $tipo = clear($tipo);
  $texto = clear($texto);

  $fecha = date("Y-m-d H:i:s");

  if(is_uploaded_file($_FILES['imagen']['tmp_name'])){
    $file = pathinfo($_FILES['imagen']['name']);

    $permitidas = array("png","jpg","jpeg");

    $ext = strtolower($file['extension']);

    if(!in_array($ext,$permitidas)){
      alert("solo se permiten imagenes en PNG, JPEG, y JPG","?p=sugerencias",0);
    }
    
    $imagen = md5(date("d-m-Y-H-i-s").rand(0,99999)).".".$ext;

    move_uploaded_file($_FILES['imagen']['tmp_name'], "imgs_sugerencias/".$imagen);

  }else{
    $imagen = "";
  }

  $q = $pdo->prepare("INSERT INTO sugerencias (tipo, created_by, text, fecha, status, imagen, deleted) VALUES (:tipo, :id, :text, :feha, 0, :imagen, 0)");
  $q->execute([
      $tipo,
      $_SESSION['id'],
      $texto,
      $fecha,
      $imagen
  ]);

  alert("Se ha enviado la queja / sugerencia muchas gracias por contactarnos, en breves momentos un administrador se pondra en contacto contigo via foro web o habbo, quedate atento.","./",1);
}

if(isset($eliminar)){
  if(is_admin_or_more(rol($_SESSION['id']))){

    $eliminar = clear($eliminar);

    $q = $pdo->prepare("UPDATE sugerencias SET deleted = 1 WHERE id = :id");

    $q->execute([
      $eliminar
    ]);

    alert("Se ha eliminado esta queja / sugerencia","?p=sugerencias",1);
  }else{
    alert("No tienes permisos","./",0);
  }
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h3>Quejas / Sugrencias</h3>
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
        

      




          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-danger" style="min-height:459px">
              <div class="card-header">
                <h3 class="card-title">Queja / Sugerencia</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="" enctype="multipart/form-data">
                
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Seleccione una categoria</label>

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="0"/> Queja Foro Web
                    </div>


                    <div class="form-group">
                        <input required type="radio" name="tipo" value="1"/> Sugerencia Foro Web
                    </div>
                    

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="2"/> Queja Personal Administrativo
                    </div>
                    

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="3"/> Sugerencia Personal Administrativo
                    </div>
                    

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="4"/> Queja Sistema de Pagos
                    </div>
                    

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="5"/> Sugerencia Sistema de Pagos
                    </div>
                    

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="6"/> Queja Discord
                    </div>
                    

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="7"/> Sugerencia Discord
                    </div>
                    

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="8"/> Queja Radio
                    </div>
                    

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="9"/> Sugerencia Radio
                    </div>
                    

                    <div class="form-group">
                        <input required type="radio" name="tipo" value="10"/> Queja Trabajador
                    </div>

                    <div class="form-group">
                        <label>Describa la Queja o Sugerencia</label>
                        <textarea required class="form-control" rows="5" name="texto"></textarea>
                    </div>

                    <div class="form-group">
                      <label>Imagen (Opcional)</label>
                      <input type="file" name="imagen" class="form-control"/>
                    </div>



                   
                  </div>

                
                
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" name="enviar" class="btn btn-danger">Enviar</button>
                </div>
                
              </form>
            </div>
            <!-- /.card -->

          </div>

          <?php
            if(is_admin_or_more(rol($_SESSION['id']))){
          ?>

          <table class="table table-striped">
            <tr>
              <th>Creador</th>
              <th>Tipo</th>
              <th>Texto</th>
              <th>Fecha</th>
              <th>Acciones</th>
            </tr>
            <?=tabla_sugerencias()?>
          </table>

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

</script>