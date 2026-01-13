<?php
require_once("class/class.php"); 
if(isset($_SESSION['acceso'])) { 
     if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria") {

$tra = new Login();
$ses = $tra->ExpiraSession(); 

if(isset($_POST["proceso"]) and $_POST["proceso"]=="save")
{
$reg = $tra->RegistrarPedidos();
exit;
}
elseif(isset($_POST["proceso"]) and $_POST["proceso"]=="update")
{
$reg = $tra->ActualizarPedidos();
exit;
}  
elseif(isset($_POST["proceso"]) and $_POST["proceso"]=="agregar") 
{
$reg = $tra->AgregarDetallesPedidos();
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

<body onLoad="muestraReloj(); getTime();" class="fix-header">
    
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
             <h5 class="font-medium text-uppercase mb-0"><i class="fa fa-tasks"></i> Gestión de Pedidos</h5>
                    </div>
                    <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
                        <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                            <ol class="breadcrumb mb-0 justify-content-end p-0">
                                <li class="breadcrumb-item">Mantenimiento</li>
                                <li class="breadcrumb-item active" aria-current="page">Proveedores</li>
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
            <h4 class="card-title text-white"><i class="fa fa-pencil"></i> Gestión de Pedidos</h4>
            </div>

<?php  if (isset($_GET['codpedido']) && isset($_GET['codsucursal']) && decrypt($_GET["proceso"])=="U") {
      
$reg = $tra->PedidosPorId(); ?>
      
<form class="form form-material" method="post" action="#" name="updatepedidos" id="updatepedidos" data-id="<?php echo $reg[0]["codpedido"] ?>">

<?php } else if (isset($_GET['codpedido']) && isset($_GET['codsucursal']) && decrypt($_GET["proceso"])=="A") {
      
$reg = $tra->PedidosPorId(); ?>
      
<form class="form form-material" method="post" action="#" name="agregapedidos" id="agregapedidos" data-id="<?php echo $reg[0]["codpedido"] ?>">
        
<?php } else { ?>
        
 <form class="form form-material" method="post" action="#" name="savepedidos" id="savepedidos">

<?php } ?>
           

                <div id="save">
                   <!-- error will be shown here ! -->
               </div>

               <div class="form-body">

            <div class="card-body">

<input type="hidden" name="codpedido" id="codpedido" <?php if (isset($reg[0]['codpedido'])) { ?> value="<?php echo $reg[0]['codpedido']; ?>"<?php } ?>>

<input type="hidden" name="pedido" id="pedido" <?php if (isset($reg[0]['codpedido'])) { ?> value="<?php echo encrypt($reg[0]['codpedido']); ?>"<?php } ?>>
<input type="hidden" name="sucursal" id="sucursal" <?php if (isset($reg[0]['codsucursal'])) { ?> value="<?php echo encrypt($reg[0]['codsucursal']); ?>" <?php } ?>>
    
<input type="hidden" name="proceso" id="proceso" <?php if (isset($_GET['codpedido']) && decrypt($_GET["proceso"])=="U") { ?> value="update" <?php } elseif (isset($_GET['codpedido']) && decrypt($_GET["proceso"])=="A") { ?> value="agregar" <?php } else { ?> value="save" <?php } ?>>


    <h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="mdi mdi-truck"></i> Detalle del Proveedor</h3><hr>

    <div class="row">

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
          for($i=0;$i<sizeof($sucursal);$i++){
          ?>
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
          for($i=0;$i<sizeof($sucursal);$i++){
          ?>
          <option value="<?php echo $sucursal[$i]['codsucursal'] ?>"><?php echo $sucursal[$i]['cuitsucursal'].": ".$sucursal[$i]['razonsocial'] ?></option>
          <?php } } ?>
          </select>
          <?php } ?> 
        </div> 
      </div>

    <?php } else { ?>

        <div class="col-md-3"> 
          <div class="form-group has-feedback"> 
            <label class="control-label">Sucursal Asignada: <span class="symbol required"></span></label> 
            <input type="hidden" name="codsucursal" id="codsucursal" value="<?php echo $_SESSION["codsucursal"]; ?>">
            <input type="text" class="form-control" name="razonsocial" id="razonsocial" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" value="<?php echo $_SESSION["razonsocial"]; ?>" readonly="readonly">
            <i class="fa fa-bank form-control-feedback"></i>  
          </div> 
        </div>

    <?php } ?>

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
            <option value="<?php echo $proveedor[$i]['codproveedor'] ?>"<?php if (!(strcmp($reg[0]['codproveedor'], htmlentities($proveedor[$i]['codproveedor'])))) { echo "selected=\"selected\""; } ?>><?php echo $proveedor[$i]['cuitproveedor'].": ".$proveedor[$i]['nomproveedor'] ?></option>        
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
            <option value="<?php echo $proveedor[$i]['codproveedor'] ?>"><?php echo $proveedor[$i]['cuitproveedor'].": ".$proveedor[$i]['nomproveedor'] ?></option>        
            <?php } } ?>
            </select>
            <?php } ?> 
          </div> 
        </div>

        <div class="col-md-2"> 
          <div class="form-group has-feedback"> 
            <label class="control-label">Fecha de Pedido: <span class="symbol required"></span></label>
            <?php if (isset($reg[0]['fechapedido'])) { ?>
              <input class="form-control" type="text" name="fechapedido" id="fechapedido" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Fecha Pedido"  value="<?php echo $reg[0]['fechapedido'] == '0000-00-00' ? "" : date("d-m-Y H:i:s",strtotime($reg[0]['fechapedido'])); ?>" readonly="readonly"><?php } else { ?><input class="form-control" type="text" name="fecharegistro" id="fecharegistro" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Fecha Pedido" required="" aria-required="true" readonly="readonly"> <?php } ?>
            <i class="fa fa-calendar form-control-feedback"></i>
          </div> 
        </div>

        <div class="col-md-4">
          <div class="form-group has-feedback">
            <label class="control-label">Observaciones: <span class="symbol required"></span></label>
            <input type="text" class="form-control" name="observacionpedido" id="observacionpedido" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Observación de Pedido" autocomplete="off" <?php if (isset($reg[0]['observacionpedido'])) { ?> value="<?php echo $reg[0]['observacionpedido']; ?>" <?php } else { ?> value="SIN OBSERVACIONES" <?php } ?> required="" aria-required="true"/> 
            <i class="fa fa-comments form-control-feedback"></i> 
          </div>
        </div>
    </div>

<?php if (isset($_GET['codpedido']) && isset($_GET['codsucursal']) && decrypt($_GET["proceso"])=="U") { ?>

<h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle de Productos</h3>

<div id="detallespedidosupdate">

        <div class="table-responsive m-t-20">
            <table class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Cantidad</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Marcas</th>
                        <th>Modelo</th>
<?php if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") { ?><th>Acción</th><?php } ?>
                    </tr>
                </thead>
                <tbody>
<?php 
$tra = new Login();
$detalle = $tra->VerDetallesPedidos();
$a=1;
for($i=0;$i<sizeof($detalle);$i++){  
    ?>
                                 <tr class="text-center">
      <td><input type="text" class="form-control" name="cantpedido[]" id="cantpedido_<?php echo $a; ?>" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Cantidad" value="<?php echo $detalle[$i]["cantpedido"]; ?>" style="width: 80px;" onfocus="this.style.background=('#B7F0FF')" onBlur="this.style.background=('#e4e7ea')" title="Ingrese Cantidad" required="" aria-required="true"></td>
      <td><input type="hidden" name="coddetallepedido[]" id="coddetallepedido" value="<?php echo $detalle[$i]["coddetallepedido"]; ?>"><?php echo $detalle[$i]['codproducto']; ?></td>
      <td><?php echo $detalle[$i]['producto']; ?></td>
      <td><?php echo $detalle[$i]['nommarca']; ?></td>
      <td><?php echo $detalle[$i]['nommodelo']; ?></td>
 <?php if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") { ?><td>
<button type="button" class="btn btn-rounded btn-dark" onClick="EliminarDetallesPedidosUpdate('<?php echo encrypt($detalle[$i]["coddetallepedido"]); ?>','<?php echo encrypt($detalle[$i]["codpedido"]); ?>','<?php echo encrypt($detalle[$i]["codsucursal"]); ?>','<?php echo encrypt("DETALLESPEDIDOS") ?>')" title="Eliminar" ><i class="fa fa-trash-o"></i></button></td><?php } ?>
                                 </tr>
                     <?php } ?>
                </tbody>
            </table>
        </div>
</div>


<?php } else if (isset($_GET['codpedido']) && isset($_GET['codsucursal']) && decrypt($_GET["proceso"])=="A") { ?>

<h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle de Productos</h3>

<div id="detallespedidosagregar">

        <div class="table-responsive m-t-20">
            <table class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Nº</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Marcas</th>
                        <th>Modelo</th>
                        <th>Cantidad</th>
<?php if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") { ?><th>Acción</th><?php } ?>
                    </tr>
                </thead>
                <tbody>
<?php 
$tra = new Login();
$detalle = $tra->VerDetallesPedidos();
$a=1;
for($i=0;$i<sizeof($detalle);$i++){  
    ?>
                                 <tr class="text-center">
      <td><?php echo $a++; ?></td>
      <td><?php echo $detalle[$i]['codproducto']; ?></td>
      <td><?php echo $detalle[$i]['producto']; ?></td>
      <td><?php echo $detalle[$i]['nommarca']; ?></td>
      <td><?php echo $detalle[$i]['nommodelo']; ?></td>
      <td><?php echo $detalle[$i]['cantpedido']; ?></td>
 <?php if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS") { ?><td>
<button type="button" class="btn btn-rounded btn-dark" onClick="EliminarDetallesPedidosAgregar('<?php echo encrypt($detalle[$i]["coddetallepedido"]); ?>','<?php echo encrypt($detalle[$i]["codpedido"]); ?>','<?php echo encrypt($detalle[$i]["codsucursal"]); ?>','<?php echo encrypt("DETALLESPEDIDOS") ?>')" title="Eliminar" ><i class="fa fa-trash-o"></i></button></td><?php } ?>
                                 </tr>
                     <?php } ?>
                </tbody>
            </table>
        </div>
</div>

          
<h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle del Comprobante</h3>

          <hr>

          <input type="hidden" name="codmarca" id="codmarca">
          <input type="hidden" name="codmodelo" id="codmodelo">

          <div class="row">
                <div class="col-md-12"> 
                    <div class="form-group has-feedback"> 
                        <label class="control-label">Realice la Búsqueda de Producto: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="busquedaproductoc" id="busquedaproductoc" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Realice la Búsqueda por Código, Descripción o Nº de Barra">
                       <i class="fa fa-pencil form-control-feedback"></i> 
                    </div> 
                </div>
          </div>

          <div class="row">
                <div class="col-md-2">
                    <div class="form-group has-feedback">
                            <label class="control-label">Código de Producto: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="codproducto" id="codproducto" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Código" autocomplete="off"/> 
                            <i class="fa fa-bolt form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                            <label class="control-label">Nombre de Producto: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="producto" id="producto" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nombre de Producto" autocomplete="off"/>  
                            <i class="fa fa-pencil form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3"> 
                    <div class="form-group has-feedback"> 
                      <label class="control-label">Marca de Producto: <span class="symbol required"></span> </label>
                      <input class="form-control" type="text" name="marcas" id="marcas" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Marca de Producto">
                      <i class="fa fa-pencil form-control-feedback"></i> 
                    </div> 
                </div>

                <div class="col-md-2"> 
                    <div class="form-group has-feedback"> 
                      <label class="control-label">Modelo de Producto: </label>
                      <input type="text" class="form-control" name="modelos" id="modelos" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Modelo de Producto">
                      <i class="fa fa-pencil form-control-feedback"></i> 
                    </div> 
                </div>

                <div class="col-md-2"> 
                    <div class="form-group has-feedback"> 
                     <label class="control-label">Cantidad: <span class="symbol required"></span></label>
                     <input type="text" class="form-control agregapedido" name="cantidad" id="cantidad" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Cantidad">
                     <i class="fa fa-bolt form-control-feedback"></i> 
                    </div> 
                </div>
          </div>

         <div class="pull-right">
            <button type="button" id="AgregaPedido" class="btn btn-info"><span class="fa fa-cart-plus"></span> Agregar</button>
        </div></br>

        <div class="table-responsive m-t-40">
            <table id="carrito" class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Cantidad</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Marcas</th>
                        <th>Modelo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan=6><h4>NO HAY DETALLES AGREGADOS</h4></td>
                    </tr>
                </tbody>
            </table>
        </div>

<?php } else { ?>

<h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle del Comprobante</h3>

          <hr>

          <input type="hidden" name="codmarca" id="codmarca">
          <input type="hidden" name="codmodelo" id="codmodelo">

          <div class="row">
                <div class="col-md-12"> 
                    <div class="form-group has-feedback"> 
                        <label class="control-label">Realice la Búsqueda de Producto: <span class="symbol required"></span></label>
                        <input type="text" class="form-control" name="busquedaproductoc" id="busquedaproductoc" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Realice la Búsqueda por Código, Descripción o Nº de Barra">
                       <i class="fa fa-pencil form-control-feedback"></i> 
                    </div> 
                </div>
          </div>

          <div class="row">
                <div class="col-md-2">
                    <div class="form-group has-feedback">
                            <label class="control-label">Código de Producto: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="codproducto" id="codproducto" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Código" autocomplete="off"/> 
                            <i class="fa fa-bolt form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group has-feedback">
                            <label class="control-label">Nombre de Producto: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="producto" id="producto" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nombre de Producto" autocomplete="off"/>  
                            <i class="fa fa-pencil form-control-feedback"></i> 
                    </div>
                </div>

                <div class="col-md-3"> 
                    <div class="form-group has-feedback"> 
                      <label class="control-label">Marca de Producto: <span class="symbol required"></span> </label>
                      <input class="form-control" type="text" name="marcas" id="marcas" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Marca de Producto">
                      <i class="fa fa-pencil form-control-feedback"></i> 
                    </div> 
                </div>

                <div class="col-md-2"> 
                    <div class="form-group has-feedback"> 
                      <label class="control-label">Modelo de Producto: </label>
                      <input type="text" class="form-control" name="modelos" id="modelos" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Modelo de Producto">
                      <i class="fa fa-pencil form-control-feedback"></i> 
                    </div> 
                </div>

                <div class="col-md-2"> 
                    <div class="form-group has-feedback"> 
                     <label class="control-label">Cantidad: <span class="symbol required"></span></label>
                     <input type="text" class="form-control agregapedido" name="cantidad" id="cantidad" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Cantidad">
                     <i class="fa fa-bolt form-control-feedback"></i> 
                    </div> 
                </div>
          </div>

         <div class="pull-right">
            <button type="button" id="AgregaPedido" class="btn btn-info"><span class="fa fa-cart-plus"></span> Agregar</button>
        </div></br>

        <div class="table-responsive m-t-40">
            <table id="carrito" class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Cantidad</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Marcas</th>
                        <th>Modelo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan=6><h4>NO HAY DETALLES AGREGADOS</h4></td>
                    </tr>
                </tbody>
            </table>
        </div>

<?php } ?> 

<div class="clearfix"></div>
<hr>
              <div class="text-right">
<?php  if (isset($_GET['codpedido']) && isset($_GET['codsucursal']) && decrypt($_GET["proceso"])=="U") { ?>
<button type="submit" name="btn-update" id="btn-update" class="btn btn-danger"><span class="fa fa-edit"></span> Actualizar</button>
<button class="btn btn-dark" type="reset"><span class="fa fa-trash-o"></span> Cancelar</button> 
<?php } else if (isset($_GET['codpedido']) && isset($_GET['codsucursal']) && decrypt($_GET["proceso"])=="A") { ?>  
<button type="submit" name="btn-agregar" id="btn-agregar" class="btn btn-danger"><span class="fa fa-plus"></span> Agregar</button>
<button class="btn btn-dark" type="button" id="vaciarp"><span class="fa fa-trash-o"></span> Cancelar</button>
<?php } else { ?>  
<button type="submit" name="btn-submit" id="btn-submit" class="btn btn-danger"><span class="fa fa-save"></span> Guardar</button>
<button class="btn btn-dark" type="button" id="vaciarp"><i class="fa fa-trash-o"></i> Limpiar</button>
<?php } ?>
</div>

          </div>
       </div>
     </form>
   </div>
  </div>
</div>

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

    <!-- script jquery -->
    <script type="text/javascript" src="assets/script/titulos.js"></script>
    <script type="text/javascript" src="assets/script/script2.js"></script>
    <script type="text/javascript" src="assets/script/jspedidos.js"></script>
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
        document.location.href='panel'   
        </script> 
<?php } } else { ?>
        <script type='text/javascript' language='javascript'>
        alert('NO TIENES PERMISO PARA ACCEDER AL SISTEMA.\nDEBERA DE INICIAR SESION')  
        document.location.href='logout'  
        </script> 
<?php } ?>