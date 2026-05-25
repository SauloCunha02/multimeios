<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'livros';
$pageTitle  = 'Editar Livro';

if (!isset($_GET['idLivro'])) {
    header('Location: ../livros.php');
    exit;
}

$idLivro = (int)$_GET['idLivro'];
$stmt    = $pdo->prepare("SELECT * FROM livros WHERE idLivro = :idLivro");
$stmt->bindParam(':idLivro', $idLivro, PDO::PARAM_INT);
$stmt->execute();
$livro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$livro) {
    header('Location: ../livros.php');
    exit;
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
                    <li class="nav-item"><a href="#">Editar</a></li>
                </ul>

                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-title">Editar: <?= htmlspecialchars($livro['titulo']) ?></div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="atualizarLivros.php" class="row g-3">
                            <input type="hidden" name="idLivro" value="<?= $idLivro ?>">
                            <div class="col-md-6">
                                <label class="form-label">Título</label>
                                <input type="text" class="form-control" name="titulo"
                                       value="<?= htmlspecialchars($livro['titulo']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Autor</label>
                                <input type="text" class="form-control" name="autor"
                                       value="<?= htmlspecialchars($livro['autor']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Editora</label>
                                <input type="text" class="form-control" name="editora"
                                       value="<?= htmlspecialchars($livro['editora']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Edição</label>
                                <input type="text" class="form-control" name="edicao"
                                       value="<?= htmlspecialchars($livro['edicao']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data de Publicação</label>
                                <input type="text" class="form-control" name="dataPublicacao"
                                       value="<?= htmlspecialchars($livro['dataPublicacao']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lugar</label>
                                <input type="text" class="form-control" name="lugar"
                                       value="<?= htmlspecialchars($livro['lugar']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Origem</label>
                                <input type="text" class="form-control" name="origem"
                                       value="<?= htmlspecialchars($livro['origem']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoria</label>
                                <input type="text" class="form-control" name="categoria"
                                       value="<?= htmlspecialchars($livro['categoria']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ISBN</label>
                                <input type="text" class="form-control" name="isbn"
                                       value="<?= htmlspecialchars($livro['ISBN']) ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="../livros.php" class="btn btn-secondary ms-2">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
</body>
</html>
