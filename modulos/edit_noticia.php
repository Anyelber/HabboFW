<?php

$id = clear($id);
$q = $pdo->prepare("SELECT * FROM noticias WHERE id = :id");
$q->execute([
    $id
]);

$r = $q->fetch();

if(!is_founder_or_more(rol($_SESSION['id']))){
  alert("No tienes permiso","./",0);
}

if(isset($enviar)){
    $titulo = clear($titulo);
    $texto = clear($texto);

    $permitidos = array("jpg","png","jpeg");

    $imagen = "";

  

    $fecha = date("Y-m-d H:i:s");

    $texto = nl2br($texto);

    $q = $pdo->prepare("UPDATE noticias SET texto = :texto, titulo = :titulo WHERE id = :id");
    $q->execute([
        $texto,
        $titulo,
        $id
    ]);

    if(is_uploaded_file($_FILES['imagen']['tmp_name'])){
        $path = pathinfo($_FILES['imagen']['name']);
        $ext = strtolower($path['extension']);

        if(in_array($ext,$permitidos)){
            $imagen = md5(date("Y-m-d H:i:s").$_SESSION['id']).".".$ext;
            move_uploaded_file($_FILES['imagen']['tmp_name'], "./imgs_noticias/".$imagen);

            $q2 = $pdo->prepare("UPDATE noticias SET imagen = :imagen WHERE id = :id");
            $q2->execute([
                $imagen,
                $id
            ]);
        }else{
            alert("La imagen que intentas subir no es permitida, solo se soportan imagenes JPG, PNG, JPEG","",0);
        }
    }



    alert("Se ha editado la noticia.","./",1);
}

if(isset($eliminar_imagen) && $eliminar_imagen == true){
    $eliminar_imagen = clear($eliminar_imagen);

    $q = $pdo->prepare("UPDATE noticias SET imagen = '' WHERE id = :id");
    $q->execute([
        $eliminar_imagen
    ]);

    alert("Se ha eliminado la imagen de la noticia","?p=edit_noticia&id=".$id,1);
}
 ?>
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Editar Noticia</h3>
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
                    <input type="text" value="<?=$r['titulo']?>" name="titulo" class="form-control" placeholder="Titulo" required/>
                </div>

                <div class="form-group">
                    <textarea class="form-control" name="texto" placeholder="Contenido de la noticia" rows="6"><?=$r['texto']?></textarea>
                </div>

                <div class="form-group">
                    <label>Imagen (Remplazar)</label>
                    <input type="file" name="imagen" class="form-control" placeholder="Imagen"/>
                    <br>
                    <?php
                              if(!empty($r['imagen']) && file_exists("./imgs_noticias/".$r['imagen'])){
                                ?>
                    <b>Imagen Actual</b><br>
                                  <img src="./imgs_noticias/<?=$r['imagen']?>" style="max-width: 80%"/><br><br>
                                  <a href="?p=edit_noticia&id=<?=$id?>&eliminar_imagen=true">[Eliminar imagen actual]</a>
                                <?php
                              }
                            ?>
                </div>




                </div>
                <!-- /.card-body -->
                </center>

                <div class="card-footer">
                    <center>
                        <button type="submit" name="enviar" class="btn btn-danger">Editar</button>
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


<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".menu1 li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});


$(document).ready(function(){
  $("#myInput2").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".menu2 li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

function prepararUsuario(id, nombre, rango){

  $("#ocultar").fadeOut();

    $("#kekox").css("opacity","1");

    $("#nombreHabbo").html(nombre);
    $("#user").val(id);

    $("#kekox").attr("src","imgs/loading.gif");

    $("#rango_habbo").html(rango);


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox").attr("src",url);
                });

}

function prepararUsuario2(id, nombre, rango){

  $("#new_rol").fadeIn();

  $("#ocultar2").fadeOut();

    $("#kekox2").css("opacity","1");

    $("#nombreHabbo2").html(nombre);
    $("#user2").val(id);

    $("#kekox2").attr("src","imgs/loading.gif");

    $("#rango_habbo2").html(rango);


                $.getJSON('https://www.habbo.es/api/public/users?name='+nombre, function(habbos){
                    url = "https://www.habbo.com/habbo-imaging/avatarimage?size=l&figure="+habbos.figureString;
                    $("#kekox2").attr("src",url);
                });

}
</script>