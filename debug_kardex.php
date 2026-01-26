<?php
require_once 'includes/db.php';
header('Content-Type: text/plain');

try {
    $db = DB::connect();
    
    echo "--- CHECKING TABLE: kardex ---\n";
    $stmt = $db->query("SELECT count(*) as count FROM kardex");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total rows in kardex: " . $row['count'] . "\n\n";

    if ($row['count'] > 0) {
        echo "--- SAMPLE DATA (first 5 rows) ---\n";
        $stmt = $db->query("SELECT * FROM kardex LIMIT 5");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($rows);
        
        echo "\n--- CHECKING A SPECIFIC PRODUCT ---\n";
        // Let's grab a 'codproducto' from the sample to test query
        $sampleCode = $rows[0]['codproducto'];
        echo "Testing query for codproducto: '$sampleCode'\n";
        
        $sql = "SELECT 
                fechakardex,
                movimiento,
                documento as coddocumento,
                (entradas + salidas) as cantidad,
                stockactual as saldoactual
                FROM kardex 
                WHERE codproducto = ? 
                ORDER BY fechakardex DESC, idkardex DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$sampleCode]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Query returned " . count($results) . " rows.\n";
        print_r($results); // Muestra lo que devolvería la API
    } else {
        echo "TABLE KARDEX IS EMPTY. This explains why history is empty.\n";
        
        echo "\n--- CHECKING ALTERNATIVE TABLES (detalleventas) ---\n";
        $stmt = $db->query("SELECT count(*) as count FROM detalleventas");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Total rows in detalleventas: " . $row['count'] . "\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
