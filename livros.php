<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: index.php');
    exit;
}

$basePath   = '';
$activePage = 'livros';
$pageTitle  = 'Livros';
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php include 'php/includes/head.php'; ?>
<body>
<div class="wrapper">
    <?php include 'php/includes/sidebar.php'; ?>
    <div class="main-panel">
        <?php include 'php/includes/navbar.php'; ?>

        <div class="container">
            <div class="page-inner">
                <ul class="breadcrumbs mb-3 pt-2">
                    <li class="nav-home">
                        <a href="principal.php"><i class="icon-home"></i></a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Livros</a></li>
                </ul>

                <div class="d-flex justify-content-end gap-2 mb-3">
                    <a href="php/importar_livros.php" class="btn btn-success btn-round">
                        <i class="fas fa-file-csv me-1"></i> Importar em Lote
                    </a>
                    <a href="php/cadastrarLivros.php" class="btn btn-primary btn-round">
                        <i class="fas fa-plus me-1"></i> Adicionar Livro
                    </a>
                </div>

                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-title">Buscar Livros</div>
                    </div>
                    <div class="card-body">
                        <form action="php/buscarLivros.php" method="get" class="row g-3">
                            <div class="col-md-6">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo">
                            </div>
                            <div class="col-md-6">
                                <label for="autor" class="form-label">Autor</label>
                                <input type="text" class="form-control" id="autor" name="autor">
                            </div>
                            <div class="col-md-6">
                                <label for="editora" class="form-label">Editora</label>
                                <input type="text" class="form-control" id="editora" name="editora">
                            </div>
                            <div class="col-md-6">
                                <label for="edicao" class="form-label">Edição</label>
                                <input type="text" class="form-control" id="edicao" name="edicao">
                            </div>
                            <div class="col-md-6">
                                <label for="dataPublicacao" class="form-label">Data de Publicação</label>
                                <input type="text" class="form-control" id="dataPublicacao" name="dataPublicacao">
                            </div>
                            <div class="col-md-6">
                                <label for="lugar" class="form-label">Lugar</label>
                                <input type="text" class="form-control" id="lugar" name="lugar">
                            </div>
                            <div class="col-md-6">
                                <label for="origem" class="form-label">Origem</label>
                                <input type="text" class="form-control" id="origem" name="origem">
                            </div>
                            <div class="col-md-6">
                                <label for="categoria" class="form-label">Categoria</label>
                                <input type="text" class="form-control" id="categoria" name="categoria">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <?php include 'php/includes/footer.php'; ?>
</body>
</html>
