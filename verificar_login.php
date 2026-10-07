<?php

session_start();

$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($usuario === 'admin' && $senha === '123') {

    $_SESSION['admin'] = true;

    header("Location: admin.php");
    exit;

}

header("Location: login.php?erro=1");
exit;