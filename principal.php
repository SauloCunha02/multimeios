<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: index.php');
    exit;
}

include 'bd.php';

$basePath   = '';
$activePage = 'inicio';
$pageTitle  = 'Início';

/* ── Marca empréstimos vencidos ────────────────────────────────── */
$pdo->prepare("UPDATE Emprestimo SET status = -1
               WHERE dataFinal < CURDATE() AND status = '0'")
    ->execute();

/* ── Cards de resumo ───────────────────────────────────────────── */
$totalLivros       = (int)$pdo->query("SELECT COUNT(*) FROM livros")->fetchColumn();
$totalAlunosAtivos = (int)$pdo->query("SELECT COUNT(*) FROM alunos WHERE status = 1")->fetchColumn();
$totalAtivos       = (int)$pdo->query("SELECT COUNT(*) FROM Emprestimo WHERE status = 0")->fetchColumn();
$totalAtrasos      = (int)$pdo->query("SELECT COUNT(*) FROM Emprestimo WHERE status = -1")->fetchColumn();
$totalTurmas       = (int)$pdo->query("SELECT COUNT(*) FROM turmas WHERE status = 1")->fetchColumn();
$devolvidosMes     = (int)$pdo->query(
    "SELECT COUNT(*) FROM Emprestimo
     WHERE status = 1
       AND MONTH(dataRecebimento) = MONTH(CURDATE())
       AND YEAR(dataRecebimento)  = YEAR(CURDATE())"
)->fetchColumn();

/* ── Empréstimos por mês (últimos 6 meses) ─────────────────────── */
$stmt = $pdo->query(
    "SELECT DATE_FORMAT(dataInicial,'%Y-%m') AS mes_key,
            DATE_FORMAT(dataInicial,'%b/%Y')  AS mes_label,
            COUNT(*) AS total
     FROM Emprestimo
     WHERE dataInicial >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), '%Y-%m-01')
     GROUP BY mes_key, mes_label
     ORDER BY mes_key ASC"
);
$dbMeses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Garante os 6 meses mesmo sem dados
$mesesLabels = [];
$mesesTotais = [];
$dbMesesMap  = array_column($dbMeses, 'total', 'mes_key');
for ($i = 5; $i >= 0; $i--) {
    $key   = date('Y-m', strtotime("-$i month"));
    $label = date('M/Y', strtotime("-$i month"));
    $mesesLabels[] = $label;
    $mesesTotais[] = (int)($dbMesesMap[$key] ?? 0);
}
$jsLabels = json_encode($mesesLabels);
$jsTotais = json_encode($mesesTotais);

/* ── Top 5 livros mais emprestados ─────────────────────────────── */
$top5Livros = $pdo->query(
    "SELECT l.titulo, l.autor, COUNT(e.idEmprestimo) AS total
     FROM Emprestimo e
     JOIN livros l ON e.idL = l.idLivro
     GROUP BY e.idL
     ORDER BY total DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

/* ── Top 5 alunos leitores do mês ──────────────────────────────── */
$top5Alunos = $pdo->query(
    "SELECT a.nome, t.nome AS turma, COUNT(e.idEmprestimo) AS total
     FROM Emprestimo e
     JOIN alunos a ON e.idA = a.idAluno
     JOIN turmas t ON a.turma = t.id
     WHERE MONTH(e.dataInicial) = MONTH(CURDATE())
       AND YEAR(e.dataInicial)  = YEAR(CURDATE())
     GROUP BY e.idA
     ORDER BY total DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

/* ── Empréstimos em atraso ──────────────────────────────────────── */
$empAtrasados = $pdo->query(
    "SELECT e.idEmprestimo, e.dataInicial, e.dataFinal,
            l.titulo, a.nome, t.nome AS nomeTurma
     FROM Emprestimo e
     JOIN livros l ON e.idL = l.idLivro
     JOIN alunos a ON e.idA = a.idAluno
     JOIN turmas t ON a.turma = t.id
     WHERE e.status = -1
     ORDER BY e.dataFinal ASC"
)->fetchAll(PDO::FETCH_ASSOC);

/* ── Empréstimos recentes (últimos 5 ativos) ───────────────────── */
$recentes = $pdo->query(
    "SELECT e.dataInicial, l.titulo, a.nome, t.nome AS turma, e.dataFinal
     FROM Emprestimo e
     JOIN livros l ON e.idL = l.idLivro
     JOIN alunos a ON e.idA = a.idAluno
     JOIN turmas t ON a.turma = t.id
     WHERE e.status = 0
     ORDER BY e.dataInicial DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

/* ── Taxa de devolução ──────────────────────────────────────────── */
$totalGeral    = (int)$pdo->query("SELECT COUNT(*) FROM Emprestimo")->fetchColumn();
$totalDevolvidos = (int)$pdo->query("SELECT COUNT(*) FROM Emprestimo WHERE status = 1")->fetchColumn();
$taxaDevolucao = $totalGeral > 0 ? round(($totalDevolvidos / $totalGeral) * 100) : 0;
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
                    <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Início</a></li>
                </ul>

                <!-- ── Linha 1: Cards de resumo ── -->
                <div class="row">
                    <!-- Livros -->
                    <div class="col-sm-6 col-md-4 col-xl-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Livros</p>
                                            <h4 class="card-title"><?= $totalLivros ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Alunos ativos -->
                    <div class="col-sm-6 col-md-4 col-xl-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-info bubble-shadow-small">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Alunos Ativos</p>
                                            <h4 class="card-title"><?= $totalAlunosAtivos ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Empréstimos ativos -->
                    <div class="col-sm-6 col-md-4 col-xl-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-success bubble-shadow-small">
                                            <i class="fas fa-hand-holding-heart"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Em Empréstimo</p>
                                            <h4 class="card-title"><?= $totalAtivos ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Devolvidos no mês -->
                    <div class="col-sm-6 col-md-4 col-xl-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                            <i class="fas fa-undo-alt"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Devolvidos (mês)</p>
                                            <h4 class="card-title"><?= $devolvidosMes ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Em atraso -->
                    <div class="col-sm-6 col-md-4 col-xl-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center btn-danger bubble-shadow-small">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Em Atraso</p>
                                            <h4 class="card-title"><?= $totalAtrasos ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Turmas ativas -->
                    <div class="col-sm-6 col-md-4 col-xl-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Turmas Ativas</p>
                                            <h4 class="card-title"><?= $totalTurmas ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /row cards -->

                <!-- ── Linha 2: Gráfico + Top livros ── -->
                <div class="row mt-2">
                    <!-- Gráfico empréstimos por mês -->
                    <div class="col-md-7">
                        <div class="card card-round">
                            <div class="card-header">
                                <div class="card-head-row">
                                    <div class="card-title">Empréstimos — últimos 6 meses</div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="chart-container" style="position:relative; height:240px;">
                                    <canvas id="chartEmprestimos"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Top 5 livros + taxa devolução -->
                    <div class="col-md-5">
                        <!-- Taxa de devolução -->
                        <div class="card card-round mb-3">
                            <div class="card-header">
                                <div class="card-title">Taxa de Devolução</div>
                            </div>
                            <div class="card-body py-3">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-auto fw-semibold"><?= $taxaDevolucao ?>% devolvidos</span>
                                    <span class="text-muted small"><?= $totalDevolvidos ?>/<?= $totalGeral ?></span>
                                </div>
                                <div class="progress" style="height:12px; border-radius:8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width:<?= $taxaDevolucao ?>%"
                                         aria-valuenow="<?= $taxaDevolucao ?>"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Top 5 livros -->
                        <div class="card card-round">
                            <div class="card-header">
                                <div class="card-title">Top 5 Livros Mais Emprestados</div>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($top5Livros): ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($top5Livros as $idx => $livro): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                        <span>
                                            <span class="badge bg-primary me-2"><?= $idx + 1 ?></span>
                                            <span class="fw-semibold"><?= htmlspecialchars($livro['titulo']) ?></span>
                                            <br>
                                            <small class="text-muted ms-4"><?= htmlspecialchars($livro['autor']) ?></small>
                                        </span>
                                        <span class="badge bg-info rounded-pill"><?= $livro['total'] ?>x</span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php else: ?>
                                <p class="text-muted p-3 mb-0">Nenhum empréstimo registrado.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /row linha 2 -->

                <!-- ── Linha 3: Top alunos + Empréstimos recentes ── -->
                <div class="row mt-2">
                    <!-- Top 5 alunos leitores do mês -->
                    <div class="col-md-5">
                        <div class="card card-round h-100">
                            <div class="card-header">
                                <div class="card-head-row">
                                    <div class="card-title">
                                        <i class="fas fa-trophy text-warning me-1"></i>
                                        Top Leitores — <?= date('F/Y') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($top5Alunos): ?>
                                <ul class="list-group list-group-flush">
                                    <?php
                                    $medalhas = ['🥇','🥈','🥉','4º','5º'];
                                    foreach ($top5Alunos as $idx => $aluno): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                        <span>
                                            <span class="me-2 fs-5"><?= $medalhas[$idx] ?></span>
                                            <span class="fw-semibold"><?= htmlspecialchars($aluno['nome']) ?></span>
                                            <br>
                                            <small class="text-muted ms-4"><?= htmlspecialchars($aluno['turma']) ?></small>
                                        </span>
                                        <span class="badge bg-success rounded-pill">
                                            <?= $aluno['total'] ?> <?= $aluno['total'] == 1 ? 'livro' : 'livros' ?>
                                        </span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php else: ?>
                                <p class="text-muted p-3 mb-0">Nenhum empréstimo neste mês ainda.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Empréstimos recentes -->
                    <div class="col-md-7">
                        <div class="card card-round h-100">
                            <div class="card-header">
                                <div class="card-head-row card-tools-still-right">
                                    <div class="card-title">Empréstimos Recentes</div>
                                    <div class="card-tools">
                                        <a href="php/emprestimo.php" class="btn btn-label-info btn-round btn-sm">Ver todos</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($recentes): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Aluno</th>
                                                <th>Livro</th>
                                                <th>Saída</th>
                                                <th>Devolução</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentes as $r): ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-semibold"><?= htmlspecialchars($r['nome']) ?></span>
                                                    <br><small class="text-muted"><?= htmlspecialchars($r['turma']) ?></small>
                                                </td>
                                                <td class="text-truncate" style="max-width:140px;">
                                                    <?= htmlspecialchars($r['titulo']) ?>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($r['dataInicial'])) ?></td>
                                                <td>
                                                    <?php
                                                    $devDate = strtotime($r['dataFinal']);
                                                    $hoje    = strtotime('today');
                                                    $diff    = (int)(($devDate - $hoje) / 86400);
                                                    $cls     = $diff <= 2 ? 'text-danger fw-bold' : 'text-success';
                                                    ?>
                                                    <span class="<?= $cls ?>"><?= date('d/m/Y', $devDate) ?></span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <p class="text-muted p-3 mb-0">Nenhum empréstimo ativo no momento.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /row linha 3 -->

                <!-- ── Linha 4: Empréstimos em atraso ── -->
                <?php if ($totalAtrasos > 0): ?>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card card-round border-danger">
                            <div class="card-header bg-danger text-white">
                                <div class="card-head-row card-tools-still-right">
                                    <div class="card-title text-white">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Empréstimos em Atraso (<?= $totalAtrasos ?>)
                                    </div>
                                    <div class="card-tools">
                                        <a href="php/emprestimo.php" class="btn btn-light btn-round btn-sm">Ver todos</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Aluno</th>
                                                <th>Turma</th>
                                                <th>Livro</th>
                                                <th>Prazo</th>
                                                <th>Dias em atraso</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($empAtrasados as $i => $emp):
                                                $diasAtraso = (int)((time() - strtotime($emp['dataFinal'])) / 86400);
                                            ?>
                                            <tr>
                                                <td class="fw-semibold"><?= htmlspecialchars($emp['nome']) ?></td>
                                                <td><?= htmlspecialchars($emp['nomeTurma']) ?></td>
                                                <td><?= htmlspecialchars($emp['titulo']) ?></td>
                                                <td class="text-danger"><?= date('d/m/Y', strtotime($emp['dataFinal'])) ?></td>
                                                <td>
                                                    <span class="badge bg-danger">
                                                        <?= $diasAtraso ?> <?= $diasAtraso == 1 ? 'dia' : 'dias' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-success btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalReceber<?= $i ?>">
                                                        <i class="fas fa-check me-1"></i>Receber
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /row atrasos -->
                <?php endif; ?>

            </div><!-- /.page-inner -->
        </div><!-- /.container -->

        <?php include 'php/includes/footer.php'; ?>

        <!-- Modais de recebimento -->
        <?php foreach ($empAtrasados as $i => $emp): ?>
        <div class="modal fade" id="modalReceber<?= $i ?>" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar recebimento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong><?= htmlspecialchars($emp['titulo']) ?></strong></p>
                        <p class="mb-0">Aluno: <?= htmlspecialchars($emp['nome']) ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <a href="php/receberLivro.php?idEmprestimo=<?= (int)$emp['idEmprestimo'] ?>"
                           class="btn btn-success">Confirmar</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Chart.js: empréstimos por mês -->
        <script>
        (function () {
            var ctx = document.getElementById('chartEmprestimos');
            if (!ctx) return;
            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: <?= $jsLabels ?>,
                    datasets: [{
                        label: 'Empréstimos',
                        data: <?= $jsTotais ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        borderColor:     'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ' ' + ctx.parsed.y + ' empréstimo(s)';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        })();
        </script>

</body>
</html>
