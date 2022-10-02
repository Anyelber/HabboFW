<?php

if(!is_founder_or_more(rol($_SESSION['id']))){
  alert("No tienes permiso","./",0);
}

if(isset($enviar)){
    $titulo = clear($titulo);
    $texto = clear($texto);

    $permitidos = array("jpg","png","jpeg");

    $imagen = "";

    if(is_uploaded_file($_FILES['imagen']['tmp_name'])){
        $path = pathinfo($_FILES['imagen']['name']);
        $ext = strtolower($path['extension']);

        if(in_array($ext,$permitidos)){
            $imagen = md5(date("Y-m-d H:i:s").$_SESSION['id']).".".$ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], "./imgs_noticias/".$imagen);
        }else{
            alert("La imagen que intentas subir no es permitida, solo se soportan imagenes JPG, PNG, JPEG","",0);
        }
    }

    $fecha = date("Y-m-d H:i:s");

    $text = nl2br($text);

    $q = $pdo->prepare("INSERT INTO noticias (id_user, texto, titulo, imagen, fecha) VALUES (:id_user, :texto, :titulo, :imagen, :fecha)");
    $q->execute([
        $_SESSION['id'],
        $texto,
        $titulo,
        $imagen,
        $fecha
    ]);

    alert("Se ha publicado la noticia.","./",1);
}

 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Crear Noticia</h3>
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
            <div class="card card-danger" >
              <div class="card-header">
                <h3 class="card-title">Informacion de la Noticia</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="post" action="" enctype="multipart/form-data">
                <div class="card-body">

                <div class="form-group">
                    <input type="text" name="titulo" class="form-control" placeholder="Titulo" required/>
                </div>

                <div class="form-group">
                    <textarea class="form-control" name="texto" placeholder="Contenido de la noticia" rows="6"></textarea>
                </div>

                <div class="form-group">
                    <label>Imagen (Opcional)</label>
                    <input type="file" name="imagen" class="form-control" placeholder="Imagen"/>
                </div>




                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                    <center>
                        <button type="submit" name="enviar" class="btn btn-danger">Publicar</button>
                    </center>
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
