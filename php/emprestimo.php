<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'emprestimos';
$pageTitle  = 'Empréstimos';

$statusFiltro = '-1';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statusFiltro = $_POST['statusFiltro'] ?? '-1';
}

$stmt = $pdo->prepare(
    "SELECT e.idEmprestimo, e.dataInicial, e.dataFinal,
            l.titulo, a.nome, t.nome AS nomeTurma
     FROM Emprestimo e
     JOIN livros l ON e.idL = l.idLivro
     JOIN alunos a ON e.idA = a.idAluno
     JOIN turmas t ON a.turma = t.id
     WHERE e.status = :statusFiltro
     ORDER BY e.dataRecebimento DESC"
);
$stmt->bindValue(':statusFiltro', $statusFiltro);
$stmt->execute();
$emprestimos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <li class="nav-item"><a href="#">Empréstimos</a></li>
                </ul>
                <div class="d-flex justify-content-end mb-3">
                    <a href="selecionarAluno.php" class="btn btn-primary btn-round">
                        <i class="fas fa-plus"></i> Adicionar
                    </a>
                </div>
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-title">Filtrar por Status</div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                            <div class="d-flex flex-wrap gap-3 mb-3 align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="statusFiltro"
                                           id="emDebito" value="-1" <?= $statusFiltro === '-1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="emDebito">Em Débito</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="statusFiltro"
                                           id="emAndamento" value="0" <?= $statusFiltro === '0' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="emAndamento">Em Andamento</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="statusFiltro"
                                           id="recebido" value="1" <?= $statusFiltro === '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="recebido">Recebido</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                            </div>
                        </form>
                        <h5>Registros encontrados: <?= count($emprestimos) ?></h5>
                        <?php if (count($emprestimos) > 0): ?>
                        <table id="tabela-emprestimos" class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Data Inicial</th><th>Data Final</th><th>Título</th>
                                    <th>Aluno</th><th>Turma</th><th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($emprestimos as $emp): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($emp['dataInicial'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($emp['dataFinal'])) ?></td>
                                    <td><?= htmlspecialchars($emp['titulo']) ?></td>
                                    <td><?= htmlspecialchars($emp['nome']) ?></td>
                                    <td><?= htmlspecialchars($emp['nomeTurma']) ?></td>
                                    <td>
                                        <?php if ($statusFiltro !== '1'): ?>
                                        <a href="receberLivro.php?idEmprestimo=<?= (int)$emp['idEmprestimo'] ?>"
                                           class="btn btn-primary btn-sm"
                                           onclick="return confirm('Confirmar recebimento?')">Receber</a>
                                        <?php endif; ?>
                                        <a href="excluirEmprestimo.php?idEmprestimo=<?= (int)$emp['idEmprestimo'] ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Excluir este registro?')">Excluir</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p class="text-danger mt-3">Nenhum empréstimo encontrado com o status selecionado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
        <script>
        $(document).ready(function () {
            $("#tabela-emprestimos").DataTable({ pageLength: 10 });
        });
        </script>
</body>
</html>
