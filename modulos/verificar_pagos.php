<?php

//Deshabilitado
//alert("Este modulo ha sido deshabilitado","./",0);

if(!is_admin_or_more(rol($_SESSION['id']))){
  alert("No tienes acceso a visualizar este modulo","./",0);
}

if(isset($pagar)){

    $fecha = date("Y-m-d H:i:s");

    $ascensos = cant_ascensos($pagar);
    $times = cant_times_validos_user($pagar);
    $horas = horas_trabajadas($pagar);


    $q = $pdo->prepare("INSERT INTO pagas (id_dio, id_recibe, ascensos, times, horas, fecha, status) VALUES (:idd, :idr, :ascensos, :times, :horas, :fecha, 1)");
    $q->execute([
        $_SESSION['id'],
        $pagar,
        $ascensos,
        $times,
        $horas,
        $fecha
    ]);

    reiniciar_user($pagar);

    alert("Se le ha pagado a la persona satisfactoriamente","?p=pagos",1);
}

if(isset($rechazar)){

    $fecha = date("Y-m-d H:i:s");

    $ascensos = cant_ascensos($pagar);
    $times = cant_times_validos_user($pagar);
    $horas = horas_trabajadas($pagar);


    $q = $pdo->prepare("INSERT INTO pagas (id_dio, id_recibe, ascensos, times, horas, fecha, status) VALUES (:idd, :idr, :ascensos, :times, :horas, :fecha, 0)");
    $q->execute([
        $_SESSION['id'],
        $rechazar,
        $ascensos,
        $times,
        $horas,
        $fecha
    ]);

    
    reiniciar_user($rechazar);

    alert("Se le ha rechazado el pago a la persona satisfactoriamente","?p=pagos",1);

}

if(isset($corte) && $corte == true){
  if(is_manager_or_more(rol($_SESSION['id']))){
    $q = $pdo->prepare("SELECT id FROM users WHERE rol > 4");
    $q->execute();
    while($r = $q->fetch()){
      $fecha = "%".date("Y-m-d")."%";
      $qs = $pdo->prepare("SELECT id FROM pagas WHERE fecha LIKE :fecha AND id_recibe = :id");
      $qs->execute([
        $fecha,
        $r['id']
      ]);

      if($qs->rowCount()==0){
        if(tiene_save($r['id'])){
          if(status_pago($r['id']) == 1){
            $qf = $pdo->prepare("UPDATE users SET save_acumulado = save_acumulado + 1, tipo_acumulado = 1 WHERE id = :id");
            $qf->execute([
              $r['id']
            ]);
          }elseif(status_pago($r['id']) == 2){
            $qf = $pdo->prepare("UPDATE users SET save_acumulado = save_acumulado + 1, tipo_acumulado = 2 WHERE id = :id");
            $qf->execute([
              $r['id']
            ]);
          }
        }

        $fecha = date("Y-m-d H:i:s");

        $qr = $pdo->prepare("INSERT INTO pagas (id_dio, id_recibe, ascensos, times, horas, fecha, status) VALUES (:idd, :idr, 0, 0, 0, :fecha, 0)");
        $qr->execute([
            $_SESSION['id'],
            $r['id'],
            $fecha
        ]);


        reiniciar_user($r['id']);
      }

    }

    $fecha = date("Y-m-d")." 15:00:00";

    $qf = $pdo->prepare("UPDATE ascensos SET pagado = 0 WHERE created_at >= :fecha");
    $qf->execute([
      $fecha
    ]);

    $qf2 = $pdo->prepare("UPDATE times SET pagado = 0, pagado_recibe = 0 WHERE created_at >= :fecha");
    $qf2->execute([
      $fecha
    ]);

  }else{
    alert("No tienes permiso para hacer esta acción","?p=pagos",0);
  }
}

 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h3><center>Lista de todos los usuarios</center></h3><br><br>
            <center><button class="btn btn-danger" onclick="cortePaga()">Hacer corte de paga</button></center><br><br>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dts" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Avatar</th>
                        <th>Nombre</th>
                        <th>Rango</th>
                        <th>Ascensos</th>
                        <th>Horas de Times Asignados</th>
                        <th>Hrs. Trabajadas</th>
                        <th>¿Asc. Pend.?</th>
                        <th>¿Tiene Save?</th>
                        <th>Status</th>
                        <th>Creditos a Pagar</th>
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






    <div class="modal fade" id="pagar_ascenso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Pagar Con Ascenso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="res">

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" onclick="pagar_ascenso()" class="btn btn-primary">Pagar</button>
      </div>
    </div>
  </div>
</div>






    <div style="position:fixed; width: 100%; height: 100vh; z-index: 99999; background: rgba(255,255,255, 0.8); left:0; top: 0; display: none; justify-content: center; align-items: center;" id="loading">
      <img src="./imgs/loading.gif"/>
    </div>
    <!-- /.content -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

      function cortePaga(){
        Swal.fire({
          title: 'Estás seguro de hacer el corte de paga?',
          text: "Si lo haces se reiniciara el progreso de todos los usuarios!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, Hacer corte!',
          cancelButtonText: 'Cancelar',
        }).then((result) => {
          if (result.isConfirmed) {
           window.location="?p=verificar_pagos&corte=true";
          }
        })
      }

     
    
    
    
    
    
    
      function dar_placa_paga(id){
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
      
      $("#paga"+id).attr("onclick","quitar_placa_paga("+id+")");
      $("#paga"+id).attr("class","badge badge-danger");
      $("#paga"+id).attr("style","background:black; cursor:pointer;");
      $("#paga"+id).html("Placa Paga");
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
    
    function quitar_placa_paga(id){
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
      
      $("#paga"+id).attr("onclick","dar_placa_paga("+id+")");
      $("#paga"+id).attr("class","badge badge-secondary");
      $("#paga"+id).attr("style","cursor:pointer;");
      $("#paga"+id).html("No Placa Paga");
      },
      success : function(){
        $("#loading").fadeOut();
        Swal.fire(
          'Bien Hecho!',
          'Le has quitado placa de paga a este usuario!',
          'success'
        )
      }
    });

  }


function dar_placa_boni(id){
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
      
       $("#boni"+id).attr("onclick","quitar_placa_boni("+id+")");
       $("#boni"+id).attr("class","badge badge-warning");
       $("#boni"+id).html("Placa Boni");
      $("#loading").fadeOut();
      Swal.fire(
        'Bien Hecho!',
        'Le has marcado placa de boni a este usuario!',
        'success'
      )
    }
  });

}


function quitar_placa_boni(id){
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
      
      $("#boni"+id).attr("onclick","dar_placa_boni("+id+")");
      $("#boni"+id).attr("class","badge badge-secondary");
      $("#boni"+id).html("No Placa Boni");
      $("#loading").fadeOut();
      Swal.fire(
        'Bien Hecho!',
        'Le has quitado placa de boni a este usuario!',
        'success'
      )
    }
  });

}

function pagar_creditos(id){
  Swal.fire({
          title: 'Estás seguro de pagar a esta persona?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, Pagar!',
          cancelButtonText: 'Cancelar',
        }).then((result) => {
          if (result.isConfirmed) {

            var parametros = {
              "id" : id
            };

            $.ajax({
              data : parametros,
              type : "post",
              url : "./ajax/pagar_user.php",
              success:function(response){
                Swal.fire(
                  'Bien Hecho!',
                  'Has pagado con creditos satisfactoriamente!',
                  'success'
                )

                $("#pagar"+id).parent().parent().fadeOut();
              }
            })

           
          }
        })
}

function no_pagar_user(id){
  Swal.fire({
          title: 'Estás seguro de  rechazar el pago a esta persona?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si, Rechazar!',
          cancelButtonText: 'Cancelar',
        }).then((result) => {
          if (result.isConfirmed) {

            var parametros = {
              "id" : id
            };

            $.ajax({
              data : parametros,
              type : "post",
              url : "./ajax/no_pagar_user.php",
              success:function(response){
                Swal.fire(
                  'Bien Hecho!',
                  'Has rechazado el pago satisfactoriamente!',
                  'success'
                )

                $("#pagar"+id).parent().parent().fadeOut();
              }
            })

           
          }
        })
}

function cargar_ascenso(id){
        var parametros = {
          "id" : id
        };
        $.ajax({
          data : parametros,
          type : "post",
          url : "./ajax/cargar_ascenso.php",
          success:function(response){
            $("#res").html(response);
          },
          beforeSend:function(){
            $("#res").html('<center><img src="./imgs/loading.gif" style="width:100px;"/></center>');
          }
        })
      }

      function pagar_ascenso(){
        var id = $("#id_asc").val();
        var rol = $("#rol_asc").val();

        if(rol.length>0){

          var parametros = {
            "id" : id,
            "rol" : rol
          };

          $.ajax({
            data : parametros,
            type : "post",
            url : "./ajax/pagar_ascenso.php",
            success:function(res){
              Swal.fire(
                    'Bien Hecho!',
                    'Has pagado con ascenso satisfactoriamente!',
                    'success'
                  )

                  $("#pagar"+id).parent().parent().fadeOut();
                  
                  $("#res").html(res);
            }
          })
        }
      }


$(document).ready(function() {
    $('.dts').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "ajax/lista_pagos_serverside.php",
    } );
} );
    </script>