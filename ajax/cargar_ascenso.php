<?php
include "../configs/configs.php";
include "../configs/functions.php";

$id = clear($id);

?>
 <center>
                <div class="card-body">
                  <div class="form-group">

                   
                  </div>

                <?=keko_user($id,100)?>
                  <br>
                  <br>
                  <span style="color:red; font-weight:bold; font-size: 25px;" id="nombreHabbo2"><?=nombre_habbo($id)?></span><br>
                  <span><?=rango_habbo($id)?></span>
          <br><br>
                  <div class="form-group" style="width:40%">
                    <select id="rol_asc" required class="form-control" name="new_rol" id="exampleInputEmail1">
                        <option value="">Seleccione un nuevo Rango</option>
                        <?=options_roles()?>
                    </select>
                  </div>

                  <input type="hidden" name="id_asc" value="<?=$id?>" id="id_asc"/>
                
                </div>
                <!-- /.card-body -->
                </center>