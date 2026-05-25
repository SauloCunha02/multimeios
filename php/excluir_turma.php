<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        $pdo->beginTransaction();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        // Remove alunos da turma primeiro
        $stmtAlunos = $pdo->prepare("DELETE FROM alunos WHERE turma = :id");
        $stmtAlunos->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtAlunos->execute();

        // Remove a turma
        $stmtTurma = $pdo->prepare("DELETE FROM turmas WHERE id = :id");
        $stmtTurma->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtTurma->execute();

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
}

header('Location: turmas.php?msg=turma_excluida');
exit;
