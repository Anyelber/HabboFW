<?php
if(!is_admin_or_more(rol($_SESSION['id']))){
    alert("No tienes permiso para acceder a este modulo","./",0);
}
?>

<div style="padding: 20px;">
<h1><i class="nav-icon fas fa-user-secret"></i> Intrusos registrados</h1><br><br>
<table class="table table-striped">
    <tr>
        <th>Avatar</th>
        <th>Nombre</th>
        <th>Rango</th>
        <th>Firma</th>
    </tr>
    <?php

$q = $pdo->prepare("SELECT * FROM users");

$q->execute();

while($r = $q->fetch()){

    

    if(!pertenece_coca($r['habbo'])){
        ?>
        <tr>
            <td><?=keko_user($r['id'])?></td>
            <td><?=nombre_habbo($r['id'])?></td>
            <td><?=nombre_rol(rol($r['id']))?></td>
            <td><?=firma($r['id'])?></td>
        </tr>
        <?php
    }
    

}

?>
</table>
</div>