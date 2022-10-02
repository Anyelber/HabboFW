<?php
include "../configs/configs.php";
include "../configs/functions.php";

$user = clear($user);
$password = clear($password);

$epassword = md5($password);

$q = $pdo->prepare("SELECT * FROM users WHERE user = :user AND password = :epassword");
$q->execute([
    $user,
    $epassword
]);


if($q->rowCount()>0){
    
    $token = md5(rand(0,999999).$prefix_firma." - COMPANY".date("Y-m-d H:i:s"));

    $qf = $pdo->prepare("UPDATE users SET token_app = :token WHERE user = :user");
    $qf->execute([
        $token,
        $user
    ]);
    $r = $q->fetch();

    

    $qf = $pdo->prepare("UPDATE users SET ip = :ip WHERE id = :id");
    $qf->execute([
      $ip,
      $_SESSION['id']
    ]);
    
    echo json_encode(array("status"=>1,"message"=>"Login Correcto","data"=>array("id"=>$r['id'],"nombre_habbo"=>nombre_habbo($r['id']), "rol"=>nombre_rol(rol($r['id'])),"token"=>$token)));

}else{
    echo json_encode(array("status"=>0,"message"=>"Usuario o contraseña incorrecta","data"=>array()));
}
?>