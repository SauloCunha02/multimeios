<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'emprestimos';
$pageTitle  = 'Selecionar Aluno';

$nome  = isset($_POST['nome'])  ? trim($_POST['nome'])  : '';
$turma = isset($_POST['turma']) ? trim($_POST['turma']) : '';

$sql    = "SELECT alunos.*, turmas.nome AS turma_nome
           FROM alunos JOIN turmas ON alunos.turma = turmas.id
           WHERE alunos.status = 1";
$params = [];
if ($nome)  { $sql .= " AND alunos.nome LIKE :nome";   $params[':nome']  = "%$nome%";  }
if ($turma) { $sql .= " AND turmas.nome LIKE :turma";  $params[':turma'] = "%$turma%"; }

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php include 'includes/head.php'; ?>
<body>
<div class="wrapper">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-panel">
        <?php include 'includes/navbar.php'; ?>
        <div class="container">
            <div class="page-inner">
                <ul class="breadcrumbs mb-3 pt-2">
                    <li class="nav-home"><a href="../principal.php"><i class="icon-home"></i></a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="emprestimo.php">Empréstimos</a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Selecionar Aluno</a></li>
                </ul>
                <div class="card card-round">
                    <div class="card-header"><div class="card-title">Selecione um Aluno</div></div>
                    <div class="card-body">
                        <form class="row g-2 mb-3" method="post"
                              action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="nome"
                                       placeholder="Nome do aluno"
                                       value="<?= htmlspecialchars($nome) ?>">
                            </div>
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="turma"
                                       placeholder="Turma"
                                       value="<?= htmlspecialchars($turma) ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Pesquisar</button>
                            </div>
                        </form>
                        <p class="text-muted">Total: <?= count($alunos) ?></p>
                        <?php if (count($alunos) > 0): ?>
                        <div class="list-group">
                            <?php $i = 1; foreach ($alunos as $aluno): ?>
                            <a href="selecionarLivro.php?idAluno=<?= (int)$aluno['idAluno'] ?>&nome=<?= urlencode($aluno['nome']) ?>&turma=<?= urlencode($aluno['turma_nome']) ?>"
                               class="list-group-item list-group-item-action list-group-item-success">
                                <?= $i++ ?> | <?= htmlspecialchars($aluno['nome']) ?> | <?= htmlspecialchars($aluno['turma_nome']) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-danger">Nenhum aluno encontrado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
</body>
</html>
