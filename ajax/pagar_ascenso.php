<?php
include "../configs/configs.php";
include "../configs/functions.php";

$id = clear($id);
$rol = clear($rol);


if(is_admin_or_more(rol($_SESSION['id']))){

    $qu = $pdo->prepare("SELECT * FROM users WHERE id = :user");
    $qu->execute([$id]);

    $ru = $qu->fetch();

    $antiguo_rol = $ru['rol'];

    if($antiguo_rol >= $rol){
      alert("Debes seleccionar un rol inferior al actual (".nombre_rol($ru['rol']).")","",0);
    }

    

    $ascensos = cant_ascensos($id);
    $times = cant_times_validos_user($id);
    $horas = horas_trabajadas($id);

    $fecha = date("Y-m-d H:i:s");

    $q2 = $pdo->prepare("INSERT INTO pagas (id_dio, id_recibe, ascensos, times, horas, fecha, status, tipo, rol_pago) VALUES (:id_dio, :id_recibe, :ascensos, :times, :horas, :fecha, 1, 1, :rol)");
    $q2->execute([
        $_SESSION['id'],
        $id,
        $ascensos,
        $times,
        $horas,
        $fecha,
        $rol
    ]);

    $q = $pdo->prepare("INSERT INTO ascensos (id_dio, id_recibe, old_rol, new_rol, created_at, tipo) VALUES (:id_dio, :id_recibe, :old_rol, :rol, :fecha, 1)");
    $q->execute([
        $_SESSION['id'],
        $id,
        $antiguo_rol,
        $rol,
        $fecha
    ]);

    $nc = generar_codigo();

    $qf = $pdo->prepare("UPDATE users SET rol = :rol, codigo = :nc WHERE id = :id");
    $qf->execute([$rol, $nc, $id]);

    reiniciar_user($id);
    
    if(is_tecnico_or_more(rol($id))){
      $mision = $prefix_firma."-  ".nombre_rol_usuario($id)." -".firma($_SESSION['id'])." -".firma($id);
    }else{
      $mision = $prefix_firma."-  ".nombre_rol_usuario($id)." -".firma($_SESSION['id']);
    }

    echo $mision.'<br><br> <center>
    <button type="reset" onclick="copiar()" class="btn btn-success">Copiar Mision</button>
</center>


<input style="position:fixed;left:-100000px;right:10000px;" id="temp"/>

<script>
function copiar() {
  var $temp = $("#temp");
  $temp.val("'.$mision.'").select();
  document.execCommand("copy");
  $temp.remove();

  Swal.fire(
    "",
    "Mision Copiada",
    "success"
  );

}
</script>';
  }else{
    echo "No tienes permisos";
  }

  
?>