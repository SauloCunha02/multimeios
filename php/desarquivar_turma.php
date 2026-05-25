<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if (isset($_GET['id'])) {
    $idTurma = (int)$_GET['id'];

    try {
        $pdo->beginTransaction();

        $pdo->prepare("UPDATE turmas SET status = 1 WHERE id = :id")
            ->execute([':id' => $idTurma]);

        $pdo->prepare("UPDATE alunos SET status = 1 WHERE turma = :id")
            ->execute([':id' => $idTurma]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}

header('Location: turmas.php?msg=desarquivada');
exit;
