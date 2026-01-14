<?php
session_start();
// Basic session check to protect the page
if (!isset($_SESSION['acceso'])) {
    header("Location: logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients V2 Dashboard</title>

    <!-- Existing project styles -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/default.css" id="theme" rel="stylesheet">

    <!-- AlpineJS via CDN -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Custom styles for badges -->
    <style>
        .badge-success { background-color: #28a745; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge {
            display: inline-block;
            padding: .35em .65em;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
        }
    </style>
</head>
<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-boxed-layout="full" data-header-position="fixed" data-sidebar-position="fixed" class="mini-sidebar">

        <!-- Include the existing menu for consistent navigation -->
        <?php include('menu.php'); ?>

        <div class="page-wrapper">
            <div class="page-breadcrumb border-bottom">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-xs-12 align-self-center">
                        <h5 class="font-medium text-uppercase mb-0"><i class="fa fa-users"></i> Clientes V2 (SaaS)</h5>
                    </div>
                </div>
            </div>

            <div class="page-content container-fluid" x-data="{
                clients: [],
                isLoading: true,
                error: '',
                fetchClients() {
                    this.isLoading = true;
                    fetch('api/clients_v2.php')
                        .then(response => {
                            if (!response.ok) throw new Error('Error de red al intentar contactar la API.');
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) throw new Error(data.error);
                            this.clients = data;
                        })
                        .catch(error => {
                            this.error = 'Error al cargar los clientes: ' + error.message;
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                }
            }" x-init="fetchClients()">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Listado de Clientes</h4>

                                <div x-show="isLoading" class="text-center">
                                    <p>Cargando datos...</p>
                                </div>

                                <div x-show="error" class="alert alert-danger" x-text="error"></div>

                                <div x-show="!isLoading && !error" class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Cod. Cliente</th>
                                                <th>Nombre</th>
                                                <th>Teléfono</th>
                                                <th>Status</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="client in clients" :key="client.codcliente">
                                                <tr>
                                                    <td x-text="client.codcliente"></td>
                                                    <td x-text="client.nombre"></td>
                                                    <td x-text="client.telefono || 'N/A'"></td>
                                                    <td>
                                                        <template x-if="client.current_balance > 0">
                                                            <span class="badge badge-danger" x-text="'Deuda: $' + parseFloat(client.current_balance).toFixed(2)"></span>
                                                        </template>
                                                        <template x-if="client.current_balance == 0">
                                                            <span class="badge badge-success">Al día</span>
                                                        </template>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm">Ver Detalles</button>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template x-if="clients.length === 0">
                                               <tr>
                                                    <td colspan="5" class="text-center">No se encontraron clientes.</td>
                                               </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Common scripts from the project -->
    <script src="assets/script/jquery.min.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="assets/js/app.min.js"></script>
    <script src="assets/js/app.init.horizontal-fullwidth.js"></script>
    <script src="assets/js/app-style-switcher.js"></script>
    <script src="assets/js/perfect-scrollbar.js"></script>
    <script src="assets/js/sparkline.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/sidebarmenu.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
