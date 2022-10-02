<?php
if(!is_admin_or_more(rol($_SESSION['id']))){
  alert("No tienes acceso a este modulo","./",0);
}

$ultimas = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"). "-7 days"));

echo $ultimas;

$ascensos = $pdo->prepare("SELECT count(a.id) as cant, a.id_dio FROM ascensos a, users b WHERE a.id_dio = b.id AND a.pagado = 0 AND a.created_at >= :ultimas AND b.rol BETWEEN 5 AND 60 GROUP BY a.id_dio ORDER BY cant DESC LIMIT 10");
$ascensos->execute([
  $ultimas
]);

$diotimes = $pdo->prepare("SELECT sum(TIMESTAMPDIFF(HOUR, a.created_at, a.ended_at)) as cant, a.id_dio FROM times a, users b WHERE a.id_dio = b.id AND a.pagado = 0 AND a.is_timing = 0 AND a.created_at >= :ultimas AND b.rol BETWEEN 5 AND 60 GROUP BY a.id_dio ORDER BY cant DESC LIMIT 10");
$diotimes->execute([
  $ultimas
]);

$recibiotimes = $pdo->prepare("SELECT sum(TIMESTAMPDIFF(HOUR, a.created_at, a.ended_at)) as cant, a.id_recibe FROM times a, users b WHERE a.id_recibe = b.id AND pagado_recibe = 0 AND a.is_timing = 0 AND a.created_at >= :ultimas AND b.rol BETWEEN 5 AND 60 GROUP BY a.id_recibe ORDER BY cant DESC LIMIT 10");
$recibiotimes->execute([
  $ultimas
]);

$ascensos1 = $pdo->prepare("SELECT count(a.id) as cant, a.id_dio FROM ascensos a, users b WHERE a.id_dio = b.id AND a.pagado = 0 AND a.created_at >= :ultimas AND b.rol BETWEEN 5 AND 60 GROUP BY a.id_dio ORDER BY cant DESC LIMIT 1");
$ascensos1->execute([
  $ultimas
]);

$diotimes1 = $pdo->prepare("SELECT sum(TIMESTAMPDIFF(HOUR, a.created_at, a.ended_at)) as cant, a.id_dio FROM times a, users b WHERE a.id_dio = b.id AND pagado = 0 AND a.is_timing = 0 AND a.created_at >= :ultimas AND b.rol BETWEEN 5 AND 60 GROUP BY a.id_dio ORDER BY cant DESC LIMIT 1");
$diotimes1->execute([
  $ultimas
]);

$recibiotimes1 = $pdo->prepare("SELECT sum(TIMESTAMPDIFF(HOUR, a.created_at, a.ended_at)) as cant, a.id_recibe FROM times a, users b WHERE a.id_recibe = b.id AND pagado_recibe = 0 AND a.is_timing = 0 AND a.created_at >= :ultimas AND b.rol BETWEEN 5 AND 60 GROUP BY a.id_recibe ORDER BY cant DESC LIMIT 1");
$recibiotimes1->execute([
  $ultimas
]);

$rascensos1 = $ascensos1->fetch();
$rdiotimes1 = $diotimes1->fetch();
$rrecibiotimes1 = $recibiotimes1->fetch();
?>
 <!-- Content Header (Page header) -->

<br><br>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row ">
          <!-- left column -->
          <div class="col-md-4">
            <!-- general form elements -->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Mejores Timmers</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">
                  <div class="form-group">

                  <center>
                    <span title="<?=nombre_habbo($rdiotimes1['id_dio'])?>" data-toggle="tooltip"><?=keko_user($rdiotimes1['id_dio'],100,'wav',3)?></span>
                        <br><br>
                    </center>

                

                      <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Posicion</th>
                                <th>Avatar</th>
                                <th>Habbo</th>
                                <th>Horas Asign</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count1 = 1;
                          while($rdiotimes=$diotimes->fetch()){
        
                            ?>
                    
                                    <tr>
                                        <td><?=$count1?></td>
                                        <td><?=keko_user($rdiotimes['id_dio'])?></td>
                                        <td><?=nombre_habbo($rdiotimes['id_dio'])?></td>
                                        <td><?=$rdiotimes['cant']?></td>
                                    </tr>

                                    <?php
                                    $count1++;
                    
                                  }
                           ?>
                        </tbody>
                    </table>    
                   
                 
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!--/.col (right) -->
        </div>















        <div class="col-md-4">
            <!-- general form elements -->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Mejores Trabajadores</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">
                  <div class="form-group">
                      

                  <center>
                    <span title="<?=nombre_habbo($rrecibiotimes1['id_recibe'])?>" data-toggle="tooltip"><?=keko_user($rrecibiotimes1['id_recibe'],100,'wav',3)?></span>
                        <br><br>
                    </center>

                

                      <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Posicion</th>
                                <th>Avatar</th>
                                <th>Habbo</th>
                                <th>Horas Trab.</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $cont2 = 1;
                          while($rrecibiotime=$recibiotimes->fetch()){
        
                            ?>
                    
                                <tr>
                                    <td><?=$cont2?></td>
                                    <td><?=keko_user($rrecibiotime['id_recibe'])?></td>
                                    <td><?=nombre_habbo($rrecibiotime['id_recibe'])?></td>
                                    <td><?=$rrecibiotime['cant']?></td>
                                </tr>

                                    <?php
                    
                    $cont2++;
                                  }
                                  
                           ?>
                        </tbody>
                    </table>      
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

          </div>
          <!--/.col (right) -->
        </div>





        








        <div class="col-md-4">
            <!-- general form elements -->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Mejores Ascendedores</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body">
                  <div class="form-group">
                      

                    <center>
                    <span title="<?=nombre_habbo($rascensos1['id_dio'])?>" data-toggle="tooltip"><?=keko_user($rascensos1['id_dio'],100,'wav',3)?></span>
                        <br><br>
                    </center>
                   
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Posicion</th>
                                <th>Avatar</th>
                                <th>Habbo</th>
                                <th>Ascensos Real.</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $count3 = 1;
                                while($rascensos = $ascensos->fetch()){
                                    ?>
                                    <tr>
                                        <td><?=$count3?></td>
                                        <td><?=keko_user($rascensos['id_dio'])?></td>
                                        <td><?=nombre_habbo($rascensos['id_dio'])?></td>
                                        <td><?=$rascensos['cant']?></td>
                                    </tr>
                                    <?php
                                    
                                    $count3++;
                                  }
                            ?>
                        </tbody>
                    </table>    
                                
                </div>
                <!-- /.card-body -->

             
            </div>
            <!-- /.card -->

          </div>
          <!--/.col (right) -->
        </div>















        































          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->