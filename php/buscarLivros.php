<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'livros';
$pageTitle  = 'Buscar Livros';

// Filtros via GET
$titulo         = isset($_GET['titulo'])         ? trim($_GET['titulo'])         : '';
$autor          = isset($_GET['autor'])          ? trim($_GET['autor'])          : '';
$editora        = isset($_GET['editora'])        ? trim($_GET['editora'])        : '';
$edicao         = isset($_GET['edicao'])         ? trim($_GET['edicao'])         : '';
$dataPublicacao = isset($_GET['dataPublicacao']) ? trim($_GET['dataPublicacao']) : '';
$lugar          = isset($_GET['lugar'])          ? trim($_GET['lugar'])          : '';
$origem         = isset($_GET['origem'])         ? trim($_GET['origem'])         : '';
$categoria      = isset($_GET['categoria'])      ? trim($_GET['categoria'])      : '';

$sql    = 'SELECT * FROM livros WHERE 1=1';
$params = [];

if ($titulo)         { $sql .= ' AND titulo LIKE :titulo';                 $params['titulo']         = "%$titulo%";         }
if ($autor)          { $sql .= ' AND autor LIKE :autor';                   $params['autor']          = "%$autor%";          }
if ($editora)        { $sql .= ' AND editora LIKE :editora';               $params['editora']        = "%$editora%";        }
if ($edicao)         { $sql .= ' AND edicao LIKE :edicao';                 $params['edicao']         = "%$edicao%";         }
if ($dataPublicacao) { $sql .= ' AND dataPublicacao = :dataPublicacao';    $params['dataPublicacao'] = $dataPublicacao;      }
if ($lugar)          { $sql .= ' AND lugar LIKE :lugar';                   $params['lugar']          = "%$lugar%";          }
if ($origem)         { $sql .= ' AND origem LIKE :origem';                 $params['origem']         = "%$origem%";         }
if ($categoria)      { $sql .= ' AND categoria LIKE :categoria';           $params['categoria']      = "%$categoria%";      }

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <li class="nav-item"><a href="#">Buscar</a></li>
                </ul>

                <div class="d-flex justify-content-end mb-3">
                    <a href="cadastrarLivros.php" class="btn btn-primary btn-round">
                        <i class="fas fa-plus"></i> Adicionar
                    </a>
                </div>

                <h5>Resultado da Busca: <?= count($livros) ?> livro(s)</h5>

                <div class="row mt-3">
                <?php if (count($livros) > 0): ?>
                    <?php foreach ($livros as $livro): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                        <div class="card h-100">
                            <?php if (!empty($livro['foto'])): ?>
                            <img src="<?= htmlspecialchars($livro['foto']) ?>"
                                 alt="Capa" class="card-img-top"
                                 style="max-height:150px; object-fit:cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($livro['titulo']) ?></h6>
                                <p class="card-text small mb-1">
                                    <strong>Autor:</strong> <?= htmlspecialchars($livro['autor']) ?><br>
                                    <strong>Editora:</strong> <?= htmlspecialchars($livro['editora']) ?><br>
                                    <strong>Edição:</strong> <?= htmlspecialchars($livro['edicao']) ?><br>
                                    <strong>Publicação:</strong> <?= htmlspecialchars($livro['dataPublicacao']) ?><br>
                                    <strong>Categoria:</strong> <?= htmlspecialchars($livro['categoria']) ?><br>
                                    <strong>ISBN:</strong> <?= htmlspecialchars($livro['ISBN']) ?>
                                </p>
                            </div>
                            <div class="card-footer d-flex gap-2">
                                <a href="editarLivros.php?idLivro=<?= (int)$livro['idLivro'] ?>"
                                   class="btn btn-success btn-sm flex-fill">Editar</a>
                                <a href="excluirLivros.php?idLivro=<?= (int)$livro['idLivro'] ?>"
                                   class="btn btn-danger btn-sm flex-fill"
                                   onclick="return confirm('Excluir este livro?')">Excluir</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-danger">Nenhum livro encontrado com os critérios informados.</p>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
</body>
</html>
