<?php 
if(isset($_SESSION['acceso'])) { 
  if ($_SESSION['acceso'] == "administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria" || $_SESSION["acceso"]=="cajero") {

$count = new Login();
$p = $count->ContarRegistros();
?>

        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header" data-logobg="skin6">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="fa fa-navicon"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand" href="javascript:void(0)">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->

                    <?php if ($_SESSION['acceso'] == "administradorG") {

                        if (file_exists("fotos/logo-principal.png")){
                    echo "<img src='fotos/logo-principal.png' width='180' height='60' alt='Logo Principal' class='dark-logo'>"; 
                            } else {
                    echo "<img src='' width='180' height='60' alt='Logo Principal' class='dark-logo'>"; 
                            } 
                            
                        } else {

                        if (file_exists("fotos/sucursales/".$_SESSION['cuitsucursal'].".png")){
                    echo "<img src='fotos/sucursales/".$_SESSION['cuitsucursal'].".png' width='120' height='60' alt='Logo Principal' class='dark-logo'>"; 
                            } else {
                    echo "<img src='fotos/logo-principal.png' width='180' height='60' alt='Logo Principal' class='dark-logo'>"; 
                            }
                        }
                    ?>
                           <!-- <img src="assets/images/logo.png" width="185" height="40" alt="Logo Principal" class="dark-logo">
                             Light Logo icon -->
                            <img src="assets/images/logo-icon.png" alt="homepage" class="light-logo">
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text">
                             <!-- dark Logo text -->
                             <img src="" alt="" class="dark-logo">
                             <!-- Light Logo text -->    
                             <img src="assets/images/logo-icon.png" class="light-logo" alt="homepage">
                        </span>
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="mdi mdi-dots-horizontal"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin6">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->

 <ul class="navbar-nav float-left mr-auto">
                        <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>

                        <!-- ============================================================== -->
                        <!-- Iconos de Productos -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-cube font-24 text-danger"></i>
                                <div class="notify">
            <span class="<?php if($p[0]['poptimo']==0 && $p[0]['pmedio']==0 && $p[0]['pminimo']==0 && $p[0]['foptimo']==0 && $p[0]['fmedio']==0 && $p[0]['fminimo']==0) { } else { ?>heartbit<?php } ?>"></span>
            <span class="<?php if($p[0]['poptimo']==0 && $p[0]['pmedio']==0 && $p[0]['pminimo']==0 && $p[0]['foptimo']==0 && $p[0]['fmedio']==0 && $p[0]['fminimo']==0) { } else { ?>point<?php } ?>"></span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-left mailbox animated bounceInDown">
                                <span class="with-arrow"><span class="bg-primary"></span></span>
                                <ul class="list-style-none">
                                    <li>
                                        <div class="drop-title border-bottom">Notificaciones</div>
                                    </li>
                                    <li>
                                        <div class="message-center notifications">
                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="message-item">
                                                <i class="mdi mdi-cube fa-2x text-success"></i>
                                                <span class="mail-contnet">
                                                    <h5 class="message-title">Productos Stock Óptimo</h5> 
                                                    <span><?php echo $p[0]['poptimo'] ?></span> 
                                                </span>
                                            </a>
                                            <a href="javascript:void(0)" class="message-item">
                                                <i class="mdi mdi-cube fa-2x text-warning"></i>
                                                <span class="mail-contnet">
                                                    <h5 class="message-title">Productos Stock Medio</h5> 
                                                    <span><?php echo $p[0]['pmedio'] ?></span> 
                                                </span>
                                            </a>
                                            <a href="javascript:void(0)" class="message-item">
                                                <i class="mdi mdi-cube fa-2x text-danger"></i>
                                                <span class="mail-contnet">
                                                    <h5 class="message-title">Productos Stock Minimo</h5> 
                                                    <span><?php echo $p[0]['pminimo'] ?></span> 
                                                </span>
                                            </a>

                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="message-item">
                                                <i class="mdi mdi-calendar fa-2x text-success"></i>
                                                <span class="mail-contnet">
                                                    <h5 class="message-title">Productos Exp. Óptimo</h5> 
                                                    <span class="time"><?php echo $p[0]['foptimo'] ?></span> 
                                                </span>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="message-item">
                                                <i class="mdi mdi-calendar fa-2x text-warning"></i>
                                                <span class="mail-contnet">
                                                    <h5 class="message-title">Productos Exp. Medio</h5> 
                                                    <span class="time"><?php echo $p[0]['fmedio'] ?></span> 
                                                </span>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="message-item">
                                                <i class="mdi mdi-calendar fa-2x text-danger"></i>
                                                <span class="mail-contnet">
                                                    <h5 class="message-title">Productos Exp. Minimo</h5> 
                                                    <span class="time"><?php echo $p[0]['fminimo'] ?></span> 
                                                </span>
                                            </a>

                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End Iconos de Productos -->
                        <!-- ============================================================== -->


                        <!-- ============================================================== -->
                        <!-- Iconos de Creditos -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-credit-card font-24 text-info"></i>
                                <div class="notify">
                        <span class="<?php if($p[0]['creditoscomprasvencidos']==0 && $p[0]['creditosventasvencidos']==0) { } else { ?>heartbit<?php } ?>"></span>
                        <span class="<?php if($p[0]['creditoscomprasvencidos']==0 && $p[0]['creditosventasvencidos']==0) { } else { ?>point<?php } ?>"></span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-left mailbox animated bounceInDown">
                                <span class="with-arrow"><span class="bg-primary"></span></span>
                                <ul class="list-style-none">
                                    <li>
                                        <div class="drop-title border-bottom">Créditos Vencidos</div>
                                    </li>
                                    <li>
                                        <div class="message-center notifications">
                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="message-item">
                                                <i class="mdi mdi-cart fa-2x text-info"></i>
                                                <span class="mail-contnet">
                                                    <h5 class="message-title">Créditos en Compras</h5> 
                                                    <span class="time"><?php echo $p[0]['creditoscomprasvencidos'] ?></span> 
                                                </span>
                                            </a>
                                            <!-- Message -->
                                            <a href="javascript:void(0)" class="message-item">
                                                <i class="mdi mdi-cart-plus fa-2x text-success"></i>
                                                <span class="mail-contnet">
                                                    <h5 class="message-title">Créditos en Ventas</h5> 
                                                    <span class="mail-desc"><?php echo $p[0]['creditosventasvencidos'] ?></span>
                                                </span>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End Iconos de Creditos -->
                        <!-- ============================================================== -->

                        <!-- Reloj start-->
                        <li class="nav-item dropdown mega-dropdown d-none d-lg-block">
                        <a class="nav-link dropdown-toggle waves-effect waves-dark hour text-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="hidden-xs"><span id="spanreloj"></span></span>
                        </a>
                        </li>
                        <!-- Reloj end -->

                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-right">
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                        <li class="nav-item search-box"> 
                            <form class="app-search d-none d-lg-block order-lg-2">
                                <input type="text" class="form-control" placeholder="Búsqueda...">
                                <a href="" class="active"><i class="fa fa-search"></i></a>
                            </form>
                        </li>

                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="dropdown-toggle waves-effect waves-dark pro-pic d-flex mt-2 pr-0 leading-none simple" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <?php if (isset($_SESSION['dni'])) {
                                    if (file_exists("fotos/".$_SESSION['dni'].".jpg")){
                                    echo "<img src='fotos/".$_SESSION['dni'].".jpg?' width='40' height='40' class='rounded-circle'>"; 
                                    } else {
                                    echo "<img src='fotos/avatar.jpg' width='40' height='40' class='rounded-circle'>"; 
                                    } } else {
                                    echo "<img src='fotos/avatar.jpg' width='40' height='40' class='rounded-circle'>"; 
                                    }
                                    ?>

                                    <span class="ml-2 d-lg-block">
                                        <h5 class="text-dark mb-0"><?php echo $_SESSION['nombres']; ?></h5>
                                        <!--<h5 class="mb-0"><?php echo $_SESSION['nombres']; ?></h5>-->
                                        <small class="text-info mb-0"><?php echo estado($_SESSION['acceso']); ?></small>
                                    </span>
                           
                         </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <div class="d-flex no-block align-items-center p-3 mb-2 border-bottom">
                                    <div class=""><?php if (isset($_SESSION['dni'])) {
                                    if (file_exists("fotos/".$_SESSION['dni'].".jpg")){
                                    echo "<img src='fotos/".$_SESSION['dni'].".jpg?' class='rounded-circle' width='80'>"; 
                                    } else {
                                    echo "<img src='fotos/avatar.jpg' class='rounded-circle' width='80'>"; 
                                    } } else {
                                    echo "<img src='fotos/avatar.jpg' class='rounded-circle' width='80'>"; 
                                    }
                                ?></div>
                                     <div class="ml-2">
                                        <h5 class="mb-0"><abbr title="Nombres y Apellidos"><?php echo $_SESSION['nombres']; ?></abbr></h5>
                                        <p class="mb-0 text-muted"><abbr title="Correo Electrónico"><?php echo $_SESSION['email']; ?></abbr></p>
                                        <p class="mb-0 text-muted"><abbr title="Nº de Teléfono"><?php echo $_SESSION['telefono']; ?></abbr></p>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="perfil"><i class="fa fa-user"></i> Ver Perfil</a>
                                <a class="dropdown-item" href="password"><i class="fa fa-edit"></i> Actualizar Password</a>
                                <a class="dropdown-item" href="bloqueo"><i class="fa fa-clock-o"></i> Bloquear Sesión</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout"><i class="fa fa-power-off"></i> Cerrar Sesión</a>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->



<?php 
switch($_SESSION['acceso'])  {

case 'administradorG':  ?>

<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">MENU</span></li>

                <li class="sidebar-item waves-effect"><a href="panel" class="sidebar-link"><i class="mdi mdi-home"></i><span class="hide-menu"> Dashboard</span></a></li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Administración</span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Configuración</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="configuracion" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Perfil General</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="provincias" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Provincias</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="departamentos" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Departamentos</a></li>
                                
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="documentos" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Docum. Tributarios</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="monedas" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Tipos de Moneda</a></li>
                                
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="cambios" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Tipos de Cambio</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="medios" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Medios de Pagos</a></li>
                                
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="impuestos" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Impuestos</a></li>

                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="sucursales" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Sucursales</a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Productos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="familias" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Familias</span></a></li>

                                <li class="sidebar-item"><a href="subfamilias" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Sub-Familias</span></a></li>

                                <li class="sidebar-item"><a href="marcas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Marcas</span></a></li>

                                <li class="sidebar-item"><a href="modelos" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Modelos</span></a></li>

                                <li class="sidebar-item"><a href="presentaciones" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Presentaciones</span></a></li>

                                <li class="sidebar-item"><a href="colores" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Colores</span></a></li>

                                <li class="sidebar-item"><a href="origenes" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Origenes</span></a></li>

                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Usuarios</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="usuarios" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Usuarios</span></a></li>

                                <li class="sidebar-item"><a href="logs" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Historial de Acceso</span></a></li>

                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Base de Datos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="backup" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Backup</span></a></li>

                                <li class="sidebar-item"><a href="restore" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Restore</span></a></li>

                            </ul>
                        </li>

                    </ul>
                </li>


                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-folder-multiple"></i><span class="hide-menu">Mantenimiento</span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="clientes" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Clientes</a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Proveedores</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="proveedores" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Proveedores</span></a></li>

                                <li class="sidebar-item"><a href="forpedido" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Nuevo Pedido</span></a></li>

                                <li class="sidebar-item"><a href="pedidos" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Consulta Pedidos</span></a></li>

                                <li class="sidebar-item"><a href="busquedapedidos" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Reporte Pedidos</span></a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Almacen</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="producto_express.php" class="sidebar-link"><i class="mdi mdi-rocket"></i><span class="hide-menu"> Nuevo Producto Express</span></a></li>
                                <li class="sidebar-item"><a href="forproducto" class="sidebar-link"><i class="mdi mdi-settings" style="font-size: 14px;"></i><span class="hide-menu"> <small>Detallado (Legacy)</small></span></a></li>

                                <li class="sidebar-item"><a href="catalogo_express.php" class="sidebar-link"><i class="mdi mdi-view-grid"></i><span class="hide-menu"> Catálogo Visual</span></a></li>

                                <li class="sidebar-item"><a href="productosvendidos" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Productos Vendidos </span></a></li>

                                <li class="sidebar-item"><a href="productosxvendedor" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Productos x Vendedor</span></a></li>  

                                <li class="sidebar-item"><a href="productosxmoneda" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Productos por Moneda </span></a></li>                                     
                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Kardex Productos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="buscakardex" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Kardex de Productos</span></a></li>

                                <li class="sidebar-item"><a href="kardexvalorizado" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Kardex Valorizado</span></a></li>

                                <li class="sidebar-item"><a href="kardexfechas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Valorizado por Fechas</span></a></li>

                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Traspasos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="traspasos" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Traspasos </span></a></li>

                                <li class="sidebar-item"><a href="traspasosxsucursal" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Traspasos x Sucursal</span></a></li>

                                <li class="sidebar-item"><a href="traspasosxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Traspasos x Fechas</span></a></li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cart"></i><span class="hide-menu">Compras </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="compras" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Compras </span></a></li>

                        <li class="sidebar-item"><a href="cuentasxpagar" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Cuentas por Pagar </span></a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Compras</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="comprasxproveedor" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Compras x Proveedor</span></a></li>

                                <li class="sidebar-item"><a href="comprasxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Compras x Fechas</span></a></li>

                                <li class="sidebar-item"><a href="creditosxproveedor" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Proveedor </span></a></li>

                                <li class="sidebar-item"><a href="creditoscomprasxfechas" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Fechas </span></a></li>                                     
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-calculator"></i><span class="hide-menu">Cotizaciones </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="cotizaciones" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Cotización </span></a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Cotización</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="cotizacionesxfechas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Cotización x Fechas</span></a></li>

                                <li class="sidebar-item"><a href="productoscotizados" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Productos Cotizados</span></a></li>

                                <li class="sidebar-item"><a href="cotizacionesxvendedor" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Cotizaciones x Vendedor</span></a></li>                                     
                            </ul>
                        </li>

                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cash-multiple"></i><span class="hide-menu">Cajas / Turnos</span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <!-- CAJA EXPRESS DESTACADA -->
                        <li class="sidebar-item" style="background-color: #4a4a4a; margin-bottom: 5px; border-radius: 4px;">
                            <a href="caja_express.php" class="sidebar-link" style="color: #ffffff !important; font-weight: bold; opacity: 1;">
                                <i class="mdi mdi-rocket" style="color: #ffffff !important;"></i>
                                <span class="hide-menu"> 🚀 CAJA EXPRESS</span>
                            </a>
                        </li>

                        <li class="sidebar-item" style="pointer-events: none; padding-left: 20px; font-size: 10px; color: #a1aab2; text-transform: uppercase; font-weight: bold; margin-top: 10px;">HISTORIAL Y CONSULTAS</li>

                        <li class="sidebar-item"><a href="arqueos" class="sidebar-link"><i class="mdi mdi-file-document"></i><span class="hide-menu"> Historial de Arqueos </span></a></li>
                        <li class="sidebar-item"><a href="arqueosxfechas" class="sidebar-link"><i class="mdi mdi-chart-bar"></i><span class="hide-menu"> Reportes Detallados </span></a></li>

                        <li class="sidebar-item" style="pointer-events: none; padding-left: 20px; font-size: 10px; color: #a1aab2; text-transform: uppercase; font-weight: bold; margin-top: 10px;">CONFIGURACIÓN</li>

                        <li class="sidebar-item"><a href="cajas" class="sidebar-link"><i class="mdi mdi-settings"></i><span class="hide-menu"> Configurar Cajas </span></a></li>
                    </ul>
                </li>


                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cart-plus"></i><span class="hide-menu">Ventas </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="ventas" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Ventas </span></a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Ventas</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="ventasxcajas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Ventas x Cajas</span></a></li>

                                <li class="sidebar-item"><a href="ventasxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Ventas x Fechas</span></a></li>                                    
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-credit-card"></i><span class="hide-menu">Créditos </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="creditos" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Consulta Créditos </span></a></li>

                        <li class="sidebar-item"><a href="creditosxclientes" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Clientes </span></a></li>

                        <li class="sidebar-item"><a href="creditosxfechas" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Fechas </span></a></li>

                        <li class="sidebar-item"><a href="creditosxdetalles" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Detalles </span></a></li>
                      
                    </ul>
                </li>

            <li class="sidebar-item waves-effect"><a href="logout" class="sidebar-link"><i class="mdi mdi-power"></i><span class="hide-menu"> Cerrar Sesión</span></a></li>

        </ul>
    </nav>
    <!-- End Sidebar navigation -->
</div>
<!-- End Sidebar scroll-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->

            
<?php
break;
case 'administradorS': ?>

<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">MENU</span></li>

                <li class="sidebar-item waves-effect"><a href="panel" class="sidebar-link"><i class="mdi mdi-home"></i><span class="hide-menu"> Dashboard</span></a></li>

                <li class="sidebar-item waves-effect"><a href="vender.php" target="_blank" class="sidebar-link"><i class="mdi mdi-desktop-mac"></i><span class="hide-menu"> POS</span></a></li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Administración</span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Configuración</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="provincias" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Provincias</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="departamentos" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Departamentos</a></li>
                                
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="documentos" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Docum. Tributarios</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="monedas" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Tipos de Moneda</a></li>
                                
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="cambios" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Tipos de Cambio</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="medios" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Medios de Pagos</a></li>
                                
                            </ul>
                        </li>


                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Productos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="familias" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Familias</span></a></li>

                                <li class="sidebar-item"><a href="subfamilias" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Sub-Familias</span></a></li>

                                <li class="sidebar-item"><a href="marcas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Marcas</span></a></li>

                                <li class="sidebar-item"><a href="modelos" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Modelos</span></a></li>

                                <li class="sidebar-item"><a href="presentaciones" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Presentaciones</span></a></li>

                                <li class="sidebar-item"><a href="colores" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Colores</span></a></li>

                                <li class="sidebar-item"><a href="origenes" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Origenes</span></a></li>

                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Usuarios</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="usuarios" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Usuarios</span></a></li>

                                <li class="sidebar-item"><a href="logs" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Historial de Acceso</span></a></li>

                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Base de Datos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="backup" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Backup</span></a></li>

                                <li class="sidebar-item"><a href="restore" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Restore</span></a></li>

                            </ul>
                        </li>

                    </ul>
                </li>


                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-folder-multiple"></i><span class="hide-menu">Mantenimiento</span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="clientes" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Clientes</a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Proveedores</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="proveedores" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Proveedores</span></a></li>

                                <li class="sidebar-item"><a href="forpedido" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Nuevo Pedido</span></a></li>

                                <li class="sidebar-item"><a href="pedidos" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Consulta Pedidos</span></a></li>

                                <li class="sidebar-item"><a href="busquedapedidos" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Reporte Pedidos</span></a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Almacen</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="producto_express.php" class="sidebar-link"><i class="mdi mdi-rocket"></i><span class="hide-menu"> Nuevo Producto Express</span></a></li>
                                <li class="sidebar-item"><a href="forproducto" class="sidebar-link"><i class="mdi mdi-settings" style="font-size: 14px;"></i><span class="hide-menu"> <small>Detallado (Legacy)</small></span></a></li>

                                <li class="sidebar-item"><a href="catalogo_express.php" class="sidebar-link"><i class="mdi mdi-view-grid"></i><span class="hide-menu"> Catálogo Visual</span></a></li>

                                <li class="sidebar-item"><a href="productosvendidos" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Productos Vendidos </span></a></li>

                                <li class="sidebar-item"><a href="productosxvendedor" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Productos x Vendedor</span></a></li> 

                                <li class="sidebar-item"><a href="productosxmoneda" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Productos por Moneda </span></a></li>

                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Kardex Productos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="buscakardex" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Kardex de Productos</span></a></li>

                                <li class="sidebar-item"><a href="kardexvalorizado" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Kardex Valorizado</span></a></li>

                                <li class="sidebar-item"><a href="kardexfechas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Valorizado por Fechas</span></a></li>

                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Traspasos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="fortraspaso" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nuevo Traspaso </span></a></li>

                                <li class="sidebar-item"><a href="traspasos" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Traspasos </span></a></li>

                                <li class="sidebar-item"><a href="traspasosxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Traspasos x Fechas</span></a></li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cart"></i><span class="hide-menu">Compras </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="forcompra" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nueva Compra </span></a></li>

                        <li class="sidebar-item"><a href="compras" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Compras </span></a></li>

                        <li class="sidebar-item"><a href="cuentasxpagar" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Cuentas por Pagar </span></a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Compras</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="comprasxproveedor" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Compras x Proveedor</span></a></li>

                                <li class="sidebar-item"><a href="comprasxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Compras x Fechas</span></a></li>

                                <li class="sidebar-item"><a href="creditosxproveedor" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Proveedor </span></a></li>

                                <li class="sidebar-item"><a href="creditoscomprasxfechas" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Fechas </span></a></li>                                     
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-calculator"></i><span class="hide-menu">Cotizaciones </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="forcotizacion" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nueva Cotización </span></a></li>

                        <li class="sidebar-item"><a href="cotizaciones" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Cotización </span></a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Cotización</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="cotizacionesxfechas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Cotización x Fechas</span></a></li>

                                <li class="sidebar-item"><a href="productoscotizados" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Productos Cotizados</span></a></li>

                                <li class="sidebar-item"><a href="cotizacionesxvendedor" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Cotizaciones x Vendedor</span></a></li>                                     
                            </ul>
                        </li>

                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cash-multiple"></i><span class="hide-menu">Cajas / Turnos</span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <!-- CAJA EXPRESS DESTACADA -->
                        <li class="sidebar-item" style="background-color: #4a4a4a; margin-bottom: 5px; border-radius: 4px;">
                            <a href="caja_express.php" class="sidebar-link" style="color: #ffffff !important; font-weight: bold; opacity: 1;">
                                <i class="mdi mdi-rocket" style="color: #ffffff !important;"></i>
                                <span class="hide-menu"> 🚀 CAJA EXPRESS</span>
                            </a>
                        </li>

                        <li class="sidebar-item" style="pointer-events: none; padding-left: 20px; font-size: 10px; color: #a1aab2; text-transform: uppercase; font-weight: bold; margin-top: 10px;">HISTORIAL Y CONSULTAS</li>

                        <li class="sidebar-item"><a href="arqueos" class="sidebar-link"><i class="mdi mdi-file-document"></i><span class="hide-menu"> Historial de Arqueos </span></a></li>
                        <li class="sidebar-item"><a href="arqueosxfechas" class="sidebar-link"><i class="mdi mdi-chart-bar"></i><span class="hide-menu"> Reportes Detallados </span></a></li>

                        <li class="sidebar-item" style="pointer-events: none; padding-left: 20px; font-size: 10px; color: #a1aab2; text-transform: uppercase; font-weight: bold; margin-top: 10px;">CONFIGURACIÓN</li>

                        <li class="sidebar-item"><a href="cajas" class="sidebar-link"><i class="mdi mdi-settings"></i><span class="hide-menu"> Configurar Cajas </span></a></li>
                    </ul>
                </li>


                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cart-plus"></i><span class="hide-menu">Ventas </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="forventa" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nueva Venta </span></a></li>

                        <li class="sidebar-item"><a href="ventas" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Ventas </span></a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Ventas</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="ventasxcajas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Ventas x Cajas</span></a></li>

                                <li class="sidebar-item"><a href="ventasxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Ventas x Fechas</span></a></li>                                    
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-credit-card"></i><span class="hide-menu">Créditos </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="creditos" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nuevo Pago </span></a></li>

                        <li class="sidebar-item"><a href="creditosxclientes" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Clientes </span></a></li>

                        <li class="sidebar-item"><a href="creditosxfechas" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Fechas </span></a></li>

                        <li class="sidebar-item"><a href="creditosxdetalles" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Detalles </span></a></li>

                    </ul>
                </li>

        <li class="sidebar-item waves-effect"><a href="logout" class="sidebar-link"><i class="mdi mdi-power"></i><span class="hide-menu"> Cerrar Sesión</span></a></li>

        </ul>
    </nav>
    <!-- End Sidebar navigation -->
</div>
<!-- End Sidebar scroll-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->


<?php
break;
case 'secretaria': ?>

<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">MENU</span></li>

                <li class="sidebar-item waves-effect"><a href="panel" class="sidebar-link"><i class="mdi mdi-home"></i><span class="hide-menu"> Dashboard</span></a></li>

                <li class="sidebar-item waves-effect"><a href="pos" class="sidebar-link"><i class="mdi mdi-desktop-mac"></i><span class="hide-menu"> POS</span></a></li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu">Administración</span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Configuración</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="provincias" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Provincias</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="departamentos" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Departamentos</a></li>
                                
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="documentos" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Docum. Tributarios</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="monedas" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Tipos de Moneda</a></li>
                                
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="cambios" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Tipos de Cambio</a></li>

                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="medios" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Medios de Pagos</a></li>
                                
                            </ul>
                        </li>


                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Productos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="familias" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Familias</span></a></li>

                                <li class="sidebar-item"><a href="subfamilias" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Sub-Familias</span></a></li>

                                <li class="sidebar-item"><a href="marcas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Marcas</span></a></li>

                                <li class="sidebar-item"><a href="modelos" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Modelos</span></a></li>

                                <li class="sidebar-item"><a href="presentaciones" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Presentaciones</span></a></li>

                                <li class="sidebar-item"><a href="colores" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Colores</span></a></li>

                                <li class="sidebar-item"><a href="origenes" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Origenes</span></a></li>

                            </ul>
                        </li>

                    </ul>
                </li>


                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-folder-multiple"></i><span class="hide-menu">Mantenimiento</span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="clientes" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Clientes</a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Proveedores</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="proveedores" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Proveedores</span></a></li>

                                <li class="sidebar-item"><a href="forpedido" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Nuevo Pedido</span></a></li>

                                <li class="sidebar-item"><a href="pedidos" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Consulta Pedidos</span></a></li>

                                <li class="sidebar-item"><a href="busquedapedidos" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Reporte Pedidos</span></a></li>
                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Almacen</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="producto_express.php" class="sidebar-link"><i class="mdi mdi-rocket"></i><span class="hide-menu"> Nuevo Producto Express</span></a></li>
                                <li class="sidebar-item"><a href="forproducto" class="sidebar-link"><i class="mdi mdi-settings" style="font-size: 14px;"></i><span class="hide-menu"> <small>Detallado (Legacy)</small></span></a></li>

                                <li class="sidebar-item"><a href="catalogo_express.php" class="sidebar-link"><i class="mdi mdi-view-grid"></i><span class="hide-menu"> Catálogo Visual</span></a></li>

                                <li class="sidebar-item"><a href="productosvendidos" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Productos Vendidos </span></a></li> 

                                <li class="sidebar-item"><a href="productosxvendedor" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Productos x Vendedor</span></a></li>

                                <li class="sidebar-item"><a href="productosxmoneda" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Productos por Moneda </span></a></li>                                        
                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Kardex Productos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="buscakardex" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Kardex de Productos</span></a></li>

                                <li class="sidebar-item"><a href="kardexvalorizado" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Kardex Valorizado</span></a></li>

                                <li class="sidebar-item"><a href="kardexfechas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Valorizado por Fechas</span></a></li>

                            </ul>
                        </li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu">Traspasos</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="fortraspaso" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nuevo Traspaso </span></a></li>

                                <li class="sidebar-item"><a href="traspasos" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Traspasos </span></a></li>

                                <li class="sidebar-item"><a href="traspasosxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Traspasos x Fechas</span></a></li>
                            </ul>
                        </li>

                    </ul>
                </li>


                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cart"></i><span class="hide-menu">Compras </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="forcompra" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nueva Compra </span></a></li>

                        <li class="sidebar-item"><a href="compras" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Compras </span></a></li>

                        <li class="sidebar-item"><a href="cuentasxpagar" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Cuentas por Pagar </span></a></li>

                       <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Compras</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="comprasxproveedor" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Compras x Proveedor</span></a></li>

                                <li class="sidebar-item"><a href="comprasxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Compras x Fechas</span></a></li>

                                <li class="sidebar-item"><a href="creditosxproveedor" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Proveedor </span></a></li>

                                <li class="sidebar-item"><a href="creditoscomprasxfechas" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Fechas </span></a></li>                                     
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-calculator"></i><span class="hide-menu">Cotizaciones </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="forcotizacion" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nueva Cotización </span></a></li>

                        <li class="sidebar-item"><a href="cotizaciones" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Cotización </span></a></li>

                        <li class="sidebar-item"><a href="cotizacionesxfechas" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Reporte Cotización </span></a></li>

                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-monitor-multiple"></i><span class="hide-menu">Cajas </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="cajas" aria-expanded="false"><i class="mdi mdi-clipboard-text"></i>Asignación de Cajas</a></li>

                         <li class="sidebar-item"><a href="arqueos" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Arqueos de Caja </span></a></li>

                        <li class="sidebar-item"><a href="movimientos" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Movimientos en Caja </span></a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Cajas</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="arqueosxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Arqueos x Fechas</span></a></li> 

                                <li class="sidebar-item"><a href="movimientosxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Movimientos x Fechas</span></a></li> 
                                    
                            </ul>
                        </li>
                    </ul>
                </li>


                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cart-plus"></i><span class="hide-menu">Ventas </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="forventa" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nueva Venta </span></a></li>

                        <li class="sidebar-item"><a href="ventas" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Ventas </span></a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Ventas</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="ventasxcajas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Ventas x Cajas</span></a></li>

                                <li class="sidebar-item"><a href="ventasxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Ventas x Fechas</span></a></li>                                    
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-credit-card"></i><span class="hide-menu">Créditos </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="creditos" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nuevo Pago </span></a></li>

                        <li class="sidebar-item"><a href="creditosxclientes" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Clientes </span></a></li>

                        <li class="sidebar-item"><a href="creditosxfechas" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Fechas </span></a></li>

                        <li class="sidebar-item"><a href="creditosxdetalles" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Detalles </span></a></li>

                    </ul>
                </li>

        <li class="sidebar-item waves-effect"><a href="logout" class="sidebar-link"><i class="mdi mdi-power"></i><span class="hide-menu"> Cerrar Sesión</span></a></li>

        </ul>
    </nav>
    <!-- End Sidebar navigation -->
</div>
<!-- End Sidebar scroll-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->


<?php
break;
case 'cajero': ?>

<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">MENU</span></li>

                <li class="sidebar-item waves-effect"><a href="panel" class="sidebar-link"><i class="mdi mdi-home"></i><span class="hide-menu"> Dashboard</span></a></li>

                <li class="sidebar-item waves-effect"><a href="pos" class="sidebar-link"><i class="mdi mdi-desktop-mac"></i><span class="hide-menu"> POS</span></a></li>

                <li class="sidebar-item waves-effect"><a href="clientes" class="sidebar-link"><i class="mdi mdi-account-multiple"></i><span class="hide-menu"> Clientes</span></a></li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cube"></i><span class="hide-menu">Productos </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="productos" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Consulta Productos</span></a></li>

                        <li class="sidebar-item"><a href="buscakardex" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Kardex de Productos</span></a></li>

                        <li class="sidebar-item"><a href="productosvendidos" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Productos Vendidos </span></a></li>

                        <li class="sidebar-item"><a href="productosxmoneda" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Productos por Moneda </span></a></li>

                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-calculator"></i><span class="hide-menu">Cotizaciones </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="forcotizacion" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nueva Cotización </span></a></li>

                        <li class="sidebar-item"><a href="cotizaciones" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Cotización </span></a></li>

                        <li class="sidebar-item"><a href="cotizacionesxfechas" class="sidebar-link"><i class="mdi mdi-cart-plus"></i><span class="hide-menu"> Reporte Cotización </span></a></li>

                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cash-multiple"></i><span class="hide-menu">Cajas / Turnos</span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <!-- CAJA EXPRESS DESTACADA -->
                        <li class="sidebar-item" style="background-color: #4a4a4a; margin-bottom: 5px; border-radius: 4px;">
                            <a href="caja_express.php" class="sidebar-link" style="color: #ffffff !important; font-weight: bold; opacity: 1;">
                                <i class="mdi mdi-rocket" style="color: #ffffff !important;"></i>
                                <span class="hide-menu"> 🚀 CAJA EXPRESS</span>
                            </a>
                        </li>

                        <li class="sidebar-item" style="pointer-events: none; padding-left: 20px; font-size: 10px; color: #a1aab2; text-transform: uppercase; font-weight: bold; margin-top: 10px;">HISTORIAL Y CONSULTAS</li>

                        <li class="sidebar-item"><a href="arqueos" class="sidebar-link"><i class="mdi mdi-file-document"></i><span class="hide-menu"> Historial de Arqueos </span></a></li>
                        <li class="sidebar-item"><a href="arqueosxfechas" class="sidebar-link"><i class="mdi mdi-chart-bar"></i><span class="hide-menu"> Reportes Detallados </span></a></li>
                    </ul>
                </li>


                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-cart-plus"></i><span class="hide-menu">Ventas </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="forventa" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nueva Venta </span></a></li>

                        <li class="sidebar-item"><a href="ventas" class="sidebar-link"><i class="mdi mdi-cart"></i><span class="hide-menu"> Consulta Ventas </span></a></li>

                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span class="hide-menu">Reporte Ventas</span></a>
                            <ul aria-expanded="false" class="collapse second-level">

                                <li class="sidebar-item"><a href="ventasxcajas" class="sidebar-link"><i class="mdi mdi-priority-low"></i><span class="hide-menu"> Ventas x Cajas</span></a></li>

                                <li class="sidebar-item"><a href="ventasxfechas" class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span class="hide-menu"> Ventas x Fechas</span></a></li>                                    
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-credit-card"></i><span class="hide-menu">Créditos </span></a>
                    <ul aria-expanded="false" class="collapse first-level">

                        <li class="sidebar-item"><a href="creditos" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Nuevo Pago </span></a></li>

                        <li class="sidebar-item"><a href="creditosxclientes" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Clientes </span></a></li>

                        <li class="sidebar-item"><a href="creditosxfechas" class="sidebar-link"><i class="mdi mdi-cards-variant"></i><span class="hide-menu"> Créditos x Fechas </span></a></li>

                    </ul>
                </li>

        <li class="sidebar-item waves-effect"><a href="logout" class="sidebar-link"><i class="mdi mdi-power"></i><span class="hide-menu"> Cerrar Sesión</span></a></li>

        </ul>
    </nav>
    <!-- End Sidebar navigation torbiscomarco@gmail.com -->
</div>
<!-- End Sidebar scroll-->
</aside>
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->



<?php
break; } ?>



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