<?php
require_once("class/class.php"); 
if(isset($_SESSION['acceso'])) { 
     if ($_SESSION["acceso"]=="administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria") {

$tra = new Login();
$ses = $tra->ExpiraSession(); 

$imp = new Login();
$imp = $imp->ImpuestosPorId();
$impuesto = $imp[0]['nomimpuesto'];
$valor = $imp[0]['valorimpuesto'];

$con = new Login();
$con = $con->ConfiguracionPorId();
$simbolo = "<strong>".$con[0]['simbolo']."</strong>";

if(isset($_POST["proceso"]) and $_POST["proceso"]=="save")
{
$reg = $tra->RegistrarCompras();
exit;
}
elseif(isset($_POST["proceso"]) and $_POST["proceso"]=="update")
{
$reg = $tra->ActualizarCompras();
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
     <h5 class="font-medium text-uppercase mb-0"><i class="fa fa-tasks"></i> Gestión de Compras</h5>
                    </div>
                    <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
                        <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                            <ol class="breadcrumb mb-0 justify-content-end p-0">
                                <li class="breadcrumb-item">Compras</li>
                                <li class="breadcrumb-item active" aria-current="page">Compras</li>
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
            <h4 class="card-title text-white"><i class="fa fa-pencil"></i> Gestión de Compras</h4>
            </div>

<?php if (isset($_GET['codcompra']) && isset($_GET['codsucursal']) && decrypt($_GET["proceso"])=="U") {
      
$reg = $tra->ComprasPorId(); ?>
      
<form class="form form-material" method="post" action="#" name="updatecompras" id="updatecompras" data-id="<?php echo $reg[0]["codcompra"] ?>">
        
<?php } else { ?>
        
 <form class="form form-material" method="post" action="#" name="savecompras" id="savecompras">

<?php } ?>
           

                <div id="save">
                   <!-- error will be shown here ! -->
               </div>

               <div class="form-body">

            <div class="card-body">

    <input type="hidden" name="idcompra" id="idcompra" <?php if (isset($reg[0]['idcompra'])) { ?> value="<?php echo $reg[0]['idcompra']; ?>"<?php } ?>>
    <input type="hidden" name="codsucursal" id="codsucursal" <?php if (isset($reg[0]['codsucursal'])) { ?> value="<?php echo $reg[0]['codsucursal']; ?>" <?php } else { ?> value="<?php echo $_SESSION["codsucursal"]; ?>"<?php } ?>>
     <input type="hidden" name="status" id="status" <?php if (isset($reg[0]['idcompra'])) { ?> value="<?php echo decrypt($_GET["status"]); ?>" <?php } ?>>
    
    <input type="hidden" name="proceso" id="proceso" <?php if (isset($reg[0]['idcompra'])) { ?> value="update" <?php } else { ?> value="save" <?php } ?>/>

    <input type="hidden" name="compra" id="compra" <?php if (isset($reg[0]['codcompra'])) { ?> value="<?php echo encrypt($reg[0]['codcompra']); ?>"<?php } ?>>
    <input type="hidden" name="sucursal" id="sucursal" <?php if (isset($reg[0]['codsucursal'])) { ?> value="<?php echo encrypt($reg[0]['codsucursal']); ?>" <?php } ?>>
        
    
    <h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-file-text"></i> Detalle de Factura</h3><hr>

    <div class="row"> 

            <?php if ($_SESSION['acceso'] == "administradorG") { ?>  

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
                <label class="control-label">Seleccione Sucursal: <span class="symbol required"></span></label>
                <i class="fa fa-bars form-control-feedback"></i>
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
            </div> 
        </div>

            <?php } else if ($_SESSION['acceso'] != "administradorG") { ?> 

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
                <label class="control-label">Sucursal Asignada: <span class="symbol required"></span></label> 
                <input type="hidden" name="codsucursal" id="codsucursal" value="<?php echo $_SESSION["codsucursal"]; ?>">
                <input type="text" class="form-control" name="razonsocial" id="razonsocial" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" value="<?php echo $_SESSION["razonsocial"]; ?>" disabled="disabled">
                <i class="fa fa-bank form-control-feedback"></i>  
            </div> 
        </div>   

            <?php } elseif (isset($reg[0]['codsucursal'])) { ?>

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
                <label class="control-label">Sucursal Asignada: <span class="symbol required"></span></label> 
                <input type="hidden" name="codsucursal" id="codsucursal" value="<?php echo $reg[0]["codsucursal"]; ?>">
                <input type="text" class="form-control" name="razonsocial" id="razonsocial" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" value="<?php echo $reg[0]["razonsocial"]; ?>" readonly="readonly">
                <i class="fa fa-bank form-control-feedback"></i>  
            </div> 
        </div>  

            <?php } ?>

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
                <label class="control-label">N° de Compra: <span class="symbol required"></span></label>
                <input class="form-control" type="text" name="codcompra" id="codcompra" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="N° Compra"  <?php if (isset($reg[0]['codcompra'])) { ?> value="<?php echo $reg[0]['codcompra']; ?>" readonly="" <?php } else { ?> required="" aria-required="true" <?php } ?>>
                <i class="fa fa-flash form-control-feedback"></i> 
            </div> 
        </div>

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
                <label class="control-label">Fecha de Emisión: <span class="symbol required"></span></label> 
                <input type="text" class="form-control calendario" name="fechaemision" id="fechaemision" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Fecha Emisión" <?php if (isset($reg[0]['fechaemision'])) { ?> value="<?php echo $reg[0]['fechaemision'] == '0000-00-00' ? "" : date("d-m-Y",strtotime($reg[0]['fechaemision'])); ?>"<?php } ?> required="" aria-required="true">
                <i class="fa fa-calendar form-control-feedback"></i>  
            </div> 
        </div>

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
                <label class="control-label">Fecha de Recepción: <span class="symbol required"></span></label> 
                <input type="text" class="form-control calendario" name="fecharecepcion" id="fecharecepcion" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Fecha Recepción" <?php if (isset($reg[0]['fecharecepcion'])) { ?> value="<?php echo $reg[0]['fecharecepcion'] == '0000-00-00' ? "" : date("d-m-Y",strtotime($reg[0]['fecharecepcion'])); ?>"<?php } ?> required="" aria-required="true">
                 <i class="fa fa-calendar form-control-feedback"></i>  
            </div> 
          </div>
    </div>


    <div class="row"> 

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

          <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
              <label class="control-label">Tipo de Compra: <span class="symbol required"></span></label>
              <i class="fa fa-bars form-control-feedback"></i>
              <?php if (isset($reg[0]['tipocompra'])) { ?>
        <select name="tipocompra" id="tipocompra" class="form-control" onChange="CargaFormaPagosCompras()" required="" aria-required="true">
                    <option value=""> -- SELECCIONE -- </option>
        <option value="CONTADO"<?php if (!(strcmp('CONTADO', $reg[0]['tipocompra']))) {echo "selected=\"selected\"";} ?>>CONTADO</option>
        <option value="CREDITO"<?php if (!(strcmp('CREDITO', $reg[0]['tipocompra']))) {echo "selected=\"selected\"";} ?>>CRÉDITO</option>
                </select>
            <?php } else { ?>
        <select name="tipocompra" id="tipocompra" class="form-control" onChange="CargaFormaPagosCompras()" required="" aria-required="true">
                    <option value=""> -- SELECCIONE -- </option>
                    <option value="CONTADO">CONTADO</option>
                    <option value="CREDITO">CRÉDITO</option>
                </select>
            <?php } ?>
           </div> 
         </div>

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
                <label class="control-label">Forma de Pago: <span class="symbol required"></span></label>
                <i class="fa fa-bars form-control-feedback"></i>

            <?php if (isset($reg[0]['formacompra']) && $reg[0]['tipocompra']=="CONTADO") { ?>

                <select name="formacompra" id="formacompra" class="form-control" required="" aria-required="true">
                <option value=""> -- SELECCIONE -- </option>
                <?php
                $pago = new Login();
                $pago = $pago->ListarMediosPagos();
               if($pago==""){ 
                echo "";
               } else {
                for($i=0;$i<sizeof($pago);$i++){ ?>
                <option value="<?php echo $pago[$i]['codmediopago'] ?>"<?php if (!(strcmp($reg[0]['formacompra'], htmlentities($pago[$i]['codmediopago'])))) {echo "selected=\"selected\""; } ?>><?php echo $pago[$i]['mediopago'] ?></option>       
                <?php } } ?> 
                </select>

            <?php } elseif (isset($reg[0]['formacompra']) && $reg[0]['tipocompra']=="CREDITO") { ?>

                <select name="formacompra" id="formacompra" class="form-control" disabled="" required="" aria-required="true">
                <option value=""> -- SELECCIONE -- </option>
                <?php
                $pago = new Login();
                $pago = $pago->ListarMediosPagos();
               if($pago==""){ 
                echo "";
               } else {
                for($i=0;$i<sizeof($pago);$i++){ ?>
                <option value="<?php echo $pago[$i]['codmediopago'] ?>"<?php if (!(strcmp($reg[0]['formacompra'], htmlentities($pago[$i]['codmediopago'])))) {echo "selected=\"selected\""; } ?>><?php echo $pago[$i]['mediopago'] ?></option>       
                <?php } } ?> 
                </select>

            <?php } else { ?>

                <select name="formacompra" id="formacompra" class="form-control" disabled="" required="" aria-required="true">
                <option value=""> -- SELECCIONE -- </option>
                <?php
                $pago = new Login();
                $pago = $pago->ListarMediosPagos();
               if($pago==""){ 
                echo "";
               } else {
                for($i=0;$i<sizeof($pago);$i++){  ?>
                <option value="<?php echo $pago[$i]['codmediopago'] ?>"><?php echo $pago[$i]['mediopago'] ?></option>       
                  <?php } } ?> 
              </select>

            <?php } ?>

            </div> 
        </div>


            <?php if (isset($reg[0]['fechavencecredito']) && $reg[0]['tipocompra']=="CONTADO") { ?>

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
               <label class="control-label">Fecha Vence Crédito: <span class="symbol required"></span></label> 
               <input type="text" class="form-control expira" name="fechavencecredito" id="fechavencecredito" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Fecha Vence Crédito" <?php if (isset($reg[0]['fechavencecredito'])) { ?> value="<?php echo $reg[0]['fechavencecredito'] == '0000-00-00' ? "" : date("d-m-Y",strtotime($reg[0]['fechavencecredito'])); ?>"<?php } ?> disabled="" required="" aria-required="true">
               <i class="fa fa-calendar form-control-feedback"></i>  
            </div> 
        </div> 

            <?php } elseif (isset($reg[0]['fechavencecredito']) && $reg[0]['tipocompra']=="CREDITO") { ?>

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
               <label class="control-label">Fecha Vence Crédito: <span class="symbol required"></span></label> 
               <input type="text" class="form-control expira" name="fechavencecredito" id="fechavencecredito" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Fecha Vence Crédito" <?php if (isset($reg[0]['fechavencecredito'])) { ?> value="<?php echo $reg[0]['fechavencecredito'] == '0000-00-00' ? "" : date("d-m-Y",strtotime($reg[0]['fechavencecredito'])); ?>"<?php } ?> required="" aria-required="true">
               <i class="fa fa-calendar form-control-feedback"></i>  
            </div> 
        </div> 

            <?php } else { ?>

        <div class="col-md-3"> 
            <div class="form-group has-feedback"> 
               <label class="control-label">Fecha Vence Crédito: <span class="symbol required"></span></label> 
               <input type="text" class="form-control expira" name="fechavencecredito" id="fechavencecredito" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Fecha Vence Crédito" disabled="" required="" aria-required="true">
               <i class="fa fa-calendar form-control-feedback"></i>  
            </div> 
        </div>  

            <?php } ?>

    </div>


<?php if (isset($_GET['codcompra']) && isset($_GET['codsucursal']) && decrypt($_GET["proceso"])=="U") { ?>

<h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle del Comprobante</h3>

<div id="detallescomprasupdate">

    <div class="table-responsive m-t-20">
            <table class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Cantidad</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Precio Unit.</th>
                        <th>Valor Total</th>
                        <th>Desc %</th>
                        <th><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?></th>
                        <th>Valor Neto</th>
<?php if ($_SESSION['acceso'] == "administradorS") { ?><th>Acción</th><?php } ?>
                    </tr>
                </thead>
                <tbody>
<?php 
$tra = new Login();
$detalle = $tra->VerDetallesCompras();
$a=1;
for($i=0;$i<sizeof($detalle);$i++){  
    ?>
                                 <tr class="text-center">
      <td>
      <input type="text" class="form-control" name="cantcompra[]" id="cantcompra<?php echo $a; ?>" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Cantidad" value="<?php echo $detalle[$i]["cantcompra"]; ?>" style="width: 80px;" onfocus="this.style.background=('#B7F0FF')" onBlur="this.style.background=('#e4e7ea')" title="Ingrese Cantidad" required="" aria-required="true">
      <input type="hidden" name="cantidadcomprabd[]" id="cantidadcomprabd" value="<?php echo $detalle[$i]["cantcompra"]; ?>">
      </td>
      
      <td>
      <input type="hidden" name="coddetallecompra[]" id="coddetallecompra" value="<?php echo $detalle[$i]["coddetallecompra"]; ?>">
      <input type="hidden" name="codproducto[]" id="codproducto" value="<?php echo $detalle[$i]["codproducto"]; ?>">
      <?php echo $detalle[$i]['codproducto']; ?>
      </td>
      
      <td><h5><?php echo $detalle[$i]['producto']; ?></h5><small>MARCA (<?php echo $detalle[$i]['nommarca'] == '' ? "*****" : $detalle[$i]['nommarca'] ?>) - MODELO (<?php echo $detalle[$i]['nommodelo'] == '' ? "*****" : $detalle[$i]['nommodelo'] ?>)</small></td>
      
      <td><input type="hidden" name="preciocompra[]" id="preciocompra" value="<?php echo $detalle[$i]["preciocomprac"]; ?>"><?php echo $simbolo.$detalle[$i]['preciocomprac']; ?></td>

      <td><input type="hidden" name="valortotal[]" id="valortotal" value="<?php echo $detalle[$i]["valortotal"]; ?>"><?php echo $simbolo.$detalle[$i]['valortotal']; ?></td>
      
      <td><input type="hidden" name="descfactura[]" id="descfactura" value="<?php echo $detalle[$i]["descfactura"]; ?>"><?php echo $simbolo.$detalle[$i]['totaldescuentoc']; ?><sup><?php echo $detalle[$i]['descfactura']; ?>%</sup></td>

      <td><input type="hidden" name="ivaproducto[]" id="ivaproducto" value="<?php echo $detalle[$i]["ivaproductoc"]; ?>"><?php echo $detalle[$i]['ivaproductoc'] == 'SI' ? $reg[0]['ivac']."%" : "(E)"; ?></td>

      <td><?php echo $simbolo.$detalle[$i]['valorneto']; ?></td>

 <?php if ($_SESSION['acceso'] == "administradorS") { ?><td>
<button type="button" class="btn btn-rounded btn-dark" onClick="EliminarDetallesComprasUpdate('<?php echo encrypt($detalle[$i]["coddetallecompra"]); ?>','<?php echo encrypt($detalle[$i]["codcompra"]); ?>','<?php echo encrypt($reg[0]["codproveedor"]); ?>','<?php echo encrypt($detalle[$i]["codsucursal"]); ?>','<?php echo encrypt("DETALLESCOMPRAS") ?>')" title="Eliminar" ><i class="fa fa-trash-o"></i></button></td><?php } ?>
                                 </tr>
                     <?php } ?>
                </tbody>
            </table><hr>

             <table id="carritototal" class="table-responsive">
                <tr>
    <td width="50">&nbsp;</td>
    <td width="250">
    <h5><label>Total Gravado <?php echo $reg[0]['ivac'] ?>%:</label></h5>
    </td>
                  
    <td width="250">
    <h5><?php echo $simbolo; ?><label id="lblsubtotal" name="lblsubtotal"><?php echo $reg[0]['subtotalivasic'] ?></label></h5>
    <input type="hidden" name="txtsubtotal" id="txtsubtotal" value="<?php echo $reg[0]['subtotalivasic'] ?>"/>
    </td>

    <td width="250">
    <h5><label>Total Exento 0%:</label></h5>
    </td>
    
    <td width="250">
    <h5><?php echo $simbolo; ?><label id="lblsubtotal2" name="lblsubtotal2"><?php echo $reg[0]['subtotalivanoc'] ?></label></h5>
    <input type="hidden" name="txtsubtotal2" id="txtsubtotal2" value="<?php echo $reg[0]['subtotalivanoc'] ?>"/>
    </td>

    <td class="text-center" width="250">
    <h2><b>Importe Total</b></h2>
    </td>
                </tr>
                <tr>
    <td>&nbsp;</td>
    <td>
    <h5><label><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?> <?php echo $reg[0]['ivac'] ?>%:<input type="hidden" name="iva" id="iva" autocomplete="off" value="<?php echo $reg[0]['ivac'] ?>"></label></h5>
    </td>
    
    <td>
    <h5><?php echo $simbolo; ?><label id="lbliva" name="lbliva"><?php echo $reg[0]['totalivac'] ?></label></h5>
    <input type="hidden" name="txtIva" id="txtIva" value="<?php echo $reg[0]['totalivac'] ?>"/>
    </td>

    <td>
    <h5><label>Desc. Global <input class="number" type="text" name="descuento" id="descuento" onKeyPress="EvaluateText('%f', this);" style="border-radius:4px;height:30px;width:70px;" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" value="<?php echo $reg[0]['descuentoc'] ?>">%:</label></h5>
    </td>

    <td>
    <h5><?php echo $simbolo; ?><label id="lbldescuento" name="lbldescuento"><?php echo $reg[0]['totaldescuentoc'] ?></label></h5>
    <input type="hidden" name="txtDescuento" id="txtDescuento" value="<?php echo $reg[0]['totaldescuentoc'] ?>"/>
    </td>

    <td class="text-center">
    <h2><?php echo $simbolo; ?><label id="lbltotal" name="lbltotal"><?php echo $reg[0]['totalpagoc'] ?></label></h2>
    <input type="hidden" name="txtTotal" id="txtTotal" value="<?php echo $reg[0]['totalpagoc'] ?>"/>
    </td>
                    </tr>
                  </table>
        </div>


</div>

<?php } else { ?>

        <input type="hidden" name="producto" id="producto">
        <input type="hidden" name="marcas" id="marcas">
        <input type="hidden" name="modelos" id="modelos">

     <h3 class="card-title text-dark m-0" style="font-weight:100;"><i class="fa fa-shopping-cart"></i> Detalle del Comprobante</h3><hr>

        <div class="row">
            <div class="col-md-12"> 
                <div class="form-group has-feedback"> 
                   <label class="control-label">Realice la Búsqueda de Producto: <span class="symbol required"></span></label>
                   <input type="hidden" name="codproducto" id="codproducto"/>
                   <input type="text" class="form-control" name="busquedaproductoc" id="busquedaproductoc" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Realice la Búsqueda por Código, Descripción o Nº de Barra">
                   <i class="fa fa-search form-control-feedback"></i> 
                </div> 
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"> 
                <div class="form-group has-feedback"> 
                    <label class="control-label">Cantidad Compra: <span class="symbol required"></span></label>
                    <input type="text" class="form-control agregacompra" name="cantidad" id="cantidad" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Cantidad Compra" autocomplete="off">
                    <i class="fa fa-bolt form-control-feedback"></i> 
                </div> 
            </div>

            <div class="col-md-3"> 
                <div class="form-group has-feedback"> 
                    <label class="control-label">Precio de Compra: <span class="symbol required"></span></label>
                    <input class="form-control calculoprecio agregacompra" type="text" name="preciocompra" id="preciocompra" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Precio de Compra">
                    <input type="hidden" name="precioconiva" id="precioconiva" value="0.00">                        
                    <i class="fa fa-tint form-control-feedback"></i> 
                </div> 
            </div>

            <div class="col-md-3"> 
                <div class="form-group has-feedback"> 
                    <label class="control-label">Dcto en Compra: <span class="symbol required"></span></label>
                    <input class="form-control agregacompra" type="text" name="descfactura" id="descfactura" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Descuento en Compra" value="0.00">
                    <i class="fa fa-tint form-control-feedback"></i> 
                </div> 
            </div>

            <div class="col-md-3"> 
                <div class="form-group has-feedback"> 
                    <label class="control-label">Dcto en Venta: <span class="symbol required"></span></label>
                    <input class="form-control agregacompra" type="text" name="descproducto" id="descproducto" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="Ingrese Descuento en Venta" value="0.00">
                    <i class="fa fa-tint form-control-feedback"></i> 
                </div> 
            </div>
        </div>
 
        <div class="row">
            <div class="col-md-3"> 
                <div class="form-group has-feedback"> 
                    <label class="control-label"><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?> de Producto: <span class="symbol required"></span></label>
                    <i class="fa fa-bars form-control-feedback"></i>
                    <select name="ivaproducto" id="ivaproducto" class="form-control">
                        <option value="">SELECCIONE</option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select> 
                </div> 
            </div>

            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Precio Venta x Menor: <span class="symbol required"></span></label>
                    <input type="text" class="form-control" name="precioxmenor" id="precioxmenor" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Precio Venta x Menor" autocomplete="off"  value="0.00"/>  
                    <i class="fa fa-tint form-control-feedback"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Precio Venta x Mayor: <span class="symbol required"></span></label>
                    <input type="text" class="form-control" name="precioxmayor" id="precioxmayor" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Precio Venta x Mayor" autocomplete="off" value="0.00"/>  
                    <i class="fa fa-tint form-control-feedback"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Precio Venta Público: <span class="symbol required"></span></label>
                    <input type="text" class="form-control" name="precioxpublico" id="precioxpublico" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Precio Venta Público" autocomplete="off" value="0.00"/>  
                    <i class="fa fa-tint form-control-feedback"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group has-feedback"> 
                    <label class="control-label">N° de Lote: <span class="symbol required"></span></label>
                    <input class="form-control agregacompra" type="text" name="lote" id="lote" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" placeholder="N° de Lote" value="0">
                    <i class="fa fa-flash form-control-feedback"></i> 
                </div> 
            </div>

            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Fecha de Elaboración: </label>
                    <input type="text" class="form-control calendario agregacompra" name="fechaelaboracion" id="fechaelaboracion" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Fecha de Elab." autocomplete="off"/>
                    <i class="fa fa-calendar form-control-feedback"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Fecha de Exp. Óptimo: </label>
                    <input type="text" class="form-control expira" name="fechaoptimo" id="fechaoptimo" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Fecha de Exp. Óptimo" autocomplete="off" /><i class="fa fa-calendar form-control-feedback"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Fecha de Exp. Medio: </label>
                    <input type="text" class="form-control expira" name="fechamedio" id="fechamedio" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Fecha de Exp. Medio" autocomplete="off" />
                    <i class="fa fa-calendar form-control-feedback"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Fecha de Exp. Minimo: </label>
                    <input type="text" class="form-control expira" name="fechaminimo" id="fechaminimo" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Fecha de Exp. Minimo" autocomplete="off"/> 
                    <i class="fa fa-calendar form-control-feedback"></i>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Stock Óptimo: <span class="symbol required"></span></label>
                    <input type="text" class="form-control" name="stockoptimo" id="stockoptimo" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Stock Óptimo" autocomplete="off"/>  
                    <i class="fa fa-bolt form-control-feedback"></i> 
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Stock Medio: <span class="symbol required"></span></label>
                    <input type="text" class="form-control" name="stockmedio" id="stockmedio" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Stock Medio" autocomplete="off"/>  
                    <i class="fa fa-bolt form-control-feedback"></i> 
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group has-feedback">
                    <label class="control-label">Stock Minimo: <span class="symbol required"></span></label>
                    <input type="text" class="form-control" name="stockminimo" id="stockminimo" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Stock Minimo" autocomplete="off"/>  
                    <i class="fa fa-bolt form-control-feedback"></i> 
                </div>
            </div>
        </div>  

        
        <div class="pull-right">
    <button type="button" id="AgregaCompra" class="btn btn-info"><span class="fa fa-cart-plus"></span> Agregar</button>
        </div></br>

        <div class="table-responsive m-t-40">
            <table id="carrito" class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th>Cantidad</th>
                        <th>Código</th>
                        <th>Descripción de Producto</th>
                        <th>Precio Unit.</th>
                        <th>Valor Total</th>
                        <th>Desc %</th>
                        <th><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?></th>
                        <th>Valor Neto</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan=9><h4>NO HAY DETALLES AGREGADOS</h4></td>
                    </tr>
                </tbody>
              </table><hr>

             <table id="carritototal" class="table-responsive">
                <tr>
    <td width="50">&nbsp;</td>
    <td width="250">
    <h5><label>Total Gravado <?php echo $valor == '' ? "0.00" : $imp[0]['valorimpuesto']; ?>%:</label></h5>
    </td>
                  
    <td width="250">
    <h5><?php echo $simbolo; ?><label id="lblsubtotal" name="lblsubtotal">0.00</label></h5>
    <input type="hidden" name="txtsubtotal" id="txtsubtotal" value="0.00"/>
    </td>

    <td width="250">
    <h5><label>Total Exento 0%:</label></h5>
    </td>
    
    <td width="250">
    <h5><?php echo $simbolo; ?><label id="lblsubtotal2" name="lblsubtotal2">0.00</label></h5>
    <input type="hidden" name="txtsubtotal2" id="txtsubtotal2" value="0.00"/>
    </td>

    <td class="text-center" width="250">
    <h2><b>Importe Total</b></h2>
    </td>
                </tr>
                <tr>
    <td>&nbsp;</td>
    <td>
    <h5><label><?php echo $impuesto == '' ? "Impuesto" : $imp[0]['nomimpuesto']; ?> <?php echo $valor == '' ? "0.00" : $imp[0]['valorimpuesto']; ?>%:<input type="hidden" name="iva" id="iva" autocomplete="off" value="<?php echo $valor == '' ? "0.00" : $imp[0]['valorimpuesto']; ?>"></label></h5>
    </td>
    
    <td>
    <h5><?php echo $simbolo; ?><label id="lbliva" name="lbliva">0.00</label></h5>
    <input type="hidden" name="txtIva" id="txtIva" value="0.00"/>
    </td>

    <td>
    <h5><label>Desc. Global <input class="number" type="text" name="descuento" id="descuento" onKeyPress="EvaluateText('%f', this);" style="border-radius:4px;height:30px;width:70px;" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" value="0.00">%:</label></h5>
    </td>

    <td>
    <h5><?php echo $simbolo; ?><label id="lbldescuento" name="lbldescuento">0.00</label></h5>
    <input type="hidden" name="txtDescuento" id="txtDescuento" value="0.00"/>
    </td>

    <td class="text-center">
    <h2><b><?php echo $simbolo; ?><label id="lbltotal" name="lbltotal">0.00</label></b></h2>
    <input type="hidden" name="txtTotal" id="txtTotal" value="0.00"/>
    <input type="hidden" name="txtTotalCompra" id="txtTotalCompra" value="0.00"/>
    </td>
                    </tr>
                  </table>
        </div>


<?php } ?> 

<div class="clearfix"></div>
<hr>
              <div class="text-right">
<?php  if (isset($_GET['codcompra']) && decrypt($_GET["proceso"])=="U") { ?>
<button type="submit" name="btn-update" id="btn-update" class="btn btn-danger"><span class="fa fa-edit"></span> Actualizar</button>
<button class="btn btn-dark" type="reset"><span class="fa fa-trash-o"></span> Cancelar</button> 
<?php } else if (isset($_GET['codcompra']) && decrypt($_GET["proceso"])=="A") { ?>  
<button type="submit" name="btn-agregar" id="btn-agregar" class="btn btn-danger"><span class="fa fa-plus"></span> Agregar</button>
<button class="btn btn-dark" type="button" id="vaciar2"><span class="fa fa-trash-o"></span> Cancelar</button>
<?php } else { ?>  
<button type="submit" name="btn-submit" id="btn-submit" class="btn btn-danger"><span class="fa fa-save"></span> Guardar</button>
<button class="btn btn-dark" type="button" id="vaciar"><i class="fa fa-trash-o"></i> Limpiar</button>
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
    <script type="text/javascript" src="assets/script/jscompras.js"></script>
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