<?php
require_once("class/class.php"); 
if(isset($_SESSION['acceso'])) { 
     if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria") {

$tra = new Login();
$ses = $tra->ExpiraSession(); 

$imp = new Login();
$imp = $imp->ImpuestosPorId();
$impuesto = $imp[0]['nomimpuesto'];

if(isset($_POST["proceso"]) and $_POST["proceso"]=="save")
{
$reg = $tra->RegistrarProductos();
exit;
} 
elseif(isset($_POST["proceso"]) and $_POST["proceso"]=="update")
{
$reg = $tra->ActualizarProductos();
exit;
}        
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Ing. Ruben Chirinos">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title></title>

    <!-- Menu CSS -->
    <link href="assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <!-- toast CSS -->
    <link href="assets/plugins/bower_components/toast-master/css/jquery.toast.css" rel="stylesheet">
    <!-- Sweet-Alert -->
    <link rel="stylesheet" href="assets/css/sweetalert.css">
    <!-- animation CSS -->
    <link href="assets/css/animate.css" rel="stylesheet">
    <!-- needed css -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- color CSS -->
    <link href="assets/css/default.css" id="theme" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

</head>

<body onLoad="muestraReloj()" class="fix-header">
    
   <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-boxed-layout="full" data-boxed-layout="boxed" data-header-position="fixed" data-sidebar-position="fixed" class="mini-sidebar">               
                    
    
        <!-- INICIO DE MENU -->
        <?php include('menu.php'); ?>
        <!-- FIN DE MENU -->
   

        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb border-bottom">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
        <h5 class="font-medium text-uppercase mb-0"><i class="fa fa-tasks"></i> Gestión de Productos</h5>
                    </div>
                    <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
                        <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                            <ol class="breadcrumb mb-0 justify-content-end p-0">
                                <li class="breadcrumb-item">Mantenimiento</li>
                                <li class="breadcrumb-item active" aria-current="page">Productos</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="page-content container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
               
<!-- Row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-danger">
                <h4 class="card-title text-white"><i class="fa fa-pencil"></i> Gestión de Productos</h4>
            </div>

<?php  if (isset($_GET['codproducto']) && isset($_GET['codsucursal'])) {
      
      $reg = $tra->ProductosPorId(); ?>
      
<form class="form form-material" method="post" action="#" name="updateproductos" id="updateproductos" data-id="<?php echo $reg[0]["codproducto"] ?>" enctype="multipart/form-data">
        
    <?php } else { ?>
        
<form class="form form-material" method="post" action="#" name="saveproductos" id="saveproductos" enctype="multipart/form-data">
      
    <?php } ?>

            <div id="save">
            <!-- error will be shown here ! -->
            </div>

    <div class="form-body">

        <div class="card-body">

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Código de Producto: <span class="symbol required"></span></label>
                        <input type="hidden" name="idproducto" id="idproducto" <?php if (isset($reg[0]['idproducto'])) { ?> value="<?php echo $reg[0]['idproducto']; ?>"<?php } ?>>
                            <input type="hidden" name="proceso" id="proceso" <?php if (isset($reg[0]['idproducto'])) { ?> value="update" <?php } else { ?> value="save" <?php } ?>/>
                        <input type="text" class="form-control" name="codproducto" id="codproducto" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Código de Producto" autocomplete="off" <?php if (isset($reg[0]['codproducto'])) { ?> value="<?php echo $reg[0]['codproducto']; ?>" readonly="readonly" <?php } else { ?><?php } ?> required="" aria-required="true"/> 
                        <i class="fa fa-bolt form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Nombre de Producto: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="producto" id="producto" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nombre de Producto" autocomplete="off" <?php if (isset($reg[0]['producto'])) { ?> value="<?php echo $reg[0]['producto']; ?>" <?php } ?> required="" aria-required="true"/>
                        <i class="fa fa-pencil form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Fabricante de Producto: </label>
                        <input type="text" class="form-control" name="fabricante" id="fabricante" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nombre de Fabricante" autocomplete="off" <?php if (isset($reg[0]['fabricante'])) { ?> value="<?php echo $reg[0]['fabricante']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-pencil form-control-feedback"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Familia de Producto: <span class="symbol required"></span></label>
                        <i class="fa fa-bars form-control-feedback"></i>
                        <?php if (isset($reg[0]['codfamilia'])) { ?>
                        <select name="codfamilia" id="codfamilia" onChange="CargaSubfamilias(this.form.codfamilia.value);" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $familia = new Login();
                        $familia = $familia->ListarFamilias();
                        if($familia==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($familia);$i++){ ?>
                        <option value="<?php echo $familia[$i]['codfamilia'] ?>"<?php if (!(strcmp($reg[0]['codfamilia'], htmlentities($familia[$i]['codfamilia'])))) { echo "selected=\"selected\"";} ?>><?php echo $familia[$i]['nomfamilia'] ?></option>        
                        <?php } } ?>
                        </select>  
                        <?php } else { ?>
                        <select name="codfamilia" id="codfamilia" onChange="CargaSubfamilias(this.form.codfamilia.value);" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $familia = new Login();
                        $familia = $familia->ListarFamilias();
                        if($familia==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($familia);$i++){ ?>
                        <option value="<?php echo $familia[$i]['codfamilia'] ?>"><?php echo $familia[$i]['nomfamilia'] ?></option>        
                        <?php } } ?>
                        </select>
                        <?php } ?>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Subfamilia de Producto: </label>
                        <i class="fa fa-bars form-control-feedback"></i>
                        <?php if (isset($reg[0]['codsubfamilia'])) { ?>
                        <select name="codsubfamilia" id="codsubfamilia" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $subfamilia = new Login();
                        $subfamilia = $subfamilia->ListarSubfamilias();
                        if($subfamilia==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($subfamilia);$i++){ ?>
                        <option value="<?php echo $subfamilia[$i]['codsubfamilia']; ?>"<?php if (!(strcmp($reg[0]['codsubfamilia'], htmlentities($subfamilia[$i]['codsubfamilia'])))) { echo "selected=\"selected\""; } ?>><?php echo $subfamilia[$i]['nomsubfamilia']; ?></option> 
                        <?php } } ?>
                        </select>
                        <?php } else { ?>
                        <select name="codsubfamilia" id="codsubfamilia" class='form-control' required="" aria-required="true">
                        <option value=""> -- SIN RESULTADOS -- </option>
                        </select> 
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Marca de Producto: <span class="symbol required"></span></label>
                        <i class="fa fa-bars form-control-feedback"></i>
                        <?php if (isset($reg[0]['codmarca'])) { ?>
                        <select name="codmarca" id="codmarca" onChange="CargaModelos(this.form.codmarca.value);" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $marca = new Login();
                        $marca = $marca->ListarMarcas();
                        if($marca==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($marca);$i++){ ?>
                        <option value="<?php echo $marca[$i]['codmarca'] ?>"<?php if (!(strcmp($reg[0]['codmarca'], htmlentities($marca[$i]['codmarca'])))) { echo "selected=\"selected\""; } ?>><?php echo $marca[$i]['nommarca'] ?></option> 
                        <?php } } ?>
                        </select>
                        <?php } else { ?>
                        <select name="codmarca" id="codmarca" onChange="CargaModelos(this.form.codmarca.value);" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $marca = new Login();
                        $marca = $marca->ListarMarcas();
                        if($marca==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($marca);$i++){ ?>
                        <option value="<?php echo $marca[$i]['codmarca'] ?>"><?php echo $marca[$i]['nommarca'] ?></option>
                        <?php } } ?>
                        </select>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Modelo de Producto: </label>
                        <i class="fa fa-bars form-control-feedback"></i>
                        <?php if (isset($reg[0]['codmodelo'])) { ?>
                        <select name="codmodelo" id="codmodelo" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $modelo = new Login();
                        $modelo = $modelo->ListarModelos();
                        if($modelo==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($modelo);$i++){ ?>
                        <option value="<?php echo $modelo[$i]['codmodelo']; ?>"<?php if (!(strcmp($reg[0]['codmodelo'], htmlentities($modelo[$i]['codmodelo'])))) { echo "selected=\"selected\""; } ?>><?php echo $modelo[$i]['nommodelo']; ?></option> 
                              <?php } } ?>
                        </select>
                        <?php } else { ?>
                        <select name="codmodelo" id="codmodelo" class='form-control' required="" aria-required="true">
                        <option value=""> -- SIN RESULTADOS -- </option>
                        </select> 
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Presentación de Producto: <span class="symbol required"></span></label>
                        <i class="fa fa-bars form-control-feedback"></i>
                        <?php if (isset($reg[0]['codpresentacion'])) { ?>
                        <select name="codpresentacion" id="codpresentacion" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $presentacion = new Login();
                        $presentacion = $presentacion->ListarPresentaciones();
                        if($presentacion==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($presentacion);$i++){ ?>
                        <option value="<?php echo $presentacion[$i]['codpresentacion'] ?>"<?php if (!(strcmp($reg[0]['codpresentacion'], htmlentities($presentacion[$i]['codpresentacion'])))) {echo "selected=\"selected\"";} ?>><?php echo $presentacion[$i]['nompresentacion'] ?></option>        
                        <?php } } ?>
                        </select> 
                        <?php } else { ?>
                        <select name="codpresentacion" id="codpresentacion" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $presentacion = new Login();
                        $presentacion = $presentacion->ListarPresentaciones();
                        if($presentacion==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($presentacion);$i++){ ?>
                        <option value="<?php echo $presentacion[$i]['codpresentacion'] ?>"><?php echo $presentacion[$i]['nompresentacion'] ?></option>        
                        <?php } } ?>
                        </select> 
                        <?php } ?> 
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Color de Producto: </label>
                        <i class="fa fa-bars form-control-feedback"></i>
                        <?php if (isset($reg[0]['codcolor'])) { ?>
                        <select name="codcolor" id="codcolor" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $color = new Login();
                        $color = $color->ListarColores();
                        if($color==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($color);$i++){ ?>
                        <option value="<?php echo $color[$i]['codcolor'] ?>"<?php if (!(strcmp($reg[0]['codcolor'], htmlentities($color[$i]['codcolor'])))) { echo "selected=\"selected\""; } ?>><?php echo $color[$i]['nomcolor'] ?></option>        
                        <?php } } ?>
                        </select>  
                        <?php } else { ?>
                        <select name="codcolor" id="codcolor" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $color = new Login();
                        $color = $color->ListarColores();
                        if($color==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($color);$i++){ ?>
                        <option value="<?php echo $color[$i]['codcolor'] ?>"><?php echo $color[$i]['nomcolor'] ?></option><?php } } ?>
                        </select> 
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Origen de Producto: </label>
                        <i class="fa fa-bars form-control-feedback"></i>
                        <?php if (isset($reg[0]['codorigen'])) { ?>
                        <select name="codorigen" id="codorigen" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $origen = new Login();
                        $origen = $origen->ListarOrigenes();
                        if($origen==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($origen);$i++){ ?>
                        <option value="<?php echo $origen[$i]['codorigen'] ?>"<?php if (!(strcmp($reg[0]['codorigen'], htmlentities($origen[$i]['codorigen'])))) { echo "selected=\"selected\""; } ?>><?php echo $origen[$i]['nomorigen'] ?></option>        
                        <?php } } ?>
                        </select>  
                        <?php } else { ?>
                        <select name="codorigen" id="codorigen" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $origen = new Login();
                        $origen = $origen->ListarOrigenes();
                        if($origen==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($origen);$i++){ ?>
                        <option value="<?php echo $origen[$i]['codorigen'] ?>"><?php echo $origen[$i]['nomorigen'] ?></option>        
                        <?php } } ?>
                             </select>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Año de Producto: </label>
                        <input type="text" class="form-control" name="year" id="year" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Año de Producto" autocomplete="off" <?php if (isset($reg[0]['year'])) { ?> value="<?php echo $reg[0]['year']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-pencil form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Part Number: </label>
                        <input type="text" class="form-control" name="nroparte" id="nroparte" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nº de Parte" autocomplete="off" <?php if (isset($reg[0]['nroparte'])) { ?> value="<?php echo $reg[0]['nroparte']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-pencil form-control-feedback"></i> 
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Nº de Lote: </label>
                        <input type="text" class="form-control" name="lote" id="lote" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nº de Lote" autocomplete="off" <?php if (isset($reg[0]['lote'])) { ?> value="<?php echo $reg[0]['lote']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-pencil form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Peso: </label>
                        <input type="text" class="form-control" name="peso" id="peso" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Peso de Producto" autocomplete="off" <?php if (isset($reg[0]['peso'])) { ?> value="<?php echo $reg[0]['peso']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-pencil form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Precio de Compra: <span class="symbol required"></span></label>
                        <input type="hidden" name="porcentaje" id="porcentaje" <?php if ($_SESSION['acceso'] == "administradorG") { ?> value="0.00" <?php } else { ?> value="<?php echo $_SESSION['porcentaje']; ?>" <?php } ?>>
                        <input type="text" class="form-control calculoprecio" name="preciocompra" id="preciocompra" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Ingrese Precio de Compra" autocomplete="off" <?php if (isset($reg[0]['preciocompra'])) { ?> value="<?php echo $reg[0]['preciocompra']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-tint form-control-feedback"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Precio Venta x Menor: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="precioxmenor" id="precioxmenor" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Ingrese Precio Venta x Menor" autocomplete="off" <?php if (isset($reg[0]['precioxmenor'])) { ?> value="<?php echo $reg[0]['precioxmenor']; ?>" <?php } ?>  required="" aria-required="true"/>  
                        <i class="fa fa-tint form-control-feedback"></i>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Precio Venta x Mayor: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="precioxmayor" id="precioxmayor" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Ingrese Precio Venta x Mayor" autocomplete="off" <?php if (isset($reg[0]['precioxmayor'])) { ?> value="<?php echo $reg[0]['precioxmayor']; ?>" <?php } ?>  required="" aria-required="true"/>  
                        <i class="fa fa-tint form-control-feedback"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Precio Venta Público: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="precioxpublico" id="precioxpublico" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Ingrese Precio Venta Público" autocomplete="off" <?php if (isset($reg[0]['precioxpublico'])) { ?> value="<?php echo $reg[0]['precioxpublico']; ?>" <?php } ?>  required="" aria-required="true"/>  
                        <i class="fa fa-tint form-control-feedback"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Existencia de Producto: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="existencia" id="existencia" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Existencia de Producto" autocomplete="off" <?php if (isset($reg[0]['existencia'])) { ?> value="<?php echo $reg[0]['existencia']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-bolt form-control-feedback"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Stock Óptimo: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="stockoptimo" id="stockoptimo" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Stock Óptimo" autocomplete="off" <?php if (isset($reg[0]['stockoptimo'])) { ?> value="<?php echo $reg[0]['stockoptimo']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-bolt form-control-feedback"></i> 
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Stock Medio: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="stockmedio" id="stockmedio" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Stock Medio" autocomplete="off" <?php if (isset($reg[0]['stockmedio'])) { ?> value="<?php echo $reg[0]['stockmedio']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-bolt form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Stock Minimo: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="stockminimo" id="stockminimo" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Stock Minimo" autocomplete="off" <?php if (isset($reg[0]['stockminimo'])) { ?> value="<?php echo $reg[0]['stockminimo']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-bolt form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label"><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?> de Producto: <span class="symbol required"></span></label>
                        <i class="fa fa-bars form-control-feedback"></i>
                        <?php if (isset($reg[0]['ivaproducto'])) { ?>
                        <select name="ivaproducto" id="ivaproducto" class="form-control" required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
<option value="SI"<?php if (!(strcmp('SI', $reg[0]['ivaproducto']))) {echo "selected=\"selected\"";} ?>>SI</option>
<option value="NO"<?php if (!(strcmp('NO', $reg[0]['ivaproducto']))) {echo "selected=\"selected\"";} ?>>NO</option>
                        </select>
                        <?php } else { ?>
                        <select name="ivaproducto" id="ivaproducto" class="form-control" required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                        </select>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Descuento de Producto: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="descproducto" id="descproducto" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Ingrese Descuento de Producto" autocomplete="off" <?php if (isset($reg[0]['descproducto'])) { ?> value="<?php echo $reg[0]['descproducto']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-tint form-control-feedback"></i>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Código de Barra: </label>
                        <input type="text" class="form-control" name="codigobarra" id="codigobarra" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Código de Barra" autocomplete="off" <?php if (isset($reg[0]['codigobarra'])) { ?> value="<?php echo $reg[0]['codigobarra']; ?>" <?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-barcode form-control-feedback"></i> 
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Fecha de Elaboración: </label>
                        <input type="text" class="form-control calendario" name="fechaelaboracion" id="fechaelaboracion" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Fecha de Elaboración" autocomplete="off" <?php if (isset($reg[0]['fechaelaboracion'])) { ?> value="<?php echo $reg[0]['fechaelaboracion'] == '0000-00-00' ? "" : date("d-m-Y",strtotime($reg[0]['fechaelaboracion'])); ?>"<?php } ?> required="" aria-required="true"/>
                        <i class="fa fa-calendar form-control-feedback"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Fecha de Exp. Óptimo: </label>
                        <input type="text" class="form-control expira" name="fechaoptimo" id="fechaoptimo" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Fecha de Exp. Óptimo" autocomplete="off" <?php if (isset($reg[0]['fechaoptimo'])) { ?> value="<?php echo $reg[0]['fechaoptimo'] == '0000-00-00' ? "" : date("d-m-Y",strtotime($reg[0]['fechaoptimo'])); ?>"<?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-calendar form-control-feedback"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Fecha de Exp. Medio: </label>
                        <input type="text" class="form-control expira" name="fechamedio" id="fechamedio" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Fecha de Exp. Medio" autocomplete="off" <?php if (isset($reg[0]['fechamedio'])) { ?> value="<?php echo $reg[0]['fechamedio'] == '0000-00-00' ? "" : date("d-m-Y",strtotime($reg[0]['fechamedio'])); ?>"<?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-calendar form-control-feedback"></i>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Fecha de Exp. Minimo: </label>
                        <input type="text" class="form-control expira" name="fechaminimo" id="fechaminimo" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Fecha de Exp. Minimo" autocomplete="off" <?php if (isset($reg[0]['fechaminimo'])) { ?> value="<?php echo $reg[0]['fechaminimo'] == '0000-00-00' ? "" : date("d-m-Y",strtotime($reg[0]['fechaminimo'])); ?>"<?php } ?> required="" aria-required="true"/>  
                        <i class="fa fa-calendar form-control-feedback"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                        <label class="control-label">Seleccione Proveedor: <span class="symbol required"></span></label>
                        <i class="fa fa-bars form-control-feedback"></i>
                        <?php if (isset($reg[0]['codproveedor'])) { ?>
                        <select name="codproveedor" id="codproveedor" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $proveedor = new Login();
                        $proveedor = $proveedor->ListarProveedores();
                        if($proveedor==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($proveedor);$i++){ ?>
                        <option value="<?php echo $proveedor[$i]['codproveedor'] ?>"<?php if (!(strcmp($reg[0]['codproveedor'], htmlentities($proveedor[$i]['codproveedor'])))) {echo "selected=\"selected\""; } ?>><?php echo $proveedor[$i]['nomproveedor'] ?></option>        
                        <?php } } ?>
                        </select>
                        <?php } else { ?>
                        <select name="codproveedor" id="codproveedor" class='form-control' required="" aria-required="true">
                        <option value=""> -- SELECCIONE -- </option>
                        <?php
                        $proveedor = new Login();
                        $proveedor = $proveedor->ListarProveedores();
                        if($proveedor==""){ 
                            echo "";
                        } else {
                        for($i=0;$i<sizeof($proveedor);$i++){ ?>
                        <option value="<?php echo $proveedor[$i]['codproveedor'] ?>"><?php echo $proveedor[$i]['nomproveedor'] ?></option>        
                        <?php } } ?>
                        </select>
                        <?php } ?> 
                    </div>
                </div>

        <?php if ($_SESSION['acceso'] == "administradorG") { ?> 

                <div class="col-md-3"> 
                    <div class="form-group has-feedback"> 
                       <label class="control-label">Seleccione Sucursal: <span class="symbol required"></span></label>
                       <i class="fa fa-bars form-control-feedback"></i>
                       <?php if (isset($reg[0]['codsucursal'])) { ?>
                       <select name="codsucursal" id="codsucursal" class="form-control" required="" aria-required="true">
                       <option value=""> -- SELECCIONE -- </option>
                       <?php
                       $sucursal = new Login();
                       $sucursal = $sucursal->ListarSucursales();
                        if($sucursal==""){ 
                            echo "";
                        } else {
                       for($i=0;$i<sizeof($sucursal);$i++){ ?>
                       <option value="<?php echo $sucursal[$i]['codsucursal'] ?>"<?php if (!(strcmp($reg[0]['codsucursal'], htmlentities($sucursal[$i]['codsucursal'])))) { echo "selected=\"selected\""; } ?>><?php echo $sucursal[$i]['cuitsucursal'].": ".$sucursal[$i]['razonsocial'] ?></option>        
                       <?php } } ?>
                       </select>
                       <?php } else { ?>
                       <select name="codsucursal" id="codsucursal" class="form-control" required="" aria-required="true">
                       <option value=""> -- SELECCIONE -- </option>
                       <?php
                       $sucursal = new Login();
                       $sucursal = $sucursal->ListarSucursales();
                        if($sucursal==""){ 
                            echo "";
                        } else {
                       for($i=0;$i<sizeof($sucursal);$i++){ ?>
                       <option value="<?php echo $sucursal[$i]['codsucursal'] ?>"><?php echo $sucursal[$i]['cuitsucursal'].": ".$sucursal[$i]['razonsocial'] ?></option>
                       <?php } } ?>
                       </select>
                       <?php } ?> 
                    </div> 
                </div>

        <?php } else {  ?> 

                <div class="col-md-3"> 
                    <div class="form-group has-feedback"> 
                      <label class="control-label">Sucursal Asignada: <span class="symbol required"></span></label> 
                      <input type="hidden" class="form-control" name="codsucursal" id="codsucursal" value="<?php echo $_SESSION["codsucursal"]; ?>">
                      <input type="text" class="form-control" name="sucursal" id="sucursal" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" value="<?php echo $_SESSION["razonsocial"]; ?>" readonly="readonly">
                      <i class="fa fa-bank form-control-feedback"></i>  
                    </div> 
                </div> 

        <?php } ?> 

                <div class="col-md-3">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 60px; height: 60px;"><?php if (isset($reg[0]['codproducto'])) {
                            if (file_exists("fotos/productos/".$reg[0]['codproducto'].".jpg")){
                                echo "<img src='fotos/productos/".$reg[0]['codproducto'].".jpg?".date('h:i:s')."' class='img-rounded' border='1' width='60' height='60' title='Foto del Producto' data-rel='tooltip'>"; 
                            }else{
                                echo "<img src='fotos/ninguna.png' class='img-rounded' border='1' width='60' height='60' title='SIN FOTO' data-rel='tooltip'>"; 
                            } } else {
                              echo "<img src='fotos/ninguna.png' class='img-rounded' border='1' width='60' height='60' title='SIN FOTO' data-rel='tooltip'>"; 
                          }
                          ?>
                        </div>
                    <div>
                        <span class="btn btn-success btn-file">
                        <span class="fileinput-new"><i class="fa fa-file-image-o"></i> Imagen</span>
                        <span class="fileinput-exists"><i class="fa fa-paint-brush"></i> Imagen</span>
                        <input type="file" size="10" data-original-title="Subir Fotografia" data-rel="tooltip" placeholder="Suba su Fotografia" name="imagen" id="imagen"  />
                        </span>
                        <a href="#" class="btn btn-dark fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times-circle"></i> Remover</a><small><p>Para Subir la Imagen debe tener en cuenta:<br> * La Imagen debe ser extension.jpg<br> * La imagen no debe ser mayor de 50 KB</p></small>                             
                        </div>
                    </div>
                </div>
            </div>

             <div class="text-right">
    <?php  if (isset($_GET['codproducto'])) { ?>
<button type="submit" name="btn-update" id="btn-update" class="btn btn-danger"><span class="fa fa-edit"></span> Actualizar</button>
<button class="btn btn-dark" type="reset"><span class="fa fa-trash-o"></span> Cancelar</button> 
    <?php } else { ?>
<button type="submit" name="btn-submit" id="btn-submit" class="btn btn-danger"><span class="fa fa-save"></span> Guardar</button>
<button class="btn btn-dark" type="reset"><span class="fa fa-trash-o"></span> Limpiar</button>
    <?php } ?>  
             </div>
          </div>
      </div>
    </form>
   </div>
  </div>
</div>
<!-- End Row -->


                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
                <i class="fa fa-copyright"></i> <span class="current-year"></span>.
            </footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
   

    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="assets/script/jquery.min.js"></script> 
    <script src="assets/js/bootstrap.js"></script>
    <!-- apps -->
    <script src="assets/js/app.min.js"></script>
    <script src="assets/js/app.init.horizontal-fullwidth.js"></script>
    <script src="assets/js/app-style-switcher.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="assets/js/perfect-scrollbar.js"></script>
    <script src="assets/js/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="assets/js/waves.js"></script>
    <!-- Sweet-Alert -->
    <script src="assets/js/sweetalert-dev.js"></script>
    <!--Menu sidebar -->
    <script src="assets/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="assets/js/custom.js"></script>

    <!-- Custom file upload -->
    <script src="assets/plugins/fileupload/bootstrap-fileupload.min.js"></script>

    <!-- script jquery -->
    <script type="text/javascript" src="assets/script/titulos.js"></script>
    <script type="text/javascript" src="assets/script/script2.js"></script>
    <script type="text/javascript" src="assets/script/validation.min.js"></script>
    <script type="text/javascript" src="assets/script/script.js"></script>
    <!-- script jquery -->

    <!-- Calendario -->
    <link rel="stylesheet" href="assets/calendario/jquery-ui.css" />
    <script src="assets/calendario/jquery-ui.js"></script>
    <script src="assets/script/jscalendario.js"></script>
    <script src="assets/script/autocompleto.js"></script>
    <!-- Calendario -->

    <!-- jQuery -->
    <script src="assets/plugins/noty/packaged/jquery.noty.packaged.min.js"></script>
    <!-- jQuery -->
    
</body>
</html>

<?php } else { ?>   
        <script type='text/javascript' language='javascript'>
        alert('NO TIENES PERMISO PARA ACCEDER A ESTA PAGINA.\nCONSULTA CON EL ADMINISTRADOR PARA QUE TE DE ACCESO')  
        document.location.href='productos'   
        </script> 
<?php } } else { ?>
        <script type='text/javascript' language='javascript'>
        alert('NO TIENES PERMISO PARA ACCEDER AL SISTEMA.\nDEBERA DE INICIAR SESION')  
        document.location.href='logout'  
        </script> 
<?php } ?>