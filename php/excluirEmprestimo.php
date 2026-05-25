<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if (isset($_GET['idEmprestimo'])) {
    $idEmprestimo = (int)$_GET['idEmprestimo'];

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $stmt = $pdo->prepare("DELETE FROM Emprestimo WHERE idEmprestimo = :idEmprestimo");
    $stmt->bindParam(':idEmprestimo', $idEmprestimo, PDO::PARAM_INT);
    $stmt->execute();
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}

header('Location: emprestimo.php?msg=excluido');
exit;
