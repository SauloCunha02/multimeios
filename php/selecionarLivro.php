<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'emprestimos';
$pageTitle  = 'Selecionar Livro';

if (!isset($_GET['idAluno'])) {
    header('Location: selecionarAluno.php');
    exit;
}

$idAluno  = (int)$_GET['idAluno'];
$nomeAluno = isset($_GET['nome'])  ? $_GET['nome']  : '';
$turmaAluno= isset($_GET['turma']) ? $_GET['turma'] : '';

$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';

$sql    = "SELECT * FROM livros WHERE 1=1";
$params = [];
if ($titulo) {
    $sql .= " AND titulo LIKE :titulo";
    $params[':titulo'] = "%$titulo%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$titulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <li class="nav-item"><a href="#">Selecionar Livro</a></li>
                </ul>
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-title">
                            Livro para: <?= htmlspecialchars($nomeAluno) ?>
                            (<?= htmlspecialchars($turmaAluno) ?>)
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="row g-2 mb-3" method="post"
                              action="<?= htmlspecialchars($_SERVER['PHP_SELF'])
                                         . '?idAluno=' . $idAluno
                                         . '&nome='   . urlencode($nomeAluno)
                                         . '&turma='  . urlencode($turmaAluno) ?>">
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="titulo"
                                       placeholder="Título do livro"
                                       value="<?= htmlspecialchars($titulo) ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Buscar</button>
                            </div>
                        </form>
                        <p class="text-muted">Total: <?= count($titulos) ?></p>
                        <?php if (count($titulos) > 0): ?>
                        <div class="list-group">
                            <?php $i = 1; foreach ($titulos as $livro): ?>
                            <a href="selecionarData.php?idAluno=<?= $idAluno ?>&nome=<?= urlencode($nomeAluno) ?>&turma=<?= urlencode($turmaAluno) ?>&idLivro=<?= (int)$livro['idLivro'] ?>&titulo=<?= urlencode($livro['titulo']) ?>&autor=<?= urlencode($livro['autor']) ?>"
                               class="list-group-item list-group-item-action list-group-item-success">
                                <?= $i++ ?> | <?= htmlspecialchars($livro['titulo']) ?> | <?= htmlspecialchars($livro['autor']) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-danger">Nenhum livro encontrado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
</body>
</html>
