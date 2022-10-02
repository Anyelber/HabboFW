<?php
include "../configs/configs.php";
include "../configs/functions.php";

$id = clear($id);

$q = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$q->execute([$id]);

$r = $q->fetch();
$nrango = $r['rol']+1;

$qa = $pdo->prepare("SELECT * FROM ascensos ORDER BY id DESC LIMIT 1");
$qa->execute();

$ra = $qa->fetch();

$num = $ra['id']+1;

echo "<span style='color: red'>".$prefix_firma."- ".nombre_rol($nrango)." -".firma($_SESSION['id'])." -".firma($id)." #".$num."</span>";