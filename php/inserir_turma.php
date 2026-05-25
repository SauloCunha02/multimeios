<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome']      ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($nome !== '' && $descricao !== '') {
        $stmt = $pdo->prepare(
            "INSERT INTO turmas (nome, descricao, status) VALUES (:nome, :descricao, 1)"
        );
        $stmt->bindParam(':nome',      $nome,      PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->execute();
    }
}

header('Location: turmas.php?msg=success');
exit;
