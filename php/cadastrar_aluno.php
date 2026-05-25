<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    http_response_code(401);
    echo 'Não autorizado.';
    exit;
}
include '../bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Método inválido.';
    exit;
}

$nome    = trim($_POST['nome']    ?? '');
$idTurma = (int)($_POST['idTurma'] ?? 0);

if ($nome === '' || $idTurma <= 0) {
    http_response_code(400);
    echo 'Nome e turma são obrigatórios.';
    exit;
}

$matricula = !empty($_POST['matricula']) ? trim($_POST['matricula']) : null;
$senha     = !empty($_POST['senha'])     ? password_hash(trim($_POST['senha']), PASSWORD_DEFAULT) : null;

try {
    $stmt = $pdo->prepare(
        "INSERT INTO alunos (nome, matricula, senha, turma, status)
         VALUES (:nome, :matricula, :senha, :turma, 1)"
    );
    $stmt->execute([
        ':nome'      => $nome,
        ':matricula' => $matricula,
        ':senha'     => $senha,
        ':turma'     => $idTurma,
    ]);
    echo 'Aluno cadastrado com sucesso!';
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Erro ao cadastrar o aluno.';
}
