<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idLivro        = (int)$_POST['idLivro'];
    $titulo         = trim($_POST['titulo']         ?? '');
    $autor          = trim($_POST['autor']          ?? '');
    $editora        = trim($_POST['editora']        ?? '');
    $edicao         = trim($_POST['edicao']         ?? '');
    $dataPublicacao = trim($_POST['dataPublicacao'] ?? '');
    $lugar          = trim($_POST['lugar']          ?? '');
    $origem         = trim($_POST['origem']         ?? '');
    $categoria      = trim($_POST['categoria']      ?? '');
    $isbn           = trim($_POST['isbn']           ?? '');

    $stmt = $pdo->prepare(
        "UPDATE livros SET titulo=:titulo, autor=:autor, editora=:editora,
         edicao=:edicao, dataPublicacao=:dataPublicacao, lugar=:lugar,
         origem=:origem, categoria=:categoria, ISBN=:isbn
         WHERE idLivro=:idLivro"
    );
    $stmt->execute([
        ':titulo'         => $titulo,
        ':autor'          => $autor,
        ':editora'        => $editora,
        ':edicao'         => $edicao,
        ':dataPublicacao' => $dataPublicacao,
        ':lugar'          => $lugar,
        ':origem'         => $origem,
        ':categoria'      => $categoria,
        ':isbn'           => $isbn,
        ':idLivro'        => $idLivro,
    ]);

    header("Location: editarLivros.php?idLivro=$idLivro&msg=ok");
    exit;
}

header('Location: ../livros.php');
exit;
