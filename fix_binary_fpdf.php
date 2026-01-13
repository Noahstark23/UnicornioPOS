<?php
$file = 'c:/xampp/htdocs/unicornio/fpdf/pdf.php';
$content = file_get_contents($file);

$search = ': $reg[$i][\'fechavencecredito\'] < date("Y-m-d") && $reg[$i][\'fechapagado\']== "0000-00-00" ? Dias_Transcurridos(date("Y-m-d"),$reg[$i][\'fechavencecredito\']) : Dias_Transcurridos($reg[$i][\'fechapagado\'],$reg[$i][\'fechavencecredito\'])';
$replace = ': ($reg[$i][\'fechavencecredito\'] < date("Y-m-d") && $reg[$i][\'fechapagado\']== "0000-00-00" ? Dias_Transcurridos(date("Y-m-d"),$reg[$i][\'fechavencecredito\']) : Dias_Transcurridos($reg[$i][\'fechapagado\'],$reg[$i][\'fechavencecredito\']))';

if (strpos($content, $search) !== false) {
    echo "String found. Replaciing...\n";
    $newOwner = str_replace($search, $replace, $content);
    file_put_contents($file, $newOwner);
    echo "Fixed.";
} else {
    echo "String NOT found. Normalizing spaces might be needed.\n";
    // Let's try matching with flexible whitespace if strict match fails
    // But for now, let's see.
    
    // Debug: output a small chunk around where we think it is
    // We knew it was line 7328.
    $lines = file($file);
    echo "Line 7328 content: " . trim($lines[7328]) . "\n";
}
?>
