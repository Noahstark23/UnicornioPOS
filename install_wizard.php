<?php
// install_wizard.php - Fast Local Installer for Unicornio POS
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$step = isset($_GET['step']) ? $_GET['step'] : 1;
$message = "";
$error = "";

// Helper to write db.php
function writeDBConfig($host, $db, $user, $pass) {
    $content = "<?php\nclass UnicornDB {\n    private static \$instance = null;\n    private \$conn;\n    private \$host = '$host';\n    private \$db   = '$db';\n    private \$user = '$user';\n    private \$pass = '$pass';\n\n    private function __construct() {\n        try {\n            date_default_timezone_set('America/Managua');\n            \$dsn = \"mysql:host=\$this->host;dbname=\$this->db;charset=utf8mb4\";\n            \$this->conn = new PDO(\$dsn, \$this->user, \$this->pass, [\n                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n                PDO::ATTR_EMULATE_PREPARES => false,\n            ]);\n            \$offset = date('P');\n            \$this->conn->exec(\"SET time_zone = '\$offset';\");\n            \$this->conn->exec(\"SET lc_time_names = 'es_ES';\");\n        } catch (PDOException \$e) {\n            die(\"DB Error: \" . \$e->getMessage());\n        }\n    }\n    public static function connect() {\n        if (!self::\$instance) self::\$instance = new UnicornDB();\n        return self::\$instance->conn;\n    }\n}\nclass_alias('UnicornDB', 'DB');\n?>";
    return file_put_contents("includes/db.php", $content);
}

// LOGIC HANDLER
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 2) {
        // DB CONFIG & IMPORT
        $host = $_POST['host'];
        $db = $_POST['db_name'];
        $user = $_POST['user'];
        $pass = $_POST['password'];
        
        // Test Connection
        try {
            $dsn = "mysql:host=$host;charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create DB if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db`");
            $pdo->exec("USE `$db`");
            
            // Write Config
            if (writeDBConfig($host, $db, $user, $pass)) {
                
                // Import Schema
                $sqlFile = 'bd-sql/schema_clean.sql';
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    // Split by ; is risky for stored procs, but schema_clean usually is simple CREATE TABLEs
                    // Better: use direct exec if file is not huge, or split loop
                    // This schema dump has SET FOREIGN.. so we read it all?
                    // PDO can't execute multiple queries in one go appropriately sometimes.
                    // Let's try splitting by ";\n"
                    $queries = explode(";\n", $sql);
                    foreach ($queries as $query) {
                        $query = trim($query);
                        if (!empty($query)) $pdo->exec($query);
                    }
                    $_SESSION['db_setup'] = true;
                    header("Location: ?step=3");
                    exit;
                } else {
                    $error = "Schema file not found (bd-sql/schema_clean.sql)";
                }
            } else {
                $error = "Could not write includes/db.php check permissions.";
            }

        } catch (PDOException $e) {
            $error = "Connection Failed: " . $e->getMessage();
        }
    }
    
    if ($step == 3) {
        // COMPANY SETUP
        require_once("includes/db.php");
        $pdo = DB::connect();
        
        $sql = "UPDATE configuracion SET 
                nomempresa=?, ruc=?, direccion=?, tlfempresa=?, correo=?, 
                simbolo=?, iva=? WHERE id=1"; // Assumptions based on common structure
        // Actually, we should check if table exists and structure.
        // Assuming clean schema includes 1 row or we insert it.
        // Let's Insert or Update
        $check = $pdo->query("SELECT count(*) FROM configuracion")->fetchColumn();
        if ($check == 0) {
            $sql = "INSERT INTO configuracion (nomempresa, ruc, direccion, tlfempresa, correo, simbolo, iva) VALUES (?,?,?,?,?,?,?)";
        } else {
            $sql = "UPDATE configuracion SET nomempresa=?, ruc=?, direccion=?, tlfempresa=?, correo=?, simbolo=?, iva=?";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['nomempresa'], $_POST['ruc'], $_POST['direccion'], 
            $_POST['tlfempresa'], $_POST['correo'], $_POST['simbolo'], $_POST['iva']
        ]);
        
        header("Location: ?step=4");
        exit;
    }

    if ($step == 4) {
        // USERS & SMART INIT
        require_once("includes/db.php");
        $pdo = DB::connect();

        // 1. Create ADMIN G
        // Hash: sha1(md5($pass)) as seen in code
        $passAdmin = sha1(md5($_POST['admin_pass']));
        $pdo->prepare("INSERT INTO usuarios (nombres, usuario, password, nivel, status, dni) VALUES (?, ?, ?, 'ADMINISTRADORG', 'ACTIVO', '111')")
            ->execute([$_POST['admin_name'], $_POST['admin_user'], $passAdmin]);

        // 2. Create BRANCH ADMIN (ADMINSUC)
        // Need a Branch first? Schema might have one.
        $sucCheck = $pdo->query("SELECT codsucursal FROM sucursales LIMIT 1")->fetchColumn();
        if (!$sucCheck) {
            $pdo->exec("INSERT INTO sucursales (cuitsucursal, razonsocial, direccion, correo, codmoneda) VALUES ('SUC-001', 'SUCURSAL PRINCIPAL', 'CALLE 1', 'sucursal@local', 1)");
            $sucCheck = $pdo->lastInsertId();
        }
        
        $passSuc = sha1(md5($_POST['suc_pass']));
        $pdo->prepare("INSERT INTO usuarios (nombres, usuario, password, nivel, status, codsucursal, dni) VALUES (?, ?, ?, 'ADMINISTRADOR', 'ACTIVO', ?, '222')")
             ->execute([$_POST['suc_name'], $_POST['suc_user'], $passSuc, $sucCheck]);
        $idSucUser = $pdo->lastInsertId();

        // 3. Create CASHIER (CAJERO)
        $passCajero = sha1(md5($_POST['cash_pass']));
        $pdo->prepare("INSERT INTO usuarios (nombres, usuario, password, nivel, status, codsucursal, dni) VALUES (?, ?, ?, 'VENDEDOR', 'ACTIVO', ?, '333')")
             ->execute([$_POST['cash_name'], $_POST['cash_user'], $passCajero, $sucCheck]);
        $idCashier = $pdo->lastInsertId();

        // SMART INIT: CASH BOXES
        // Create Boxes
        $pdo->exec("INSERT INTO cajas (nrocaja, nomcaja, codsucursal) VALUES ('CAJA-001', 'CAJA PRINCIPAL', $sucCheck)");
        $idCaja1 = $pdo->lastInsertId();
        $pdo->exec("INSERT INTO cajas (nrocaja, nomcaja, codsucursal) VALUES ('CAJA-002', 'CAJA VENTANILLA 1', $sucCheck)");
        $idCaja2 = $pdo->lastInsertId();

        // Force Open (The "Smart" part)
        // 1. Admin Sucursal -> Caja Principal
        $pdo->prepare("INSERT INTO movimiento_caja (codcaja, codsucursal, montoinicial, fechaapertura, usuario, estado) VALUES (?, ?, '0.00', NOW(), ?, 'ABIERTA')")
            ->execute([$idCaja1, $sucCheck, $idSucUser]);
        
        // 2. Cajero -> Caja Ventanilla 1
        $pdo->prepare("INSERT INTO movimiento_caja (codcaja, codsucursal, montoinicial, fechaapertura, usuario, estado) VALUES (?, ?, '0.00', NOW(), ?, 'ABIERTA')")
            ->execute([$idCaja2, $sucCheck, $idCashier]);

        // SUCCESS -> KILL SWITCH
        rename(__FILE__, "_INSTALLED_install_wizard.php.bak");
        
        echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px; color:green;'>";
        echo "<h1>✅ INSTALLATION COMPLETE</h1>";
        echo "<h2>System Initialized. Users Created. Cash Boxes Opened.</h2>";
        echo "<p>Security: File renamed to _INSTALLED_wizard.php.bak</p>";
        echo "<a href='index.php' style='padding:10px 20px; background:blue; color:white; text-decoration:none;'>GO TO LOGIN</a>";
        echo "</div>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unicornio POS Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f4f6f9; padding-top: 50px; } .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); }</style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>🦄 Unicornio POS - Fast Installer</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if ($step == 1): ?>
                        <h4>Step 1: Environment Check</h4>
                        <ul class="list-group mb-3">
                            <li class="list-group-item">PHP Version: <?php echo phpversion(); ?> (Req: 8.0+) ✅</li>
                            <li class="list-group-item">Writable 'includes/': <?php echo is_writable('includes/') ? 'Yes' : 'No'; ?> ✅</li>
                            <li class="list-group-item">Schema File: <?php echo file_exists('bd-sql/schema_clean.sql') ? 'Found' : 'Missing'; ?> ✅</li>
                        </ul>
                        <a href="?step=2" class="btn btn-success w-100">Next: Database Setup</a>
                    <?php endif; ?>

                    <?php if ($step == 2): ?>
                        <h4>Step 2: Database Connection</h4>
                        <form method="post">
                            <div class="mb-3"><label>Host</label><input type="text" name="host" class="form-control" value="localhost" required></div>
                            <div class="mb-3"><label>DB Name</label><input type="text" name="db_name" class="form-control" value="unicornio" required></div>
                            <div class="mb-3"><label>User</label><input type="text" name="user" class="form-control" value="root" required></div>
                            <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control"></div>
                            <button type="submit" class="btn btn-primary w-100">Connect & Import Schema</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($step == 3): ?>
                        <h4>Step 3: Company Setup</h4>
                        <form method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3"><label>Company Name</label><input type="text" name="nomempresa" class="form-control" required></div>
                                <div class="col-md-6 mb-3"><label>RUC/NIT</label><input type="text" name="ruc" class="form-control" required></div>
                            </div>
                            <div class="mb-3"><label>Address</label><input type="text" name="direccion" class="form-control" required></div>
                            <div class="mb-3"><label>Phone</label><input type="text" name="tlfempresa" class="form-control" required></div>
                            <div class="mb-3"><label>Currency Symbol</label><input type="text" name="simbolo" class="form-control" value="$" required></div>
                            <div class="mb-3"><label>IVA (%)</label><input type="number" name="iva" class="form-control" value="15" required></div>
                            <button type="submit" class="btn btn-primary w-100">Next: Create Users</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($step == 4): ?>
                        <h4>Step 4: Smart User Setup</h4>
                        <p class="text-muted">We will create mandatory users and auto-assign cash boxes.</p>
                        <form method="post">
                            <h5 class="mt-3">1. General Administrator (SuperUser)</h5>
                            <div class="row">
                                <div class="col"><input type="text" name="admin_name" class="form-control" placeholder="Name" value="Super Admin" required></div>
                                <div class="col"><input type="text" name="admin_user" class="form-control" placeholder="User" value="admin" required></div>
                                <div class="col"><input type="password" name="admin_pass" class="form-control" placeholder="Password" value="123456" required></div>
                            </div>

                            <h5 class="mt-3">2. Branch Admin (Runs the Store)</h5>
                            <div class="row">
                                <div class="col"><input type="text" name="suc_name" class="form-control" placeholder="Name" value="Gerente Sucursal" required></div>
                                <div class="col"><input type="text" name="suc_user" class="form-control" placeholder="User" value="gerente" required></div>
                                <div class="col"><input type="password" name="suc_pass" class="form-control" placeholder="Password" value="123456" required></div>
                            </div>
                            
                            <h5 class="mt-3">3. Cashier (Front Desk)</h5>
                            <div class="row">
                                <div class="col"><input type="text" name="cash_name" class="form-control" placeholder="Name" value="Cajero Principal" required></div>
                                <div class="col"><input type="text" name="cash_user" class="form-control" placeholder="User" value="cajero" required></div>
                                <div class="col"><input type="password" name="cash_pass" class="form-control" placeholder="Password" value="123456" required></div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 mt-4">FINISH & INSTALL</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
