<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}

$basePath   = '../';
$activePage = 'emprestimos';
$pageTitle  = 'Confirmar Empréstimo';

if (!isset($_GET['idAluno']) || !isset($_GET['idLivro'])) {
    header('Location: selecionarAluno.php');
    exit;
}

$idAluno  = (int)$_GET['idAluno'];
$idLivro  = (int)$_GET['idLivro'];
$nome     = $_GET['nome']   ?? '';
$turma    = $_GET['turma']  ?? '';
$titulo   = $_GET['titulo'] ?? '';
$autor    = $_GET['autor']  ?? '';
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
                    <li class="nav-item"><a href="#">Confirmar</a></li>
                </ul>
                <div class="card card-round">
                    <div class="card-header"><div class="card-title">Confirmar Empréstimo</div></div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card card-stats">
                                    <div class="card-body">
                                        <p class="card-category">Aluno</p>
                                        <h5 class="card-title"><?= htmlspecialchars($nome) ?></h5>
                                        <p class="text-muted"><?= htmlspecialchars($turma) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-stats">
                                    <div class="card-body">
                                        <p class="card-category">Livro</p>
                                        <h5 class="card-title"><?= htmlspecialchars($titulo) ?></h5>
                                        <p class="text-muted"><?= htmlspecialchars($autor) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form method="post" action="salvarEmprestimo.php">
                            <input type="hidden" name="idAluno" value="<?= $idAluno ?>">
                            <input type="hidden" name="idLivro" value="<?= $idLivro ?>">
                            <div class="mb-3">
                                <label for="dataFinal" class="form-label">
                                    <strong>Data de Devolução</strong>
                                </label>
                                <input type="date" name="dataFinal" id="dataFinal"
                                       class="form-control col-md-4"
                                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                       required>
                            </div>
                            <button type="submit" name="enviar" class="btn btn-success">
                                <i class="fas fa-save"></i> Salvar Empréstimo
                            </button>
                            <a href="selecionarLivro.php?idAluno=<?= $idAluno ?>&nome=<?= urlencode($nome) ?>&turma=<?= urlencode($turma) ?>"
                               class="btn btn-secondary ms-2">Voltar</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
</body>
</html>
