<?php
$file = 'c:/xampp/htdocs/unicornio/fpdf/pdf.php';
$content = file_get_contents($file);

// We need to look for '$ratio = ($w-$this->cMargin*2)/$str_width;'
// This is line 13141 based on previous read.

// BUT, to be safe with white space, we'll try to match the exact string from the block read.
$lines = file($file);
$targetLine = 13140; // index is 0-based. Line 13141 is index 13140.

$original = trim($lines[$targetLine]);
echo "Checking line 13141: '$original'\n";

if (strpos($original, '$ratio = ($w-$this->cMargin*2)/$str_width;') !== false) {
    // Replace logic
    // If $str_width is 0, ratio should be 1? Or just return?
    // If text is empty, CellFit usually just draws the cell background/border but prints nothing.
    // Let's modify: if($str_width == 0) $ratio = 1; else ...
    
    $newLine = '        $ratio = ($str_width == 0) ? 1 : ($w-$this->cMargin*2)/$str_width;' . "\n";
    $lines[$targetLine] = $newLine;
    
    file_put_contents($file, implode("", $lines));
    echo "Fixed DivisionByZero.";
} else {
    echo "Line content mismatch. Aborting.";
}
?>
