<?php
$lines = file('c:/xampp/htdocs/unicornio/fpdf/pdf.php');
$line = $lines[7325];
echo "Length: " . strlen($line) . "\n";
echo chunk_split($line, 80, "\n");
?>
