<?php
require_once 'class/class.php';

try {
    $reflector = new ReflectionClass('Login');
    $method = $reflector->getMethod('BuscarProductosVendidos');
    echo "Method found in: " . $method->getFileName() . "\n";
    echo "Start Line: " . $method->getStartLine() . "\n";
    echo "End Line: " . $method->getEndLine() . "\n";
} catch (Exception $e) {
    echo "Method not found: " . $e->getMessage() . "\n";
    // List all methods to see if I'm crazy
    $methods = get_class_methods('Login');
    echo "Available methods:\n";
    print_r($methods);
}
?>
