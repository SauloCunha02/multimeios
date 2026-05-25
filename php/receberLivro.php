<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if (isset($_GET['idEmprestimo'])) {
    $idEmprestimo = (int)$_GET['idEmprestimo'];

    $stmt = $pdo->prepare(
        "UPDATE Emprestimo SET status = 1, dataRecebimento = NOW()
         WHERE idEmprestimo = :idEmprestimo"
    );
    $stmt->bindParam(':idEmprestimo', $idEmprestimo, PDO::PARAM_INT);
    $stmt->execute();
}

header('Location: emprestimo.php?msg=devolvido');
exit;
