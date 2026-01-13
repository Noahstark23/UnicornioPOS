<?php
$file = 'c:/xampp/htdocs/unicornio/fpdf/pdf.php';
$lines = file($file);
$lineIndex = 7325; // Line 7326
$line = $lines[$lineIndex];

echo "Original: " . $line . "\n";

// Attempt to parse nested ternary
// Pattern: A ? B : C ? D : E
// We want: A ? B : (C ? D : E)

if (preg_match('/^(.*)\?(.*)\:(.*)\?(.*)\:(.*)$/', trim($line), $matches)) {
    // This simple regex might fail if there are more complex expressions inside, but let's try
    // Actually, let's identify the parts.
    // The error says unparenthesized. 
    // Usually it is: 
    // $val = $cond1 ? $res1 : $cond2 ? $res2 : $res3;
    // Fix:
    // $val = $cond1 ? $res1 : ($cond2 ? $res2 : $res3);
    
    // Let's use a specialized replacement that just adds parens around the second part.
    // Finding the FIRST ':' and the SECOND '?'
    
    $posQ1 = strpos($line, '?');
    $posC1 = strpos($line, ':', $posQ1);
    
    $posQ2 = strpos($line, '?', $posC1);
    $posC2 = strpos($line, ':', $posQ2);
    
    if ($posQ1 !== false && $posC1 !== false && $posQ2 !== false && $posC2 !== false) {
        $part1 = substr($line, 0, $posC1 + 1); // " condition ? value1 :"
        $part2 = substr($line, $posC1 + 1);    // " condition2 ? value2 : value3;"
        
        // Remove semicolon/trailing space from part2 to wrap
        $part2 = trim($part2);
        $semicolon = '';
        if (substr($part2, -1) == ';') {
            $semicolon = ';';
            $part2 = substr($part2, 0, -1);
        } else if (substr($part2, -2) == ');') {
             // It might be inside a function call like utf8_decode(...)
             // This complicates things.
        }
        
        // Let's just try to be smart about the parens.
        // It's likely:
        // utf8_decode( condition1 ? val1 : condition2 ? val2 : val3 )
        
        // Let's replace ": condition2 ?" with ": (condition2 ?" and end with ")"
        
        // Strategy: Inspect the string manually via echo in this script, verify, then write back?
        // No, I need to automate.
        
        // Let's just wrap the 'else' part of the first ternary in parens.
        // But we need to know where it ends.
        
        // If it's inside a function call `utf8_decode(...)`, the line likely matches `utf8_decode(... ? ... : ... ? ... : ...)`
        
        // Let's look for the pattern `? ... : ... ? ... : ...`
        // And replace the second `... ? ... : ...` with `(... ? ... : ...)`
        
        // Find the position of the first `:`
        $firstColon = strpos($line, ':');
        // Find position of second `?` (must be AFTER first colon)
        $secondQuestion = strpos($line, '?', $firstColon);
        
        if ($secondQuestion !== false) {
             // Replace ` : ` with ` : (` ? No.
             // We need to wrap everything after the first `:` (excluding the semicolon/closing paren at end).
             
             // This is risky without seeing the code.
        }
    }
}
?>
