<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'livros';
$pageTitle  = 'Cadastrar Livro';

$sucesso = false;
$erro    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo         = trim($_POST['titulo']         ?? '');
    $autor          = trim($_POST['autor']          ?? '');
    $editora        = trim($_POST['editora']        ?? '');
    $edicao         = trim($_POST['edicao']         ?? '');
    $dataPublicacao = trim($_POST['dataPublicacao'] ?? '');
    $lugar          = trim($_POST['lugar']          ?? '');
    $origem         = trim($_POST['origem']         ?? '');
    $categoria      = trim($_POST['categoria']      ?? '');
    $ISBN           = trim($_POST['ISBN']           ?? '');

    if ($titulo && $autor) {
        $sql = "INSERT INTO livros (titulo, autor, editora, edicao, dataPublicacao, lugar, origem, categoria, ISBN)
                VALUES (:titulo, :autor, :editora, :edicao, :dataPublicacao, :lugar, :origem, :categoria, :ISBN)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':titulo'         => $titulo,
            ':autor'          => $autor,
            ':editora'        => $editora,
            ':edicao'         => $edicao,
            ':dataPublicacao' => $dataPublicacao,
            ':lugar'          => $lugar,
            ':origem'         => $origem,
            ':categoria'      => $categoria,
            ':ISBN'           => $ISBN,
        ]);
        $sucesso = true;
    } else {
        $erro = 'Título e Autor são obrigatórios.';
    }
}
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
                    <li class="nav-item"><a href="../livros.php">Livros</a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Cadastrar</a></li>
                </ul>

                <?php if ($sucesso): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Livro cadastrado com sucesso!
                    <a href="../livros.php" class="alert-link ms-2">Ver livros</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                <?php if ($erro): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <div class="card card-round">
                    <div class="card-header"><div class="card-title">Cadastrar Novo Livro</div></div>
                    <div class="card-body">
                        <form method="post" class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="titulo" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Autor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="autor" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Editora</label>
                                <input type="text" class="form-control" name="editora">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Edição</label>
                                <input type="text" class="form-control" name="edicao">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data de Publicação</label>
                                <input type="text" class="form-control" name="dataPublicacao">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lugar</label>
                                <input type="text" class="form-control" name="lugar">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Origem</label>
                                <input type="text" class="form-control" name="origem">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoria</label>
                                <input type="text" class="form-control" name="categoria">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ISBN</label>
                                <input type="text" class="form-control" name="ISBN">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Cadastrar</button>
                                <a href="../livros.php" class="btn btn-secondary ms-2">Voltar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
</body>
</html>
