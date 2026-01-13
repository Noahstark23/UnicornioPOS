<?php
session_start();
if (!isset($_SESSION['codsucursal'])) {
    die("Access denied. Please login.");
}

// Fixed path: relative to root now
require_once 'class/Client.php';

$tenant_id = $_SESSION['codsucursal'];
$clientModel = new Client();
$clients = $clientModel->listClients($tenant_id);

// Incluir Header del Sistema
require_once 'includes/header.php';
?>

<div class="page-wrapper">
    <div class="container-fluid">
        
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h4 class="text-themecolor">Listado de Clientes (SaaS)</h4>
            </div>
            <div class="col-md-7 align-self-center text-right">
                <div class="d-flex justify-content-end align-items-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="panel">Inicio</a></li>
                        <li class="breadcrumb-item active">Clientes SaaS</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Directorio de Clientes (Tenant ID: <?php echo htmlspecialchars($tenant_id); ?>)</h4>
                        <h6 class="card-subtitle">Gestión centralizada de clientes y saldos</h6>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Límite Crédito</th>
                                        <th>Saldo Actual</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($clients) > 0): ?>
                                        <?php foreach ($clients as $client): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($client['idcliente']); ?></td>
                                                <td><?php echo htmlspecialchars($client['nomcliente']); ?></td>
                                                <td><?php echo htmlspecialchars($client['emailcliente']); ?></td>
                                                <td><?php echo number_format($client['limitecredito'], 2); ?></td>
                                                <td class="<?php echo ($client['current_balance'] > 0) ? 'text-danger font-weight-bold' : ''; ?>">
                                                    <?php echo number_format($client['current_balance'], 2); ?>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-sm btn-info"><i class="fa fa-list-alt"></i> Ver Ledger</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No hay clientes registrados en este Tenant.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
