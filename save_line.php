<?php
$lines = file('c:/xampp/htdocs/unicornio/fpdf/pdf.php');
$line = $lines[7325];
file_put_contents('c:/xampp/htdocs/unicornio/line_7326.txt', trim($line));
?>
