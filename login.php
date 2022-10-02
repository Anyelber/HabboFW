<?php
include "./configs/configs.php";
include "./configs/functions.php";
require_once('anti_ddos/start.php'); 

if(isset($_SESSION['id'])){
  redir("./dashboard.php");
}

if(isset($enviar)){
  $user = clear($user);
  $password = clear($password);
  $epassword = md5($password);

  $q = $pdo->prepare("SELECT * FROM users WHERE user = :user AND `password` = :pw");
  $q->execute([$user,$epassword]);

  if($q->rowCount()>0){
    $r = $q->fetch();
    $_SESSION['id'] = $r['id'];
    set_last_visit($_SESSION['id']);
    
    $ip = $_SERVER['REMOTE_ADDR'];

    $qf = $pdo->prepare("UPDATE users SET ip = :ip WHERE id = :id");
    $qf->execute([
      $ip,
      $_SESSION['id']
    ]);
    
    redir("./dashboard.php");
  }else{
    alert("Datos incorrectos","",0);
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=$habbo_name?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="./plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="./plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./dist/css/adminlte.min.css">
  
  <meta name="description" contet="<?=$habbo_name?> es la mejor agencia de Habbo que podras encontrar, disfruta de nuestras instalaciones y de nuestro foro web"/>
	<meta name="keyword" contet="habbo,anyslehider,foro web,web"/>
</head>
<body class="hold-transition login-page" style="background: white;">
  
<center style="position:fixed;left:0;top:0;">
  <iframe allow="autoplay" id="audio" style="    height: 161px;
    border: none;
    width: 400px;
    position: relative;
    filter: invert(1);
    top: -80px;"></iframe>
  </center>
<div class="login-box">
  <div class="login-logo">
    <img src="./imgs/logococa.png" style="max-width: 100px;"/>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg" style="color: #dc2626;">¡Inicia Sesión!</p>

      <form action="" method="post">
        <div class="input-group mb-3">
          <input type="text" name="user" class="form-control" placeholder="Usuario">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Clave">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
         
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" name="enviar" class="btn btn-primary btn-block" style="background:#dc2626;border: 1px solid #dc2626">Entrar</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <!-- /.social-auth-links -->
      <br>
      <hr>
      <p class="mb-1">
        <a href="#" onclick="show_recover()" style="color: #dc2626">Recuperar Contraseña</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="./plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="./plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="./dist/js/adminlte.min.js"></script>
</body>
</html>

<?php
check_msg();
?>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

$(document).ready(function(){
    $("body").focus();
    $("body").click();
    $("[data-toggle='tooltip']").each(function(){
      $(this).tooltip();
    });
    $("input").each(function(){
      $(this).attr("autocomplete","off");
    });

    var myaudio = document.getElementById("audio").autoplay = true;


    $(document).click(function(){
      console.log("Radio activa")
    })
    
  });

  $(document).ready(function(){
    $.ajax({
  "url" : "<?=$my_radio_stream_embed?>",
  "type" : "get",
  success:function(data){
    var url_audio = data.url;
    console.log(url_audio);
    $("#audio").attr("src",url_audio);
    
    $("#audio").contents().find('body').css("background-color","rgba(0,0,0,0)");
  }
})
  });

  function show_recover(){
    Swal.fire(
    '¿Perdiste tu contraseña?',
    '¡debes contactar a un admin en la sala de <?=$se?> para que te la restauren!',
    'warning'
  )
  }

</script>

