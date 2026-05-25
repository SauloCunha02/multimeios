<?php
session_start();

include '../bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$usuario = $_POST['usuario'] ?? '';
$senha   = $_POST['senha']   ?? '';

$sql  = 'SELECT * FROM usuarios WHERE usuario = :usuario';
$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario' => $usuario]);
$user = $stmt->fetch();

if ($user && password_verify($senha, $user['senha'])) {
    $_SESSION['autenticado'] = '1';
    $_SESSION['nome']        = $user['nome'];
    $_SESSION['usuario']     = $user['usuario'];
    $_SESSION['foto']        = $user['foto'];
    header('Location: ../principal.php');
    exit;
}

header('Location: ../index.php?erro=1');
exit;
