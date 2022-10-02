<?php
include "../configs/configs.php";
include "../configs/functions.php";

$id = clear($id);
$q = $pdo->prepare("SELECT * FROM likes WHERE id_noticia = :id");
$q->execute([
    $id
]);

while($r = $q->fetch()){
    ?>
        <div class="form-group">
            <?=keko_user($r['id_user'])?> <?=nombre_habbo($r['id_user'])?>
        </div>
    <?php
}
?>