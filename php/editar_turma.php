<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = (int)$_POST['id'];
    $nome      = trim($_POST['nome']      ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($id > 0 && $nome !== '' && $descricao !== '') {
        $stmt = $pdo->prepare(
            "UPDATE turmas SET nome = :nome, descricao = :descricao WHERE id = :id"
        );
        $stmt->bindParam(':nome',      $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':id',        $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

header('Location: turmas.php?msg=editado');
exit;
