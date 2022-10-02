<?php
include "./configs/configs.php";
include "./configs/functions.php";
require_once('anti_ddos/start.php'); 

if(isset($_SESSION['id'])){
    redir("./dashboard.php");
}else{
    redir("./login.php");
}
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=$habbo_name?> Foro Web</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <link rel="stylesheet" href="searchers.css">
  <meta name="description" contet="<?=$habbo_name?> es la mejor agencia de Habbo que podras encontrar, disfruta de nuestras instalaciones y de nuestro foro web"/>
	<meta name="keyword" contet="habbo,,anyslehider,foro,web,foro web"/>
</head>
</html>

