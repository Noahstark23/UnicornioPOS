<?php
$file = 'funciones.php';
if (!file_exists($file)) {
    echo "ERROR: funciones.php not found.\n";
    exit;
}

$content = file_get_contents($file);
$len = strlen($content);
echo "File size: $len bytes\n";

$search = "BuscaProductoVendidos";
$pos = strpos($content, $search);

if ($pos === false) {
    echo "String '$search' NOT FOUND.\n";
} else {
    echo "String '$search' FOUND at position $pos.\n";
    // Show context
    $start = max(0, $pos - 100);
    $length = 200;
    echo "Context:\n" . substr($content, $start, $length) . "\n";
    
    // Find line number
    $lines = explode("\n", $content);
    $lineNum = 0;
    $count = 0;
    foreach ($lines as $line) {
        $count += strlen($line) + 1; // +1 for newline
        $lineNum++;
        if ($count > $pos) {
            echo "Line Number: $lineNum\n";
            break;
        }
    }
}
?>
