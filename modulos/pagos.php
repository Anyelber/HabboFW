 <?php 
 if(!is_admin_or_more(rol($_SESSION['id']))){
    alert("No tienes acceso a visualizar este modulo","./",0);
}
    $q1 = $pdo->prepare("SELECT fecha FROM pagas WHERE status = 1 ORDER BY id ASC LIMIT 1");
    $q2 = $pdo->prepare("SELECT fecha FROM pagas WHERE status = 1 ORDER BY id DESC LIMIT 1");

    $q1->execute();
    $q2->execute();

    $r1 = $q1->fetch();
    $r2 = $q2->fetch();

    $min_fecha = date("Y-m-d",strtotime($r1['fecha']));
    $max_fecha = date("Y-m-d",strtotime($r2['fecha']));

 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h3><center>Lista de Pagos Realizados</center></h3><br><br>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    
    <div class="row">

    <div class="form-group col-sm-6 col-md-6 col-lg-6" style="padding-left:30px;">
        <label>Filtrar por fecha</label>
        <form method="post" action="">
        <div class="input-group" >
            <div class="form-outline">
                <input min="<?=$min_fecha?>" max="<?=$max_fecha?>" type="date" id="form1" class="form-control" name="date" />
            </div>
            <button type="submit" name="enviar" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </div>
</form>
    </div>

    </div>

    <br>
    <?php
        if(isset($date)){
            echo "<span style='color:red;padding-left:30px;'>Filtrando por ".date("d/m/Y", strtotime($date))."</span><br><br>";
        }
    ?>
    <br>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dt" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Avatar</th>
                        <th>Nombre</th>
                        <th>Tipo de Pago</th>
                        <th>Nuevo Rango</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        if(isset($enviar)){
                            tabla_pagos_realizados($date);
                        }else{
                            tabla_pagos_realizados();
                        }
                    ?>
                </tbody>
            </table>
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->









    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h3><center>Lista de Pagos Rechazados</center></h3><br><br>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    
    <div class="row">

    <div class="form-group col-sm-6 col-md-6 col-lg-6" style="padding-left:30px;">
        <label>Filtrar por fecha</label>
        <form method="post" action="">
        <div class="input-group" >
            <div class="form-outline">
                <input min="<?=$min_fecha?>" max="<?=$max_fecha?>" type="date" id="form1" class="form-control" name="date2" />
            </div>
            <button type="submit" name="enviar2" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </div>
</form>
    </div>

    </div>

    <br>
    <?php
        if(isset($date2)){
            echo "<span style='color:red;padding-left:30px;'>Filtrando por ".date("d/m/Y", strtotime($date))."</span><br><br>";
        }
    ?>
    <br>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row ">
            
            <div class="col-sm-12 col-md-12 col-lg-12">
            <table class="table table-striped dt" >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Avatar</th>
                        <th>Nombre</th>
                        <th>Rango</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        if(isset($enviar2)){
                            tabla_pagos_rechazados($date2);
                        }else{
                            tabla_pagos_rechazados();
                        }
                    ?>
                </tbody>
            </table>
            </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->