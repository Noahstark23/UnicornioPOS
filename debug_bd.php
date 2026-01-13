<?php
require_once 'includes/db.php';
$db = DB::connect();
echo '=== TABLA CLIENTES ===';
$cols = $db->query('DESCRIBE clientes')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) echo $c['Field'] . ' | ' . $c['Type'] . PHP_EOL;
echo PHP_EOL . '=== TABLA ARQUEOCAJA ===' . PHP_EOL;
$cols2 = $db->query('DESCRIBE arqueocaja')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols2 as $c) echo $c['Field'] . ' | ' . $c['Type'] . PHP_EOL;
echo PHP_EOL . '=== TABLAS CREDITO ===' . PHP_EOL;
$tables = $db->query('SHOW TABLES LIKE \"%credit%\"')->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) echo $t . PHP_EOL;
?>
