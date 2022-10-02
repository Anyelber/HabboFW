<?php
include "../configs/configs.php";
include "../configs/functions.php";

$firma = clear($firma);

$q = $pdo->prepare("SELECT * FROM users WHERE firma = :firma");
$q->execute([$firma]);

if($q->rowCount()==0){
    echo 1;
}else{
    echo 0;
}