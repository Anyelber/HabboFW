 <?php
if(isset($enviar)){

  if(!pertenece_coca($habbo)){
    alert("Este habbo no pertenece a ".$habbo_name." o tiene su perfil privado","",0);
  }

   $user = clear($user);
   $password = clear($password);
   $epassword = md5($password);
   $rol = clear($rol);
   $habbo = clear($habbo);
   $especial = clear($especial);

   $check1 = $pdo->prepare("SELECT * FROM users WHERE user = :user");
   $check2 = $pdo->prepare("SELECT * FROM users WHERE habbo = :habbo");


   $check1->execute([$user]);
   $check2->execute([$habbo]);

   $cod = generar_codigo();

   if($check1->rowCount()>0){
     alert("Este usuario ya esta en uso. intenta con otro o busca en la tabla usuarios","",0);
   }

   if($check2->rowCount()>0){
     alert("Este nombre habbo ya esta registrado. verifica en la tabla usuarios","",0);
   }

   $firma = "";

   $q = $pdo->prepare("INSERT INTO users (user, `password`, rol, habbo, created_at, created_by, firma, codigo) VALUES (:user, :pw, 1, :habbo, NOW(), :id, :firma, :cod)");
    $q->execute([
        $user,
        $epassword,
        $habbo,
        $_SESSION['id'],
        $firma,
        $cod
    ]);

    alert("Se ha creado el usuario","",1);
 
}

if(isset($eliminar)){
  $eliminar = clear($eliminar);
  if(is_manager_or_more(rol($_SESSION['id']))){



    $q = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $q->execute([
      $eliminar
    ]);

    $q = $pdo->prepare("DELETE FROM ascensos WHERE id_dio = :id1 OR id_recibe = :id2");
    $q->execute([
      $eliminar,
      $eliminar
    ]);

    $q = $pdo->prepare("DELETE FROM times WHERE id_dio = :id1 OR id_recibe = :id2");
    $q->execute([
      $eliminar,
      $eliminar
    ]);

    alert("Has eliminado a este usuario","?p=usuarios",1);

  }else{
    alert("No tienes permiso para hacer esto","?p=usuarios",0);
  }
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Administrar Usuarios</h3>
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
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Información del usuario</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="">
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Usuario</label>
                    <input name="user" required type="text" class="form-control" id="exampleInputEmail1" placeholder="Usuario">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Clave</label>
                    <input name="password" required type="text" value="<?=generar_codigo()?>" class="form-control" id="exampleInputPassword1" placeholder="Clave">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Nombre del Habbo</label>
                    <p style="color:#f80">Este nombre debe ser bien escrito para poder usar correctamente las funciones</p>
                    <input name="habbo" required type="text" class="form-control" id="exampleInputEmail1" placeholder="Nombre del Keko">
                  </div>
                 

                  <div class="form-group" <?php if(!is_admin_or_more(rol($_SESSION['id']))){ echo "style='display:none'"; } ?>>
                    <label for="exampleInputEmail1">¿Foro Inactivo?</label><br>
                    <input name="especial" value="1" required  type="radio" > SI
                    &nbsp; &nbsp;
                    <input name="especial" value="0" required checked type="radio" > NO
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" name="enviar" class="btn btn-danger">Guardar</button>
                </div>
              </form>
            </div>
            <!-- /.card -->

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->


        <h3>Lista de usuarios</h3>
        <div class="row ">
          
          <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dts">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Habbo</th>
                        <th>Registrado Por</th>
                        <th>Usuario</th>
                        <th>Fecha Placa Seg</th>
                        <th>Rol</th>
                        <th>Firma</th>
                        <th>¿Foro Inactivo?</th>
                        <th>Placas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div style="position:fixed; width: 100%; height: 100vh; z-index: 99999; background: rgba(255,255,255, 0.8); left:0; top: 0; display: none; justify-content: center; align-items: center;" id="loading">
      <img src="./imgs/loading.gif"/>
    </div>
    <!-- /.content -->

<script>
  function marcarPaga(id){
    var parametros = {
      "id" : id,
      "token" : "adg090adhjaln1madogi1#a0d98adh!!"
    }
    $.ajax({
      data : parametros,
      url : "./ajax/marcarPaga.php",
      type : "post",
      beforeSend: function(){
        $("#loading").fadeIn();
        $("#loading").css("display","flex");
      },
      success : function(){
        $("#loading").fadeOut();
        Swal.fire(
          'Bien Hecho!',
          'Le has marcado placa de paga a este usuario!',
          'success'
        )
      }
    });

  }

  // $('#firma').blur(function() {
    
  //       var firma = $("#firma").val();
  //   var parametros = {
  //     "firma" : firma
  //   };

  //   $.ajax({
  //     data : parametros,
  //     url : "./ajax/check_firma.php",
  //     success:function(response){
  //       if(response == 0){
  //         Swal.fire(
  //           '¡Esta firma ya esta en uso!',
  //           'Trata con otra diferente',
  //           'danger'
  //         )
  //         $("#firma").val('');
          
  //       }
  //     }
  //   })
  //   });

  

  function marcarBoni(id){
    var parametros = {
      "id" : id,
      "token" : "adg090adhjaln1madogi1#a0d98adh!!"
    }
    $.ajax({
      data : parametros,
      url : "./ajax/marcarBoni.php",
      type : "post",
      beforeSend: function(){
        $("#loading").fadeIn();
        $("#loading").css("display","flex");
      },
      success : function(){
        $("#loading").fadeOut();
        Swal.fire(
          'Bien Hecho!',
          'Le has marcado placa de boni a este usuario!',
          'success'
        )
      }
    });

  }

  $(document).ready(function() {
    $('.dts').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "ajax/lista_users_serverside.php",
    } );
} );
</script>

<script>
      function eliminarusuario(id, nombre){
        Swal.fire({
            icon: 'warning',
          title: 'Estás seguro de querer eliminar a <span style="color:red">'+nombre+'</span>',
          showDenyButton: false,
          showCancelButton: true,
          confirmButtonText: 'Eliminar',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#d33',
        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            window.location="?p=usuarios&eliminar="+id
          } 
        })
      }
      </script>