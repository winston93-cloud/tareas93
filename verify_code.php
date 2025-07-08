<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];

    if ($code == $_SESSION['verification_code']) {
        // El código es correcto, redirigir al index.html
        header('Location: index1.php');
        exit();
    } else {
        echo 'Código incorrecto. Inténtalo de nuevo.';
    }
} else {
    header('Location: login.html');
}
?>
