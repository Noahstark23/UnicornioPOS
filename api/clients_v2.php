<?php
session_start();
header('Content-Type: application/json');

// Check for session and permissions
if (!isset($_SESSION['acceso']) || ($_SESSION['acceso'] != "administradorG" && $_SESSION["acceso"] != "administradorS" && $_SESSION["acceso"] != "secretaria")) {
    http_response_code(403);
    echo json_encode(['error' => 'No tienes permiso para acceder a esta página.']);
    exit;
}

require_once("../class/classconexion.php");

try {
    $db = Db::getInstance();
    $dbh = $db->getConnection();

    $codsucursal = $_SESSION['codsucursal'];

    // The user mentioned a `current_balance` column, but based on the existing codebase,
    // the client's debt is stored in the `creditosxclientes` table as `montocredito`.
    // A LEFT JOIN is used here to fetch that value. COALESCE ensures we get 0 if no record exists.
    $sql = "SELECT
                c.codcliente,
                c.nomcliente AS nombre,
                c.tlfcliente AS telefono,
                COALESCE(cc.montocredito, 0) AS current_balance
            FROM
                clientes c
            LEFT JOIN
                creditosxclientes cc ON c.codcliente = cc.codcliente AND cc.codsucursal = :codsucursal";

    // According to memory, a tenant_id column exists for data isolation.
    // The current session's sucursal code should be used as the tenant_id.
    if (isset($_SESSION['codsucursal'])) {
        $sql .= " WHERE c.tenant_id = :tenant_id";
    }

    $stmt = $dbh->prepare($sql);

    if (isset($_SESSION['codsucursal'])) {
        $stmt->bindParam(':codsucursal', $codsucursal, PDO::PARAM_INT);
        $stmt->bindParam(':tenant_id', $codsucursal, PDO::PARAM_INT);
    }

    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($clientes);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
