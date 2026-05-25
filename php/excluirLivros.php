<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if (isset($_GET['idLivro'])) {
    $idLivro = (int)$_GET['idLivro'];

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $stmt = $pdo->prepare("DELETE FROM livros WHERE idLivro = :idLivro");
    $stmt->bindParam(':idLivro', $idLivro, PDO::PARAM_INT);
    $stmt->execute();
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}

header('Location: ../livros.php?msg=excluido');
exit;
