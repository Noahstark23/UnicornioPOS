<?php
// debug_api_final.php
session_start();
$_SESSION['codigo'] = 'LEGACY_ADMIN'; 
unset($_SESSION['id_usuario']);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['q'] = 'PINEDA';

ob_start();
require 'api/clients_v2.php';
$output = ob_get_clean();

echo "Raw Output: " . $output . "\n\n";

$data = json_decode($output, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON Error: " . json_last_error_msg() . "\n";
} else {
    foreach($data as $row) {
        if (stripos($row['nombre'], 'PINEDA') !== false) {
            echo "MATCH: " . $row['nombre'] . " | Balance: " . $row['current_balance'] . " (" . gettype($row['current_balance']) . ")\n";
        }
    }
}
?>
