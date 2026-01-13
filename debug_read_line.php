<?php
$lines = file('c:/xampp/htdocs/unicornio/fpdf/pdf.php');
if(isset($lines[7325])) {
    echo "LINE 7326:\n";
    echo $lines[7325];
} else {
    echo "Line not found";
}
?>
