<?php
session_start();
if (!isset($_GET['step'])) {
    $_SESSION['test_var'] = 'Hello Unicorn';
    echo "Session started. Var set. <a href='debug_session.php?step=2'>Click to check</a>";
} else {
    echo "Session check: " . ($_SESSION['test_var'] ?? 'MISSING');
}
?>
