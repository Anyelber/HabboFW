<div style="width: 100%; height: 100vh; display: flex; justify-content: center; align-items: center;flex-wrap:wrap;">
<div>
    <center>
<?php
$q = $pdo->prepare("SELECT habbo,rol FROM users WHERE rol >= 61 ORDER BY rol DESC, habbo ASC");
                $q->execute();

                while($r = $q->fetch()){

                 
                    if($r['habbo'] == "Pamebandlas"){
                        $gesture = "agr";
                    }else{
                        $gesture = "sml";
                    }

                    ?>
                    
                      <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?=$r['habbo']?>&direction=3&head_direction=3&gesture=<?=$gesture?>&size=l&action=wav&rand=<?=date("YmdHis").rand(0,99999)?>"/>
                    <?php
                  }
                
              ?>
              </center>
              </div>


<h3 style="width: 100%;"><center>¡Estamos trabajando en este modulo para ti!</center></div>