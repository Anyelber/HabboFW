
<?php

if(!is_admin_or_more(rol($_SESSION['id']))){
  alert("No tienes acceso a visualizar este modulo","./",0);
}

$id = clear($id);


if(isset($pagar)){
  if(is_admin_or_more(rol($_SESSION['id']))){
    $user2 = clear($user2);
    $new_rol = clear($new_rol);

    $qu = $pdo->prepare("SELECT * FROM users WHERE id = :user");
    $qu->execute([$id]);

    $ru = $qu->fetch();

    $antiguo_rol = $ru['rol'];

    if($antiguo_rol >= $new_rol){
      alert("Debes seleccionar un rol inferior al actual (".nombre_rol($ru['rol']).")","",0);
    }

    

    $ascensos = cant_ascensos($pagar);
    $times = cant_times_validos_user($pagar);
    $horas = horas_trabajadas($pagar);

    $fecha = date("Y-m-d H:i:s");

    $q2 = $pdo->prepare("INSERT INTO pagas (id_dio, id_recibe, ascensos, times, horas, fecha, status, tipo, rol_pago) VALUES (:id_dio, :id_recibe, :ascensos, :times, :horas, :fecha, 1, 1, :new_rol)");
    $q2->execute([
        $_SESSION['id'],
        $id,
        $ascensos,
        $times,
        $horas,
        $fecha,
        $new_rol
    ]);

    $q = $pdo->prepare("INSERT INTO ascensos (id_dio, id_recibe, old_rol, new_rol, created_at, tipo) VALUES (:id_dio, :id_recibe, :old_rol, :new_rol, NOW(), 2)");
    $q->execute([
        $_SESSION['id'],
        $id,
        $antiguo_rol,
        $new_rol
    ]);

    $qf = $pdo->prepare("UPDATE users SET rol = :rol WHERE id = :id");
    $qf->execute([$new_rol, $id]);

    reiniciar_user($id);

    alert("Se ha ascendido a ".$ru['habbo']." Satisfactoriamente.","?p=mi_ultimo_ascenso",1);
  }else{
    alert("No tienes permisos para realizar esta acción","",0);
  }
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Pagar con Ascenso</h3>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
      <div class="row" style="display: flex; justify-content: center;">
          <!-- left column -->

     




          <div class="col-md-6">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Pagar con Ascenso</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <center>
                <div class="card-body">
                  <div class="form-group">

                   
                  </div>

                <?=keko_user($id,100)?>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo2"><?=nombre_habbo($id)?></span><br>
                  <span><?=rango_habbo($id)?></span>
          <br><br>
                  <div class="form-group" style="width:40%">
                    <select id="new_rol" required class="form-control" name="new_rol" id="exampleInputEmail1">
                        <option value="">Seleccione un nuevo Rango</option>
                        <?=options_roles()?>
                    </select>
                  </div>
                
                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                  <button type="submit" name="pagar" class="btn btn-success">Pagar</button>
                </div>
                
              </form>
            </div>
            <!-- /.card -->

          </div>







          
          <!--/.col (right) -->
        </div>
        <!-- /.row -->

            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->