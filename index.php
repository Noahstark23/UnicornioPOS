<?php
require_once("class/class.php");

$tra = new Login();

if(isset($_POST["proceso"]) and $_POST["proceso"]=="login")
{
  $log = $tra->Logueo();
  exit;
}
elseif(isset($_POST["proceso"]) and $_POST["proceso"]=="recuperar")
{
  $reg = $tra->RecuperarPassword();
  exit;
}

?>
<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="Ing. Ruben Chirinos">
<link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
<title></title>
<!-- Bootstrap Core CSS -->
<link href="assets/css/bootstrap.css" rel="stylesheet">
<!-- animation CSS -->
<link href="assets/css/animate.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="assets/css/index.css" rel="stylesheet">
<!-- color CSS -->
<link href="assets/css/default.css" id="theme" rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<!-- script jquery -->
<script src="assets/script/jquery.min.js"></script> 
<script type="text/javascript" src="assets/script/titulos.js"></script>
<script type="text/javascript" src="assets/script/validation.min.js"></script>
<script type="text/javascript" src="assets/script/script.js"></script>
<!-- script jquery -->

</head>
<body>

<!-- Preloader -->
<div class="preloader" style="display: none;">
  <div class="cssload-speeding-wheel"></div>
</div>

<section id="wrapper" class="new-login-register">
     
      <div class="lg-info-panel">
              <div class="inner-panel">
                  <a href="javascript:void(0)" class="p-20 di"></a>
                  <div class="lg-content">
                      <h2>SISTEMA MULTISUCURSAL PARA LA GESTIÓN DE VENTAS E INVENTARIO </h2>
                      <p class="text-muted">Siempre pensando en el gusto de nuestros Clientes.</p>
                  </div>
              </div>
      </div>


      <div class="new-login-box">
                <div class="white-box">

            <form class="form form-material new-lg-form" name="loginform" id="loginform" action="">


            <h3 class="box-title m-b-0">Login de Acceso</h3>
            <small class="text-danger">Introduzca sus datos a continuación</small>

                    <div id="login">
        <!-- error will be shown here ! -->
                    </div>
                    
            <div class="row">
              <div class="col-md-12 m-t-20">
                <div class="form-group has-feedback">
                  <label class="control-label">Ingrese su Usuario: <span class="symbol required"></span></label>
                  <input type="hidden" name="proceso" id="proceso" value="login"/>
                  <input type="text" class="form-control" placeholder="Ingrese su Usuario" name="usuario" id="usuario" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" required="" aria-required="true"> 
                  <i class="fa fa-user form-control-feedback"></i>                
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="form-group has-feedback">
                  <label class="control-label">Ingrese su Password: <span class="symbol required"></span></label>
                  <input class="form-control" type="password" placeholder="Ingrese su Password" name="password" id="password" onKeyUp="this.value=this.value.toUpperCase();" autocomplete="off" required="" aria-required="true">
                  <i class="fa fa-key form-control-feedback"></i>               
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-12">
                <a href="javascript:void(0)" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock"></i> Olvidaste tu Contraseña?</a>          
              </div>
            </div>

            <div class="form-group text-center m-t-20">
              <div class="col-xs-12">
                <button class="btn btn-danger btn-lg btn-block text-uppercase" data-toggle="tooltip" data-placement="top" title="" data-original-title="Haga clic aquí para iniciar sesión" name="btn-login" id="btn-login" type="submit"><span class="fa fa-sign-in"></span> Acceder</button>
              </div>
            </div>

          </form>

          <form class="form form-material new-lg-form" name="recoverform" id="recoverform" action="">

            <div id="recover">
            <!-- error will be shown here ! -->
            </div>

            <h3 class="box-title m-b-0">Recuperar Password</h3>
            <small class="text-danger">Ingrese su dirección de correo electrónico y le enviaremos instrucciones sobre como restablecer su Contraseña.</small>
            
            <div class="row">
              <div class="col-md-12 m-t-20">
                <div class="form-group has-feedback">
                  <label class="control-label">Correo Electrónico: <span class="symbol required"></span></label>
                  <input type="hidden" name="proceso" id="proceso" value="recuperar"/>
                  <input type="text" class="form-control" name="email" id="email" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese su Correo Electronico" autocomplete="off" required="" aria-required="true"/> 
                  <i class="fa fa-envelope-o form-control-feedback"></i>                            
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-12">
                 <a href="javascript:void(0)" id="to-login" class="text-dark pull-right"><i class="fa fa-arrow-circle-left"></i> Acceder al Sistema</a>
              </div>
            </div>

            <div class="form-group text-center m-t-20">
              <div class="col-xs-12">
                <button class="btn btn-danger btn-lg btn-block text-uppercase" name="btn-recuperar" id="btn-recuperar" type="submit"><span class="fa fa-check-square-o"></span> Recuperar Password</button>
              </div>
            </div>
          </form>
        </div>
      </div>            
  
  
</section>
<!-- jQuery -->
<script src="assets/js/jquery.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="assets/js/bootstrap.js"></script>
<!-- Menu Plugin JavaScript -->
<script src="assets/js/sidebar-nav.js"></script>

<!--slimscroll JavaScript -->
<script src="assets/js/jquery_002.js"></script>
<!--Wave Effects -->
<script src="assets/js/waves.js"></script>
<!-- Custom Theme JavaScript -->
<script src="assets/js/custom.js"></script>
<!-- jQuery -->
<script src="assets/plugins/noty/packaged/jquery.noty.packaged.min.js"></script>


</body></html>