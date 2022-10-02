<?php
include "../configs/configs.php";
include "../configs/functions.php";

$id = clear($id);

$fecha = date("Y-m-d H:i:s");

$ascensos = cant_ascensos($id);
$times = cant_times_validos_user($id);
$horas = horas_trabajadas($id);


$q = $pdo->prepare("INSERT INTO pagas (id_dio, id_recibe, ascensos, times, horas, fecha, status) VALUES (:idd, :idr, :ascensos, :times, :horas, :fecha, 1)");
$q->execute([
    $_SESSION['id'],
    $id,
    $ascensos,
    $times,
    $horas,
    $fecha
]);

reiniciar_user($id);
?>