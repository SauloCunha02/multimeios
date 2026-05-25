<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'turmas';
$pageTitle  = 'Turmas';

$turmas = $pdo->query("SELECT * FROM turmas ORDER BY status DESC, nome ASC")->fetchAll(PDO::FETCH_ASSOC);
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
                    <li class="nav-item"><a href="#">Turmas</a></li>
                </ul>

                <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Operação realizada com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="d-flex justify-content-end gap-2 mb-3">
                    <a href="importar_turmas.php" class="btn btn-success btn-round">
                        <i class="fas fa-file-csv me-1"></i> Importar em Lote
                    </a>
                    <button type="button" class="btn btn-primary btn-round"
                            data-bs-toggle="modal" data-bs-target="#modalNovaTurma">
                        <i class="fas fa-plus me-1"></i> Nova Turma
                    </button>
                </div>

                <div class="card card-round">
                    <div class="card-header"><div class="card-title">Turmas Cadastradas</div></div>
                    <div class="card-body">
                        <?php if (count($turmas) > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr><th>Nome</th><th>Descrição</th><th>Status</th><th>Ações</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($turmas as $turma): ?>
                                <tr>
                                    <td><?= htmlspecialchars($turma['nome']) ?></td>
                                    <td><?= htmlspecialchars($turma['descricao']) ?></td>
                                    <td>
                                        <?php if ($turma['status'] == 1): ?>
                                            <span class="badge bg-success">Ativa</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Arquivada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="alterar_turma.php?idTurma=<?= (int)$turma['id'] ?>"
                                           class="btn btn-info btn-sm">Alunos</a>
                                        <button type="button" class="btn btn-warning btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditar<?= (int)$turma['id'] ?>">Editar</button>
                                        <?php if ($turma['status'] == 1): ?>
                                        <a href="arquivar_turma.php?id=<?= (int)$turma['id'] ?>"
                                           class="btn btn-secondary btn-sm"
                                           onclick="return confirm('Arquivar esta turma?')">Arquivar</a>
                                        <?php else: ?>
                                        <a href="desarquivar_turma.php?id=<?= (int)$turma['id'] ?>"
                                           class="btn btn-success btn-sm"
                                           onclick="return confirm('Reativar esta turma?')">Reativar</a>
                                        <?php endif; ?>
                                        <a href="excluir_turma.php?id=<?= (int)$turma['id'] ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Excluir esta turma permanentemente?')">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-warning">Nenhuma turma cadastrada.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>

<!-- ── Modais de edição (um por turma) ─────────────────────────── -->
<?php foreach ($turmas as $turma): ?>
<div class="modal fade" id="modalEditar<?= (int)$turma['id'] ?>" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="post" action="editar_turma.php">
            <div class="modal-header">
                <h5 class="modal-title">Editar Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" value="<?= (int)$turma['id'] ?>">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control"
                           value="<?= htmlspecialchars($turma['nome'], ENT_QUOTES) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" class="form-control"
                           value="<?= htmlspecialchars($turma['descricao'], ENT_QUOTES) ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div></div>
</div>
<?php endforeach; ?>

<!-- ── Modal nova turma ─────────────────────────────────────────── -->
<div class="modal fade" id="modalNovaTurma" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="post" action="inserir_turma.php">
            <div class="modal-header">
                <h5 class="modal-title">Nova Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <input type="text" name="descricao" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </div>
        </form>
    </div></div>
</div>

</body>
</html>
