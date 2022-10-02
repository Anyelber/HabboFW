 <?php

if(isset($responderSugerencia) && is_admin_or_more(rol($_SESSION['id']))){

  $q = $pdo->prepare("SELECT * FROM sugerencias WHERE id = :responderSugerencia");
  $q->execute([
    $responderSugerencia
  ]);

  $r = $q->fetch();

  if($r['tipo'] == 0){
    $tipo = "Queja Foro Web";
}elseif($r['tipo'] == 1){
    $tipo = "Sugerencia Foro Web";
}elseif($r['tipo'] == 2){
    $tipo = "Queja Personal Administrativo";
}elseif($r['tipo'] == 3){
    $tipo = "Sugerencia Personal Administrativo";
}elseif($r['tipo'] == 4){
    $tipo = "Queja Sistema de Pagos";
}elseif($r['tipo'] == 5){
    $tipo = "Sugerencia Sistema de Pagos";
}elseif($r['tipo'] == 6){
    $tipo = "Queja Discord";
}elseif($r['tipo'] == 7){
    $tipo = "Sugerencia Discord";
}elseif($r['tipo'] == 8){
    $tipo = "Queja Radio";
}elseif($r['tipo'] == 9){
    $tipo = "Sugerencia Radio";
}elseif($r['tipo'] == 10){
    $tipo = "Queja Trabajador";
}

  $texto = '
  Respondiendo a la siguiente queja / sugerencia:
  <br><br>
  <div style="background:#333;color:white;text-left;padding: 10px; border-radius: 10px; font-size: 20px;">
  <b style="color: red">'.$tipo.'</b>
  <br><br>
  <i>'.$r['text'].'</i>
  </div>
  ';

  $fecha = date("Y-m-d H:i:s");

  $q = $pdo->prepare("INSERT INTO chat (sender, receiver, text, fecha, status) VALUES (:sender, :receiver, :texto, :fecha, 0)");
  $q->execute([
    $_SESSION['id'],
    $r['created_by'],
    $texto,
    $fecha
  ]);
  redir("?p=chat&user=".$r['created_by']);
}

if(!is_admin_or_more(rol($_SESSION['id'])) && isset($user) && !son_amigos($_SESSION['id'],$user)){
  $v = $pdo->prepare("SELECT id FROM chat WHERE (sender = :yo AND receiver = :otro) OR (receiver = :yo2 AND sender = :otro2)");
  $v->execute([
    $_SESSION['id'],
    $user,
    $_SESSION['id'],
    $user
  ]);

  if($v->rowCount()==0){
    alert("No eres amigo de esta persona asi que no puedes enviarle un mensaje","?p=chat",0);
  }
}


if(isset($enviar)){
  $texto = clear($texto);

  if(strlen($texto) == 0){
    alert("No puedes enviar un mensaje en blanco","?p=chat&user=".$user,0);
  }

  $fecha = date("Y-m-d H:i:s");

  $q = $pdo->prepare("INSERT INTO chat (sender, receiver, text, fecha, status) VALUES (:sender, :receiver, :texto, :fecha, 0)");
  $q->execute([
    $_SESSION['id'],
    $user,
    $texto,
    $fecha
  ]);
  redir("");
}
 ?>
 <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?=$habbo_name?> Chat</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?=$habbo_name?></a></li>
              <li class="breadcrumb-item active">Chat</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
          <div class="card">
              <div class="card-header">
                <h3 class="card-title">Amigos Chat</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                  <?php
                    $qa = $pdo->prepare("SELECT id_user1, id_user2 FROM  amigos  WHERE id_user1 = :id1 OR id_user2 = :id2");
                    $qa->execute([
                      $_SESSION['id'],
                      $_SESSION['id']
                    ]);

                    while($ra = $qa->fetch()){
                      if($ra['id_user1'] == $_SESSION['id']){
                        $id_habbo = $ra['id_user2'];
                        $nombre_habbo = nombre_habbo($ra['id_user2']);
                      }else{
                        $id_habbo = $ra['id_user1'];
                        $nombre_habbo = nombre_habbo($ra['id_user1']);
                      }

                      $qf = $pdo->prepare("SELECT count(id) as total FROM chat WHERE sender = :ids AND receiver = :idu AND status = 0");
                      $qf->execute([
                        $id_habbo,
                        $_SESSION['id']
                      ]);

                      $rf = $qf->fetch();

                      ?>  
                        <li class="nav-item" <?=((isset($user) && $user == $id_habbo) ? "style='background:#dc3545;cursor:default;'" : "")?>>
                          <a href="?p=chat&user=<?=$id_habbo?>" class="nav-link" <?=((isset($user) && $user == $id_habbo) ? "style='color:white;cursor:default;'" : "")?>>
                              <img class="direct-chat-img" src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=$nombre_habbo?>&direction=3&head_direction=3&gesture=sml&size=l&action=&headonly=true" alt="Message User Image"> &nbsp; <?=$nombre_habbo?>
                              <?php
                                if($rf['total']>0){
                                  ?>
                                    <span class="right badge badge-danger"><?=$rf['total']?></span>
                                  <?php
                                }
                              ?>
                          </a>
                        </li>

                      <?php

                    }
                  ?>
                
                 
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
























            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Chats Abiertos</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                  <?php
                    $qa = $pdo->prepare("SELECT DISTINCT sender, receiver FROM  chat  WHERE sender = :id1 OR receiver = :id2");
                    $qa->execute([
                      $_SESSION['id'],
                      $_SESSION['id']
                    ]);

                    while($ra = $qa->fetch()){
                      if($ra['sender'] == $_SESSION['id']){
                        $id_habbo = $ra['receiver'];
                        $nombre_habbo = nombre_habbo($ra['receiver']);
                      }else{
                        $id_habbo = $ra['sender'];
                        $nombre_habbo = nombre_habbo($ra['sender']);
                      }

                      $qf = $pdo->prepare("SELECT count(id) as total FROM chat WHERE sender = :ids AND receiver = :idu AND status = 0");
                      $qf->execute([
                        $id_habbo,
                        $_SESSION['id']
                      ]);

                      $rf = $qf->fetch();

                      ?>  
                        <li class="nav-item" <?=((isset($user) && $user == $id_habbo) ? "style='background:#dc3545;cursor:default;'" : "")?>>
                          <a href="?p=chat&user=<?=$id_habbo?>" class="nav-link" <?=((isset($user) && $user == $id_habbo) ? "style='color:white;cursor:default;'" : "")?>>
                              <img class="direct-chat-img" src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=$nombre_habbo?>&direction=3&head_direction=3&gesture=sml&size=l&action=&headonly=true" alt="Message User Image"> &nbsp; <?=$nombre_habbo?>
                              <?php
                                if($rf['total']>0){
                                  ?>
                                    <span class="right badge badge-danger"><?=$rf['total']?></span>
                                  <?php
                                }
                              ?>
                          </a>
                        </li>

                      <?php

                    }
                  ?>
                
                 
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
          <div class="card card-danger direct-chat direct-chat-danger shadow-lg">
              <div class="card-header">
                <h3 class="card-title">Mensajes</h3>

                <div class="card-tools">
                    
                 
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- Conversations are loaded here -->
                <div class="direct-chat-messages" id="mensajes" style="height: 70vh !important;">
                  <?php
                    if(isset($user)){

                      $qm = $pdo->prepare("SELECT * FROM chat WHERE (sender = :idu1 AND receiver = :idu2) OR (sender = :idu3 AND receiver = :idu4) ORDER BY id ASC");
                      $qm->execute([
                        $_SESSION['id'],
                        $user,
                        $user,
                        $_SESSION['id']
                      ]);

                      while($rm = $qm->fetch()){
                        if($rm['sender'] == $_SESSION['id']){

                          ?>
                            <div class="direct-chat-msg right" style="text-align:right;">
                              <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name float-right"><?=nombre_habbo($_SESSION['id'])?></span>
                                <span class="direct-chat-timestamp float-left"><?=date("d/m/Y h:i a", strtotime($rm['fecha']))?></span>
                              </div>
                              <!-- /.direct-chat-infos -->
                              <img class="direct-chat-img" src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=nombre_habbo($_SESSION['id'])?>&direction=3&head_direction=3&gesture=sml&size=l&action=&headonly=true" alt="Message User Image">
                              <!-- /.direct-chat-img -->
                              <div class="direct-chat-text">
                                <?=$rm['text']?>
                              </div>
                              <!-- /.direct-chat-text -->
                            </div>
                          <?php

                        }else{
                          ?>
                            <div class="direct-chat-msg">
                              <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name float-left"><?=nombre_habbo($user)?></span>
                                <span class="direct-chat-timestamp float-right"><?=date("d/m/Y h:i a", strtotime($rm['fecha']))?></span>
                              </div>
                              <!-- /.direct-chat-infos -->
                              <img class="direct-chat-img" src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=nombre_habbo($user)?>&direction=3&head_direction=3&gesture=sml&size=l&action=&headonly=true" alt="Message User Image">
                              <!-- /.direct-chat-img -->
                              <div class="direct-chat-text">
                                <?=$rm['text']?>
                              </div>
                              <!-- /.direct-chat-text -->
                            </div>
                          <?php
                        }
                        ?>

                        <?php
                      }
                      ?>
                     
                      <!-- /.direct-chat-msg -->

                      <!-- Message to the right -->
                     
                      <!-- /.direct-chat-msg -->
                    </div>

                      <?php

                    }else{
                      ?>
                      <div style='width: 100%;height: 60vh; display: flex; align-items:center; justify-content:center; '>
                        <i style="color:red">Seleccione un amigo para empezar un chat</i>
                      </div>
                      <?php
                      echo "";
                    }
                  ?>
                  <!-- Message. Default to the left -->
                  
                <!--/.direct-chat-messages-->

                <!-- Contacts are loaded here -->
           
                <!-- /.direct-chat-pane -->
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <form action="" method="post">
                  <div class="input-group">
                    <input type="text" name="texto" placeholder="Escribe un mensaje ..." class="form-control">
                    <span class="input-group-append">
                      <button name="enviar" type="submit" class="btn btn-danger"><i class="fas fa-paper-plane"></i></button>
                    </span>
                  </div>
                </form>
              </div>
              <!-- /.card-footer-->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>

    <script>
      $("#mensajes").animate({
                    scrollTop: $(
                      '#mensajes').get(0).scrollHeight
                }, 1000);

      </script>