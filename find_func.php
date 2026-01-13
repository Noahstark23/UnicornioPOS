<?php
$lines = file('c:/xampp/htdocs/unicornio/class/class.php');
foreach($lines as $k => $v) {
    if(strpos($v, "function RegistrarCompras") !== false) {
        echo "Found at line: " . ($k+1) . "\n";
        exit;
    }
}
echo "Not found";
?>
