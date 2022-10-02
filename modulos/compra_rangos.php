<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Compra de Rangos <?=$habbo_name?></h3>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

<table class="table table-strpied dtc">
    <thead>
        <tr>
            <th >Rango</th>
            <th >Precio</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $q = $pdo->prepare("SELECT * FROM roles WHERE precio >0 ORDER BY precio ASC");
            $q->execute();
            while($r = $q->fetch()){
                ?>
                    <tr>
                        <td><?=$r['nombre']?></td>
                        <td><?=$r['precio']?></td>
                    </tr>
                <?php
            }
        ?>
    </tbody>
</table>
</div>
</section>
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h3>Simulador de compra</h3>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <div class="row" style="padding: 20px;">
        <div class="col-md-5 col-sm-5 col-lg-5" style="background: #eaeaea; border-radius: 10px;">
            <div class="form-group">
                <label>Rango actual</label>
                <select onchange="calcular()" class="form-control" id="rango_actual">
                    <option value="">Seleccione su rango actual</option>
                    <?php
                        $qra = $pdo->prepare("SELECT * FROM roles");
                        $qra->execute();

                        while($rra = $qra->fetch()){
                            ?>
                                <option <?=($rra['id'] == rol($_SESSION['id'])) ? "selected" : ""; ?> value="<?=$rra['precio']?>"><?=$rra['nombre']?></option>
                            <?php
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-2 col-sm-2 col-lg-2">&nbsp;</div>
        <div class="col-md-5 col-sm-5 col-lg-5" style="background: #eaeaea; border-radius: 10px;">
            <label>Rango a comprar</label>
                <select onchange="calcular()" class="form-control" id="rango_compra">
                    <option value="">Seleccione rango a comprar</option>
                    <?php
                        $qra2 = $pdo->prepare("SELECT * FROM roles WHERE precio > 0 AND id > 25");
                        $qra2->execute();

                        while($rra2 = $qra2->fetch()){
                            ?>
                                <option value="<?=$rra2['precio']?>"><?=$rra2['nombre']?></option>
                            <?php
                        }
                    ?>
                </select>
        </div>

        <div class="col-md-12 col-sm-12 col-lg-12 mt-4">
            <h2>Total a pagar: <span id="creditos" style="color: red;">0c</span></h2>
        </div>
    </div>
                    </div>


<script>
    function calcular(){
        var actual = $("#rango_actual").val();
        var compra = $("#rango_compra").val();

        if(compra>0){

            if(actual<0){
                actual = 0;
            }

            if(compra<0){
                compra = 0;
            }

            var total = compra - actual;

            if(total<0){
                total = 0;
            }

            if(compra == 20){
                total = "No en venta";
            }

            $("#creditos").text(total+"c");

        }
    }

    $(document).ready(function(){
        $(".dtc").dataTable({
            "order" : [[
                1, "asc"
            ]]
        })
    })
</script>