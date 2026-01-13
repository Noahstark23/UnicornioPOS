<?php
require_once("class/class.php");

$con = new Login();
$con = $con->ConfiguracionPorId();
$simbolo = "<strong>".$con[0]['simbolo']."</strong>";
?>





<!--######################### LISTAR PRODUCTOS POR FAMILIAS ########################-->
<?php if (isset($_GET['ProductosxFamilias']) && isset($_GET['url'])): ?>

<?php
$familia = new Login();
$familia = $familia->ListarFamilias();
?>
    <div class="row-horizon">
        <span class="categories selectedGat" id=""><i class="fa fa-home"></i></span>
        <?php 
        if($familia==""){ echo ""; } else {
        $a=1;
        for ($i = 0; $i < sizeof($familia); $i++) { ?>
        <span class="categories" id="<?php echo $familia[$i]['nomfamilia'];?>"><?php echo $familia[$i]['nomfamilia'];?></span>
        <?php } } ?>
    </div>

    <?php if($_GET['url']=="modal"){ ?>

    <div class="col-md-12">
        <div id="searchContaner"> 
            <div class="form-group has-feedback2"> 
                <label class="control-label"></label>
                <input type="text" class="form-control" name="busquedaproductov" id="busquedaproductov" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Realice la Búsqueda del Producto por Nombre">
                  <i class="fa fa-search form-control-feedback2"></i> 
            </div> 
        </div>
    </div>

    <?php } ?>

    <div id="productList2">
        <div class="row row-vertical">
        <?php
        $producto = new Login();
        $producto = $producto->ListarProductosModal();

        $monedap = new Login();
        $cambio = $monedap->MonedaProductoId(); 

        if($producto==""){

        echo "<div class='alert alert-danger'>";
        echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
        echo "<center><span class='fa fa-info-circle'></span> NO EXISTEN PRODUCTOS REGISTRADOS ACTUALMENTE</center>";
        echo "</div>";    

        } else {

        for ($ii = 0; $ii < sizeof($producto); $ii++) { ?>


    <div ng-click="afterClick()" ng-repeat="product in ::getFavouriteProducts()" OnClick="DoAction('<?php echo $producto[$ii]['codproducto']; ?>','<?php echo $producto[$ii]['producto']; ?>','<?php echo $producto[$ii]['nommarca'] == '' ? "******" : $producto[$ii]['nommarca']; ?>','<?php echo $producto[$ii]['nommodelo'] == '' ? "******" : $producto[$ii]['nommodelo']; ?>','<?php echo $producto[$ii]['preciocompra']; ?>','<?php echo $producto[$ii]['precioxpublico']; ?>','<?php echo $producto[$ii]['descproducto']; ?>','<?php echo $producto[$ii]['ivaproducto']; ?>','<?php echo $producto[$ii]['existencia']; ?>','<?php echo $precioconiva = ( $producto[$ii]['ivaproducto'] == 'SI' ? $producto[$ii]['precioxpublico'] : "0.00"); ?>','<?php echo $producto[$ii]['existencia']; ?>');"> 
        <div id="<?php echo $producto[$ii]['codproducto']; ?>">
            <div class="darkblue-panel pn" title="<?php echo $producto[$ii]['producto'].' | ('.$producto[$ii]['nomfamilia'].')';?>">
                    <div class="darkblue-header">
                        <div id="proname" class="text-white"><?php echo getSubString($producto[$ii]['producto'],16);?></div>
                    </div>
        <?php if (file_exists("./fotos/productos/".$producto[$ii]["codproducto"].".jpg")){

        echo "<img src='fotos/productos/".$producto[$ii]['codproducto'].".jpg?' class='rounded-circle' style='width:150px;height:134px;'>"; 

        } else {

        echo "<img src='fotos/producto.png' class='rounded-circle' style='width:150px;height:134px;'>";  } ?>
                <input type="hidden" id="category" name="category" value="<?php echo $producto[$ii]['nomfamilia']; ?>">
                <div class="mask">
                    <a class="text-white">
                    <?php echo $simbolo.$producto[$ii]['precioxpublico'];?><br>
                    <?php echo $cambio == '' ? "" : $cambio[0]['simbolo']."</strong>".number_format($producto[$ii]['precioxpublico']/$cambio[0]['montocambio'], 2, '.', ','); ?>
                    </a>
                    <h5><i class="fa fa-bars"></i> <?php echo $producto[$ii]['existencia'];?></h5>
                </div>

            </div>
        </div>
    </div>
                 
        <?php } } ?>

        </div> 
    </div>

<?php endif; ?>
<!--######################### LISTAR PRODUCTOS POR FAMILIAS ########################-->




<!--######################### LISTAR PRODUCTOS POR FAMILIAS EN MODAL #########################-->
<?php if (isset($_GET['ProductosxFamiliasModal'])): ?>

<div class="row">
    <div class="scroll col-lg-4 col-xl-3">
        <!-- Nav tabs -->
        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
        <?php
        $categoria = new Login();
        $categoria = $categoria->ListarFamilias();
        if($categoria==""){ echo ""; } else {
            for ($i = 0; $i < sizeof($categoria); $i++) {
        ?>
        <a class="nav-link" id="v-pills-<?php echo $categoria[$i]['codfamilia'];?>-tab" data-toggle="pill" href="#v<?php echo $categoria[$i]['codfamilia'];?>" data-toggle="tab" title="<?php echo $categoria[$i]['nomfamilia'];?>" role="tab" aria-controls="v<?php echo $categoria[$i]['codfamilia'];?>" aria-selected="false">
        <span class="hidden-xs"><i class="fa fa-asterisk"></i> <?php echo $categoria[$i]['nomfamilia'];?></span>
        </a>
        </li>
        <?php
    }
        }
    ?> 
    </div>
</div>

<div class="scroll col-lg-8 col-xl-9">
    <div class="tab-content p-4" id="v-pills-tabContent">
    <?php
    $categoria = new Login();
    $categoria = $categoria->ListarFamilias();
    if($categoria==""){ echo ""; } else {
    for ($i = 0; $i < sizeof($categoria); $i++) {
    ?>
    <?php if ($i === 0): ?>
    <div class="tab-pane active" id="v<?php echo $categoria[$i]['codfamilia'];?>">
        <?php else: ?>
        <div class="tab-pane" id="v<?php echo $categoria[$i]['codfamilia'];?>">
        <?php endif; ?>
        <?php $codigo_cate = $categoria[$i]['codfamilia']; ?>
        <p>
        <!--AQUI LISTO LOS PRODUCTOS -->
        <div class="row">
        <?php
        $producto = new Login();
        $producto = $producto->ListarProductosModal();

        $monedap = new Login();
        $cambio = $monedap->MonedaProductoId(); 

        if($producto==""){

        echo "<div class='alert alert-danger'>";
        echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
        echo "<center><span class='fa fa-info-circle'></span> NO EXISTEN PRODUCTOS REGISTRADOS ACTUALMENTE</center>";
        echo "</div>";    

        } else {

        for ($ii = 0; $ii < sizeof($producto); $ii++) {

            if ($producto[$ii]['codfamilia'] == $codigo_cate && $producto[$ii]['existencia'] > 0) { ?>
        <div class="col-md-2 mb" ng-click="afterClick()" ng-repeat="product in ::getFavouriteProducts()" OnClick="DoAction('<?php echo $producto[$ii]['codproducto']; ?>','<?php echo $producto[$ii]['producto']; ?>','<?php echo $producto[$ii]['nommarca'] == '' ? "******" : $producto[$ii]['nommarca']; ?>','<?php echo $producto[$ii]['nommodelo'] == '' ? "******" : $producto[$ii]['nommodelo']; ?>','<?php echo $producto[$ii]['preciocompra']; ?>','<?php echo $producto[$ii]['precioxpublico']; ?>','<?php echo $producto[$ii]['descproducto']; ?>','<?php echo $producto[$ii]['ivaproducto']; ?>','<?php echo $producto[$ii]['existencia']; ?>','<?php echo $precioconiva = ( $producto[$ii]['ivaproducto'] == 'SI' ? $producto[$ii]['precioxpublico'] : "0.00"); ?>','<?php echo $producto[$ii]['existencia']; ?>');">
                <div class="darkblue-panel pn" title="<?php echo $producto[$ii]['producto'].' | MARCA: ('.$producto[$ii]['nommarca'].') | MODELO: ('.$producto[$ii]['nommodelo'].')';?>">
                    <div class="darkblue-header">
                        <h6 class="text-white"><?php echo getSubString($producto[$ii]['producto'],14);?></h6>
                    </div>
                    <p><?php if (file_exists("./fotos/productos/".$producto[$ii]["codproducto"].".jpg")){

        echo "<img src='fotos/productos/".$producto[$ii]['codproducto'].".jpg?' class='img-circle' style='width:80px;height:60px;'>"; 

            } else {

        echo "<h4 class='mb-0 display-6'><i class='fa fa-cubes text-white'></i></h4>";  } ?></p>
                                                        <h5> <?php echo $simbolo." ".$producto[$ii]['precioxpublico'];?></h5>
    <?php echo $cambio[0]['codmoneda'] == '' ? "" : "<h5>".$cambio[0]['simbolo']."</strong>".number_format($producto[$ii]['precioxpublico']/$cambio[0]['montocambio'], 2, '.', ',')."</h5>"; ?>
                <h5><i class="fa fa-bars"></i> <?php echo $producto[$ii]['existencia'];?></h5><br>
                            </div><br>
                        </div>
                        <?php
                        }
                    }
                }
                ?>
                </div>
                <!--FIN LISTO LOS PRODUCTOS -->
                            </p>
                        </div>
                    <?php
                }
            } ?>
        </div>
    </div>
</div>
<?php endif; ?>
<!--######################### LISTAR PRODUCTOS POR FAMILIAS EN MODAL ########################-->




<script type="text/javascript">
$(document).ready(function() {

    //  search product
   $("#busquedaproductov").keyup(function(){
      // Retrieve the input field text
      var filter = $(this).val();
      // Loop through the list
      $("#productList2 #proname").each(function(){
         // If the list item does not contain the text phrase fade it out
         if ($(this).text().search(new RegExp(filter, "i")) < 0) {
             $(this).parent().parent().parent().hide();
         // Show the list item if the phrase matches
         } else {
             $(this).parent().parent().parent().show();
         }
      });
   });
});


$(".categories").on("click", function () {
   // Retrieve the input field text
   var filter = $(this).attr('id');
   $(this).parent().children().removeClass('selectedGat');

   $(this).addClass('selectedGat');
   // Loop through the list
   $("#productList2 #category").each(function(){
      // If the list item does not contain the text phrase fade it out
      if ($(this).val().search(new RegExp(filter, "i")) < 0) {
         $(this).parent().parent().parent().hide();
         // Show the list item if the phrase matches
      } else {
         $(this).parent().parent().parent().show();
      }
   });
});

</script>