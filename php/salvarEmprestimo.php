<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar'])) {
    $idAluno   = (int)$_POST['idAluno'];
    $idLivro   = (int)$_POST['idLivro'];
    $dataAtual = date('Y-m-d');
    $dataFinal = $_POST['dataFinal'];

    $stmt = $pdo->prepare(
        "INSERT INTO Emprestimo (idA, idL, dataInicial, dataFinal, status)
         VALUES (:idA, :idL, :dataInicial, :dataFinal, 0)"
    );
    $stmt->execute([
        ':idA'         => $idAluno,
        ':idL'         => $idLivro,
        ':dataInicial' => $dataAtual,
        ':dataFinal'   => $dataFinal,
    ]);

    header('Location: emprestimo.php?msg=ok');
    exit;
}

header('Location: selecionarAluno.php');
exit;
