<?php
$lines = file('c:/xampp/htdocs/unicornio/fpdf/pdf.php');
$block = "";
for($i=13130; $i<13160; $i++) {
    $block .= ($i+1) . ": " . $lines[$i];
}
file_put_contents('c:/xampp/htdocs/unicornio/block_13141.txt', $block);
?>
