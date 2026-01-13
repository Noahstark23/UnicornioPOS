<?php
$lines = file('c:/xampp/htdocs/unicornio/fpdf/pdf.php');
$block = "";
for($i=7320; $i<7335; $i++) {
    $block .= ($i+1) . ": " . $lines[$i];
}
file_put_contents('c:/xampp/htdocs/unicornio/block_7326.txt', $block);
?>
