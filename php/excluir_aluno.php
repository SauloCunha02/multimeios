<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if (isset($_GET['idAluno']) && isset($_GET['idTurma'])) {
    $idAluno = (int)$_GET['idAluno'];
    $idTurma = (int)$_GET['idTurma'];

    $stmt = $pdo->prepare("DELETE FROM alunos WHERE idAluno = :idAluno");
    $stmt->bindParam(':idAluno', $idAluno, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: alterar_turma.php?idTurma=$idTurma&msg=aluno_excluido");
    exit;
}

header('Location: turmas.php');
exit;
