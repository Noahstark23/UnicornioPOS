<?php
require_once("class/class.php"); 
if(isset($_SESSION['acceso'])) { 
    if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria" || $_SESSION["acceso"]=="cajero") {

$tra = new Login();
$ses = $tra->ExpiraSession();

$arqueo = new Login();
$arqueo = $arqueo->ArqueoCajaPorUsuario();

if(isset($_POST["proceso"]) and $_POST["proceso"]=="save")
{
$reg = $tra->RegistrarPago();
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
          
 <!--############################## MODAL PARA ABONOS DE CREDITOS DE VENTAS ######################################-->
<!-- sample modal content -->
<div id="ModalAbonos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title text-white" id="myModalLabel"><i class="fa fa-pencil"></i> Detalle de Crédito</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><img src="assets/images/close.png"/></button>
            </div>
            
        <form class="form form-material" name="saveabonoscliente" id="saveabonoscliente" action="#">
                
               <div class="modal-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                        <label class="control-label">Nº de Documento: <span class="symbol required"></span></label>
                        <input type="hidden" name="codcaja" id="codcaja" value="<?php echo $var = $arqueo == '' ? "SIN ARQUEO" : $arqueo[0]["codcaja"]; ?>">
                        <input type="hidden" name="proceso" id="proceso" value="save"/>
                        <input type="hidden" name="codsucursal" id="codsucursal">
                        <input type="hidden" name="codcliente" id="codcliente">
                        <input type="hidden" name="codventa" id="codventa">
                        <input type="hidden" name="totaldebe" id="totaldebe">
                       <input type="text" class="form-control" name="dnicliente" id="dnicliente" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nº de Documento" autocomplete="off" disabled="" aria-required="true"/> 
                            <i class="fa fa-bolt form-control-feedback"></i>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Nombre de Cliente: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="nomcliente" id="nomcliente" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nombre de Cliente" autocomplete="off" disabled="" aria-required="true"/>  
                            <i class="fa fa-pencil form-control-feedback"></i> 
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Nº de Venta: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="nroventa" id="nroventa" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Nº de Venta" autocomplete="off" disabled="" aria-required="true"/> 
                            <i class="fa fa-bolt form-control-feedback"></i>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Total Factura: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="totalfactura" id="totalfactura" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Total Factura" autocomplete="off" disabled="" aria-required="true"/>  
                            <i class="fa fa-tint form-control-feedback"></i> 
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Fecha de Emisión: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="fechaventa" id="fechaventa" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Fecha de Venta" autocomplete="off" disabled="" aria-required="true"/>  
                            <i class="fa fa-calendar form-control-feedback"></i> 
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Total Abono: <span class="symbol required"></span></label>
                       <input type="text" class="form-control" name="totalabono" id="totalabono" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Total de Abono" autocomplete="off" disabled="" aria-required="true"/> 
                            <i class="fa fa-bolt form-control-feedback"></i>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Total Debe: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="debe" id="debe" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Total Debe" autocomplete="off" disabled="" aria-required="true"/>  
                            <i class="fa fa-tint form-control-feedback"></i> 
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label class="control-label">Monto de Abono: <span class="symbol required"></span></label>
                            <input type="text" class="form-control" name="montoabono" id="montoabono" onKeyUp="this.value=this.value.toUpperCase();" onKeyPress="EvaluateText('%f', this);" onBlur="this.value = NumberFormat(this.value, '2', '.', '')" placeholder="Ingrese Monto de Abono" autocomplete="off" required="" aria-required="true"/>  
                            <i class="fa fa-tint form-control-feedback"></i> 
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="submit" name="btn-submit" id="btn-submit" class="btn btn-danger"><span class="fa fa-save"></span> Guardar</button>
                <button class="btn btn-dark" type="reset" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-trash-o"></span> Cerrar</button>
            </div>
        </form>

    </div>
    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal --> 



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
    <h5 class="font-medium text-uppercase mb-0"><i class="fa fa-tasks"></i> Créditos por Clientes</h5>
                    </div>
                    <div class="col-lg-9 col-md-8 col-xs-12 align-self-center">
                        <nav aria-label="breadcrumb" class="mt-2 float-md-right float-left">
                            <ol class="breadcrumb mb-0 justify-content-end p-0">
                                <li class="breadcrumb-item">Créditos</li>
                                <li class="breadcrumb-item active" aria-current="page">Por Clientes</li>
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
                <h4 class="card-title text-white"><i class="fa fa-search"></i> Créditos por Clientes</h4>
            </div>
            
        <form class="form form-material" method="post" action="#" name="creditosxclientes" id="creditosxclientes">

             <div class="form-body">

            <div id="save">
            <!-- error will be shown here ! -->
            </div>

                <div class="card-body">

    <?php if($_SESSION['acceso'] == "administradorG") { ?>

        <div class="row">

            <div class="col-md-4"> 
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
                      <option value="<?php echo encrypt($sucursal[$i]['codsucursal']); ?>"><?php echo $sucursal[$i]['cuitsucursal'].":".$sucursal[$i]['razonsocial']; ?></option>       
                  <?php } } ?>
                  </select>
                </div> 
            </div>

            <div class="col-md-8">
                <div class="form-group has-feedback">
                    <label class="control-label">Búsqueda de Clientes: <span class="symbol required"></span></label>
                    <input type="hidden" name="cliente" id="cliente">
                    <input type="text" class="form-control" name="busqueda" id="busqueda" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Realice la Búsqueda del Cliente por Nº de Documento, Nombres o Apellidos" autocomplete="off" required="" aria-required="true"/> 
                    <i class="fa fa-search form-control-feedback"></i> 
                </div>
            </div>
        </div>

    <?php } else { ?>


        <div class="row">

           <input type="hidden" name="codsucursal" id="codsucursal" value="<?php echo encrypt($_SESSION["codsucursal"]); ?>"> 

           <div class="col-md-12">
                <div class="form-group has-feedback">
                    <label class="control-label">Búsqueda de Clientes: <span class="symbol required"></span></label>
                    <input type="hidden" name="cliente" id="cliente">
                    <input type="text" class="form-control" name="busqueda" id="busqueda" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Realice la Búsqueda del Cliente por Nº de Documento, Nombres o Apellidos" autocomplete="off" required="" aria-required="true"/> 
                    <i class="fa fa-search form-control-feedback"></i> 
                </div>
            </div>
        </div>

    <?php } ?> 

                    <div class="text-right">
                        <button type="button" onClick="BuscarCreditosxClientes()" class="btn btn-danger"><span class="fa fa-search"></span> Realizar Búsqueda</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
<!-- End Row -->

<div id="muestracreditosxclientes"></div>


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