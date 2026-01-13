<?php
session_start();
if (!isset($_SESSION['codsucursal'])) {
    die("Access denied. Please login.");
}

require_once '../class/Client.php';

$tenant_id = $_SESSION['codsucursal'];
$clientModel = new Client();
$clients = $clientModel->listClients($tenant_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients List</title>
    <link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .debt-warning {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Clients List (Tenant ID: <?php echo htmlspecialchars($tenant_id); ?>)</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Credit Limit</th>
                <th>Current Balance</th>
                <th>Actions</th>
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
                        <td class="<?php echo ($client['current_balance'] > 0) ? 'debt-warning' : ''; ?>">
                            <?php echo number_format($client['current_balance'], 2); ?>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">View Ledger</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No clients found for this tenant.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
