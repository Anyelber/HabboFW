<?php
@session_start();
@extract($_REQUEST);

@date_default_timezone_set("America/Bogota");

$pdo_user = "mysql_username";
$pdo_password = "mysql_password";
$pdo_db = "mysql_db";
$habbo_name = "Tu nombre de agencia";
$prefix_firma = "tu prefix de agencia ej: AGEN";

$fecha_actual = date("Y-m-d H:i:s");

$my_radio_stream_url = "http://s20.myradiostream.com:00000/7.html";

$radio_url = "https://radio_url.com/";

$my_radio_stream_embed = "https://myradiostream.com/embed/json.php?s=USER&nocache=0000000000";

$instagram_url = "http://www.instagram.com/tu_instagram";
$twitter_url = "http://www.twitter.com/tu_twitter";
$discord_url = "http://www.discord.com/tu_discord";

$pdo = new PDO('mysql:host=localhost;dbname='.$pdo_db, $pdo_user, $pdo_password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);



$agente_roles = array(2,3,4);
$seguridad_roles = array(5,6,7,8,9,10,11);
$tecnico_roles = array(12,13,14,15,16,17,18);
$logistica_roles = array(19,20,21,22,23,24,25);
$supervisor_roles = array(26,27,28,29,30,31,32);
$director_roles = array(33,34,35,36,37,38,39);
$presidente_roles = array(40,41,42,43,44,45,46);
$elite_roles = array(47,48,49,50,51,52,53);
$junta_directiva_roles = array(54,55,56,57,58,59,60);
$administrador_roles = array(61);
$manager_roles = array(62);
$founder_roles = array(63);
$owner_roles = array(64);
$developer_roles = array(65);

$agente_tiempo = "30 minutes";
$seguridad_tiempo = "2 hours";
$tecnico_tiempo = "1 days";
$logistica_tiempo = "2 days";
$supervisor_tiempo = "3 days";
$director_tiempo = "5 days";
$presidente_tiempo = "7 days";
$elite_tiempo = "10 days";
$junta_directiva_tiempo = "15 days";
$administrador_tiempo = "0 hours";
$manager_tiempo = "0 hours";
$founder_tiempo = "0 hours";
$owner_tiempo = "0 hours";
$developer_tiempo = "2 days";

$pago_seg = array(3,3);
$pago_tec = array(4,3);
$pago_log = array(5,3);
$pago_sup = array(6,3);
$pago_dir = array(7,3);
$pago_pre = array(8,3);
$pago_eli = array(9,3);
$pago_jtd = array(10,3);


$req_pago_seg = array("time",4,2);
$req_pago_tec = array("time",4,2);
$req_pago_log = array("time",5,2);
$req_pago_sup = array("asc",10,5);
$req_pago_dir = array("asct",12,5);

$req_pago_pre = array("timea",14,5);
$req_pago_eli = array("timea",15,5);
$req_pago_jtd = array("timea",16,5);

$req_pago_especial_log = array("time",5,2);
$req_pago_especial_other = array("time",7,2);

$black_list = array("141.414.141.414","132.123.132.123"); //add ban ip example: array("187.524.25.14","185.121.23.14");




//Roles

//-1 Despedido

//0 Aspirante
//1 Oficinista
//2 Agente C
//3 Agente B
//4 Agente A

//5 Seguridad G
//6 Seguridad F
//7 Seguridad E
//8 Seguridad D
//9 Seguridad C
//10 Seguridad B
//11 Seguridad A

//12 Tecnico G
//13 Tecnico F
//14 Tecnico E
//15 Tecnico D
//16 Tecnico C
//17 Tecnico B
//18 Tecnico A

//19 Logistica G
//20 Logistica F
//21 Logistica E
//22 Logistica D
//23 Logistica C
//24 Logistica B
//25 Logistica A

//26 Supervisor G
//27 Supervisor F
//28 Supervisor E
//29 Supervisor D
//30 Supervisor C
//31 Supervisor B
//32 Supervisor A

//33 Director G
//34 Director F
//35 Director E
//36 Director D
//37 Director C
//38 Director B
//39 Director A

//40 Presidente G
//41 Presidente F
//42 Presidente E
//43 Presidente D
//44 Presidente C
//45 Presidente B
//46 Presidente A

//47 Elite G
//48 Elite F
//49 Elite E
//50 Elite D
//51 Elite C
//52 Elite B
//53 Elite A

//54 Junta Directiva G
//55 Junta Directiva F
//56 Junta Directiva E
//57 Junta Directiva D
//58 Junta Directiva C
//59 Junta Directiva B
//60 Junta Directiva A

//61 Administrador  (roles - 2)

//62 Manager

//63 Founder

//64 Dueño

//65 Developer
?>