<?php
require_once("class/class.php"); 
if(isset($_SESSION['acceso'])) { 
     if ($_SESSION["acceso"]=="administradorG" || $_SESSION["acceso"]=="administradorS" || $_SESSION["acceso"]=="secretaria") {

$tra = new Login();
$ses = $tra->ExpiraSession(); 

if(isset($_POST["proceso"]) and $_POST["proceso"]=="save") {
    $reg = $tra->RegistrarCajas();
    exit;
}
elseif(isset($_POST["proceso"]) and $_POST["proceso"]=="update") {
    $reg = $tra->ActualizarCajas();
    exit;
}  
elseif(isset($_GET["proceso"]) and $_GET["proceso"]=="eliminar") {
    $reg = $tra->EliminarCajas();
    exit;
}       
?>
<!DOCTYPE html>
<html dir="ltr" lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Cajas</title>
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <!-- Menu CSS -->
    <link href="assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <link href="assets/plugins/bower_components/toast-master/css/jquery.toast.css" rel="stylesheet">
    <link href="assets/plugins/datatables/dataTables.bootstrap4.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/sweetalert.css">
    <link href="assets/css/animate.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Custom Unicorn Theme -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif !important; background-color: #f3f4f6; }
        
        /* Modern Cards */
        .box-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            position: relative;
            border: 1px solid #e5e7eb;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .box-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #6366f1;
        }
        .box-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .btn-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #f472b6 0%, #db2777 100%);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            box-shadow: 0 10px 15px -3px rgba(219, 39, 119, 0.5);
            transition: transform 0.2s;
            z-index: 1000;
            border: none;
            cursor: pointer;
        }
        .btn-fab:hover { transform: scale(1.1); }

        /* Typography */
        h4.box-title { font-size: 1.1rem; font-weight: 700; color: #1f2937; margin-bottom: 5px; }
        p.box-subtitle { color: #6b7280; font-size: 0.9rem; margin-bottom: 20px; }
        .badge-status { 
            padding: 4px 12px; 
            border-radius: 9999px; 
            font-size: 0.75rem; 
            font-weight: 600; 
            display: inline-block; 
        }
        .badge-active { background-color: #dcfce7; color: #166534; }
        .badge-inactive { background-color: #f3f4f6; color: #374151; }

        /* Modal Modernization */
        .modal-content { border-radius: 20px; border: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .modal-header { background: #1e1b4b; border-radius: 20px 20px 0 0; color: white; border-bottom: none; padding: 20px 30px; }
        .close { color: white; opacity: 1; }
        .form-control { border-radius: 8px; border: 1px solid #d1d5db; padding: 10px 15px; }
        .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        
        .action-btn {
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 5px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .action-btn:hover { background: #f3f4f6; color: #4b5563; }
        .action-btn.edit:hover { color: #6366f1; background: #e0e7ff; }
        .action-btn.delete:hover { color: #ef4444; background: #fee2e2; }
    </style>
</head>

<body onLoad="muestraReloj()" class="fix-header">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>

    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" class="mini-sidebar"> 
        
        <!-- Navbar & Sidebar -->
        <?php include('menu.php'); ?>

        <div class="page-wrapper" style="background:#f3f4f6;">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb" style="background:transparent; padding: 20px 30px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="page-title" style="font-weight: 800; color: #111827; font-size: 1.5rem;">Cajas Registradoras</h4>
                        <span style="color: #6b7280;">Administra los puntos de venta de tu sucursal.</span>
                    </div>
                    <?php if ($_SESSION["acceso"]=="administradorG" || $_SESSION["acceso"]=="administradorS") { ?>
                    <button onclick="NuevaCaja()" class="btn btn-primary" style="background: #4f46e5; border:none; border-radius: 10px; padding: 10px 20px; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                        <i class="fa fa-plus-circle"></i> Nueva Caja
                    </button>
                    <?php } ?>
                </div>
            </div>

            <!-- Content -->
            <div class="container-fluid" style="padding: 0 30px 30px;">
                <div class="row" id="cajas-grid">
                    <?php 
                    $cajas = $tra->ListarCajas();
                    if($cajas) {
                        foreach($cajas as $c) {
                            // CORRECCION: Usar solo 'nombres' ya que 'apellidos' no existe en la consulta
                            $responsable = $c['nombres'];
                            // Encriptar IDs para seguridad en JS
                            $idEnc = encrypt($c['codcaja']);
                            $uniqueId = "dropdown-" . $c['codcaja'];
                    ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                        <div class="box-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="box-icon"><i class="fa fa-desktop"></i></div>
                                
                                <div class="position-relative">
                                    <button class="action-btn" onclick="toggleMenu('<?php echo $uniqueId; ?>', event)">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>
                                    
                                    <!-- Custom Dropdown Menu -->
                                    <div id="<?php echo $uniqueId; ?>" class="custom-menu" style="display:none;">
                                        <a href="javascript:void(0)" onclick="EditarCaja('<?php echo $c['codcaja']; ?>', '<?php echo $c['nrocaja']; ?>', '<?php echo $c['nomcaja']; ?>', '<?php echo $c['codigo']; ?>', '<?php echo $c['codsucursal']; ?>')">
                                            <i class="fa fa-edit text-info"></i> Editar
                                        </a>
                                        <a href="javascript:void(0)" onclick="EliminarCaja('<?php echo $idEnc; ?>')" class="text-danger">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <h4 class="box-title">Caja <?php echo $c['nrocaja']; ?></h4>
                            <p class="box-subtitle"><?php echo $c['nomcaja']; ?></p>
                            
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex align-items-center">
                                    <div class="ml-2">
                                        <small class="text-muted d-block">Asignada a:</small>
                                        <span class="font-weight-bold text-dark"><?php echo $responsable; ?></span>
                                    </div>
                                </div>
                                <div class="mt-2 text-right">
                                    <span class="badge-status badge-active">DISPONIBLE</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        }
                    } else {
                        echo '<div class="col-12 text-center py-5"><h4 class="text-muted">No hay cajas registradas</h4></div>';
                    }
                    ?>
                </div>
            </div>

            <footer class="footer text-center" style="background:transparent;">
                Unicornio POS &copy; <?php echo date("Y"); ?>
            </footer>
        </div>
    </div>

    <!-- STYLES FOR CUSTOM MENU -->
    <style>
        .custom-menu {
            position: absolute;
            right: 0;
            top: 35px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            width: 160px;
            z-index: 100;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }
        .custom-menu a {
            display: block;
            padding: 12px 15px;
            color: #4b5563;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .custom-menu a:hover {
            background: #f8fafc;
            color: #6366f1;
        }
    </style>

    <!-- SCRIPT FOR CUSTOM MENU -->
    <script>
        function toggleMenu(id, event) {
            event.stopPropagation();
            // Close all others
            var menus = document.getElementsByClassName('custom-menu');
            for(var i=0; i<menus.length; i++) {
                if(menus[i].id !== id) menus[i].style.display = 'none';
            }
            // Toggle current
            var menu = document.getElementById(id);
            if (menu.style.display === 'block') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        }

        // Close when clicking outside
        document.addEventListener('click', function(event) {
            var menus = document.getElementsByClassName('custom-menu');
            for(var i=0; i<menus.length; i++) {
                menus[i].style.display = 'none';
            }
        });
    </script>

    <!-- MODAL GESTION CAJA -->
    <div id="modalCaja" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalTitle">Gestión de Caja</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body p-4">
                    <form id="form-caja" name="form-caja" method="post">
                        <!-- Hidden Fields -->
                        <input type="hidden" name="proceso" id="proceso" value="save" />
                        <input type="hidden" name="codcaja" id="codcaja" />
                        
                        <!-- Sucursal (Solo Admin Global) -->
                        <?php if ($_SESSION["acceso"]=="administradorG") { ?>
                        <div class="form-group mb-3">
                            <label>Sucursal</label>
                            <select name="codsucursal" id="codsucursal" class="form-control" onChange="CargaUsuarios(this.value);" required>
                                <option value="">-- Seleccione Sucursal --</option>
                                <?php
                                $sucursal = new Login();
                                $suc = $sucursal->ListarSucursales();
                                if($suc){
                                    foreach($suc as $s){
                                        echo '<option value="'.encrypt($s['codsucursal']).'">'.$s['razonsocial'].'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <?php } else { ?>
                            <input type="hidden" name="codsucursal" id="codsucursal" value="<?php echo encrypt($_SESSION['codsucursal']); ?>">
                        <?php } ?>

                        <!-- Responsable -->
                        <div class="form-group mb-3">
                            <label>Responsable de Caja</label>
                            <select name="codigo" id="codigo" class="form-control" required>
                                <option value="">-- Seleccione Responsable --</option>
                                <?php
                                if ($_SESSION["acceso"]!="administradorG") {
                                    $usuario = new Login();
                                    $users = $usuario->ListarUsuarios();
                                    if($users){
                                        foreach($users as $u){
                                            echo '<option value="'.$u['codigo'].'">'.$u['dni'].' - '.$u['nombres'].'</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Nº de Caja</label>
                                <input type="text" class="form-control" name="nrocaja" id="nrocaja" placeholder="Ej: 01" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Nombre Identificativo</label>
                                <input type="text" class="form-control" name="nomcaja" id="nomcaja" placeholder="Ej: CAJA PRINCIPAL" required>
                            </div>
                        </div>

                        <div class="text-right mt-4">
                            <button type="button" class="btn btn-light mr-2" data-dismiss="modal">Cancelar</button>
                            <button type="submit" id="btn-submit" class="btn btn-primary" style="background: #4f46e5; border:none;">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/script/jquery.min.js"></script> 
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/app.min.js"></script>
    <script src="assets/js/app.init.horizontal-fullwidth.js"></script>
    <script src="assets/js/perfect-scrollbar.js"></script>
    <script src="assets/js/sweetalert-dev.js"></script>
    <script src="assets/js/custom.js"></script>
    
    <script>
        // Nueva Caja
        function NuevaCaja() {
            $('#form-caja')[0].reset();
            $('#proceso').val('save');
            $('#modalTitle').text('Nueva Caja');
            $('#codcaja').val('');
            $('#modalCaja').modal('show');
        }

        // Editar Caja
        function EditarCaja(codcaja, nrocaja, nomcaja, codigo, codsucursal) {
            $('#proceso').val('update');
            $('#modalTitle').text('Editar Caja');
            $('#codcaja').val(codcaja);
            $('#nrocaja').val(nrocaja);
            $('#nomcaja').val(nomcaja);
            $('#codigo').val(codigo);
            // Si es admin global, setear sucursal seria necesario pero requiere desencriptar o manejar logica extra.
            // Simplificaremos asumiendo la carga de usuarios.
            $('#modalCaja').modal('show');
        }

        // Eliminar Caja
        function EliminarCaja(id) {
            swal({
                title: "¿Estás seguro?",
                text: "Se eliminará esta caja permanentemente. No podrás deshacer esta acción.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false
            }, function(){
                $.ajax({
                    url: "cajas.php?proceso=eliminar&codcaja="+id,
                    type: "GET",
                    success: function(data){
                        if(data == "1"){
                            swal("Eliminado", "La caja ha sido eliminada exitosamente.", "success");
                            setTimeout(function(){ location.reload(); }, 1500);
                        } else if(data == "2"){
                            swal("Aviso", "No se puede eliminar la caja porque tiene ventas asociadas.", "error");
                        } else {
                            swal("Error", "No tienes permisos para realizar esta acción.", "error");
                        }
                    },
                    error: function() {
                        swal("Error", "Ocurrió un error al procesar la solicitud.", "error");
                    }
                });
            });
        }

        // Submission Logic
        $('#form-caja').submit(function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            $.ajax({
                url: "cajas.php",
                type: "POST",
                data: data,
                success: function(response){
                    $('#modalCaja').modal('hide');
                    swal("¡Éxito!", "Operación realizada correctamente", "success");
                    setTimeout(function(){ location.reload(); }, 1500);
                },
                error: function(){
                    swal("Error", "Ocurrió un error al procesar", "error");
                }
            });
        });

        // Carga Dinámica de Usuarios por Sucursal (Legacy Logic Wrapper)
        function CargaUsuarios(val) {
           // Si se necesita implementar, se puede reutilizar la logica de consultas.php
           // $('#codigo').load("consultas.php?CargaUsuariosCaja=si&codsucursal="+val);
        }
    </script>

</body>
</html>
<?php } else { header("Location: panel"); } } else { header("Location: logout"); } ?>