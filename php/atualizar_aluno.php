<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAluno   = (int)$_POST['idAluno'];
    $idTurma   = (int)$_POST['idTurma'];
    $nome      = trim($_POST['nome']);
    $matricula = trim($_POST['matricula']);
    $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    $stmt = $pdo->prepare(
        "UPDATE alunos SET nome = :nome, matricula = :matricula, status = :status
         WHERE idAluno = :idAluno"
    );
    $stmt->bindParam(':nome',      $nome);
    $stmt->bindParam(':matricula', $matricula);
    $stmt->bindParam(':status',    $status,   PDO::PARAM_INT);
    $stmt->bindParam(':idAluno',   $idAluno,  PDO::PARAM_INT);
    $stmt->execute();

    header("Location: alterar_turma.php?idTurma=$idTurma&msg=editado");
    exit;
}

header('Location: turmas.php');
exit;
