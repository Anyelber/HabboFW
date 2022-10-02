<?php

if(!is_admin_or_more(rol($_SESSION['id']))){
  alert("No tienes permisos para acceder a este modulo","./",0);
}

if(isset($enviar)){
  $peticion = clear($peticion);

  $fecha = date("Y-m-d H:i:s");

  $q = $pdo->prepare("INSERT INTO peticiones (id_user, peticion, fecha, status) VALUES (:idu, :peticion, :fecha, 0)");
  $q->execute([
      $_SESSION['id'],
      $peticion,
      $fecha
  ]);

  alert("Se ha enviado la peticion.","?p=peticiones",1);
}

if(isset($eliminar)){
  if($_SESSION['id'] == 1){

    $eliminar = clear($eliminar);

    $q = $pdo->prepare("UPDATE peticiones SET deleted = 1 WHERE id = :id");

    $q->execute([
      $eliminar
    ]);

    alert("Se ha eliminado esta peticion","?p=peticiones",1);
  }else{
    alert("No tienes permisos","./",0);
  }
}

if(isset($completado)){
  if($_SESSION['id'] == 1){

    $completado = clear($completado);

    $q = $pdo->prepare("UPDATE peticiones SET status = 1 WHERE id = :id");

    $q->execute([
      $completado
    ]);

    alert("Se ha marcado como completado esta peticion","?p=peticiones",1);
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
            <h3>Peticiones</h3>
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
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Peticion</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                
                <div class="card-body">
                  <div class="form-group">
                    

                    <div class="form-group">
                        <label>Describa la petición</label>
                        <textarea required class="form-control" rows="5" name="peticion"></textarea>
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

         

          <table class="table table-striped">
            <tr>
              <th>Creador</th>
              <th>peticion</th>
              <th>Fecha</th>
              <th>Status</th>
              <?php
                if($_SESSION['id'] == 1){
              ?>
              <th>Acciones</th>
              <?php
                }
              ?>
            </tr>
            <?=tabla_peticiones()?>
          </table>


      




          
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </div>

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->


<script>

</script>