<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'ranking';
$pageTitle  = 'Ranking';

// Parâmetros — default: mês/ano atual
$mes   = isset($_POST['mes'])   ? (int)$_POST['mes']   : (int)date('m');
$ano   = isset($_POST['ano'])   ? (int)$_POST['ano']   : (int)date('Y');
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
$turmaFiltro = isset($_POST['turma']) ? (int)$_POST['turma'] : 0;

// Monta query com filtro opcional de turma
$sql = "SELECT a.idAluno, a.nome AS NomeAluno, t.nome AS NomeTurma,
               COUNT(e.idL) AS LivrosLidos
        FROM emprestimo e
        JOIN alunos a ON e.idA = a.idAluno
        JOIN turmas t ON a.turma = t.id
        WHERE MONTH(e.dataInicial) = :mes
          AND YEAR(e.dataInicial)  = :ano";

if ($turmaFiltro > 0) {
    $sql .= " AND t.id = :turma";
}

$sql .= " GROUP BY a.idAluno, a.nome, t.nome
          ORDER BY LivrosLidos DESC";

if ($limit > 0) {
    $sql .= " LIMIT :limit";
}

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
$stmt->bindParam(':ano', $ano, PDO::PARAM_INT);
if ($turmaFiltro > 0) {
    $stmt->bindParam(':turma', $turmaFiltro, PDO::PARAM_INT);
}
if ($limit > 0) {
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
}
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Turmas para o select de filtro
$turmas = $pdo->query("SELECT id, nome FROM turmas WHERE status = 1 ORDER BY nome ASC")
              ->fetchAll(PDO::FETCH_ASSOC);

// Nomes dos meses
$nomesMeses = ['', 'Janeiro','Fevereiro','Março','Abril','Maio','Junho',
               'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

// Dados para Chart.js
$chartLabels = json_encode(array_map(fn($r) => $r['NomeAluno'], $resultado));
$chartData   = json_encode(array_map(fn($r) => (int)$r['LivrosLidos'], $resultado));
$chartTurmas = json_encode(array_map(fn($r) => $r['NomeTurma'], $resultado));

// Pódio (top 5) — ordem visual: 4º | 2º | 1º | 3º | 5º
$top5  = array_slice($resultado, 0, 5);
$podio = [];
if (isset($top5[3])) $podio[] = ['pos' => 4, 'dados' => $top5[3]];
if (isset($top5[1])) $podio[] = ['pos' => 2, 'dados' => $top5[1]];
if (isset($top5[0])) $podio[] = ['pos' => 1, 'dados' => $top5[0]];
if (isset($top5[2])) $podio[] = ['pos' => 3, 'dados' => $top5[2]];
if (isset($top5[4])) $podio[] = ['pos' => 5, 'dados' => $top5[4]];
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php include 'includes/head.php'; ?>
<style>
.podium-card   { border-radius: 16px; text-align: center; padding: 16px 12px; position: relative; }
.podium-1      { background: linear-gradient(135deg, #fff8e1, #ffe082); border: 2px solid #ffc107; }
.podium-2      { background: linear-gradient(135deg, #f5f5f5, #e0e0e0); border: 2px solid #bdbdbd; }
.podium-3      { background: linear-gradient(135deg, #fbe9e7, #ffccbc); border: 2px solid #ff8a65; }
.podium-4      { background: linear-gradient(135deg, #e3f2fd, #bbdefb); border: 2px solid #64b5f6; }
.podium-5      { background: linear-gradient(135deg, #f3e5f5, #e1bee7); border: 2px solid #ce93d8; }
.podium-medal  { font-size: 2.4rem; line-height: 1; }
.podium-pos    { font-size: 1.4rem; font-weight: 900; line-height: 1; }
.podium-nome   { font-size: .9rem; font-weight: 700; margin: 8px 0 2px; }
.podium-turma  { font-size: 0.75rem; color: #666; }
.podium-livros { font-size: 1.3rem; font-weight: 800; margin-top: 8px; }
.podium-livros small { font-size: 0.68rem; font-weight: 400; color: #666; display: block; }
.podium-wrap   { display: flex; align-items: flex-end; justify-content: center; gap: 10px; flex-wrap: wrap; }
.podium-item-1 { transform: translateY(-28px); }
.podium-item-2 { transform: translateY(-14px); }
.podium-item-3 { transform: translateY(-8px); }
.rank-badge-1  { background:#ffc107; color:#333; }
.rank-badge-2  { background:#bdbdbd; color:#333; }
.rank-badge-3  { background:#ff8a65; color:#fff; }
.rank-badge-4  { background:#64b5f6; color:#fff; }
.rank-badge-5  { background:#ce93d8; color:#fff; }
</style>
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
                    <li class="nav-item"><a href="#">Ranking</a></li>
                </ul>

                <!-- Filtros -->
                <div class="card card-round mb-3">
                    <div class="card-body py-3">
                        <form method="POST" class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label mb-1">Mês</label>
                                <select name="mes" class="form-select" required>
                                    <?php foreach ($nomesMeses as $i => $nm):
                                        if ($i === 0) continue; ?>
                                    <option value="<?= $i ?>" <?= $mes == $i ? 'selected' : '' ?>><?= $nm ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1">Ano</label>
                                <input type="number" name="ano" class="form-control"
                                       value="<?= $ano ?>" min="2020" max="<?= date('Y') + 1 ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1">Turma</label>
                                <select name="turma" class="form-select">
                                    <option value="0">Todas as turmas</option>
                                    <?php foreach ($turmas as $t): ?>
                                    <option value="<?= (int)$t['id'] ?>"
                                            <?= $turmaFiltro == $t['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['nome']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1">Exibir</label>
                                <select name="limit" class="form-select">
                                    <?php foreach ([5,10,15,20,0] as $opt): ?>
                                    <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>>
                                        <?= $opt === 0 ? 'Todos' : "$opt alunos" ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (count($resultado) === 0): ?>
                <div class="card card-round">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-book-reader fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Nenhum empréstimo encontrado para
                            <strong><?= $nomesMeses[$mes] ?>/<?= $ano ?></strong>
                            <?= $turmaFiltro > 0 ? 'nesta turma.' : '.' ?>
                        </p>
                    </div>
                </div>
                <?php else: ?>

                <!-- Pódio Top 5 -->
                <?php if (count($top5) >= 1): ?>
                <div class="card card-round mb-3">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-trophy text-warning me-1"></i>
                            Pódio — <?= $nomesMeses[$mes] ?>/<?= $ano ?>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="podium-wrap">
                            <?php
                            $medalhas   = [1 => '🥇', 2 => '🥈', 3 => '🥉', 4 => '4º', 5 => '5º'];
                            $cardClass  = [1 => 'podium-1', 2 => 'podium-2', 3 => 'podium-3', 4 => 'podium-4', 5 => 'podium-5'];
                            $itemClass  = [1 => 'podium-item-1', 2 => 'podium-item-2', 3 => 'podium-item-3', 4 => '', 5 => ''];
                            $maxWidth   = [1 => '200px', 2 => '180px', 3 => '170px', 4 => '155px', 5 => '155px'];
                            foreach ($podio as $p):
                                $pos = $p['pos'];
                                $row = $p['dados'];
                                $isMedal = $pos <= 3;
                            ?>
                            <div class="flex-fill <?= $itemClass[$pos] ?>" style="max-width:<?= $maxWidth[$pos] ?>;">
                                <div class="podium-card <?= $cardClass[$pos] ?>">
                                    <?php if ($isMedal): ?>
                                        <div class="podium-medal"><?= $medalhas[$pos] ?></div>
                                    <?php else: ?>
                                        <div class="podium-pos text-muted"><?= $medalhas[$pos] ?></div>
                                    <?php endif; ?>
                                    <div class="podium-nome"><?= htmlspecialchars($row['NomeAluno']) ?></div>
                                    <div class="podium-turma"><?= htmlspecialchars($row['NomeTurma']) ?></div>
                                    <div class="podium-livros">
                                        <?= (int)$row['LivrosLidos'] ?>
                                        <small><?= $row['LivrosLidos'] == 1 ? 'livro lido' : 'livros lidos' ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Gráfico + Tabela -->
                <div class="row">
                    <!-- Gráfico horizontal -->
                    <div class="col-md-5">
                        <div class="card card-round h-100">
                            <div class="card-header">
                                <div class="card-title">Livros por aluno</div>
                            </div>
                            <div class="card-body">
                                <div style="position:relative; height:<?= max(200, count($resultado) * 36) ?>px;">
                                    <canvas id="chartRanking"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Tabela completa -->
                    <div class="col-md-7">
                        <div class="card card-round h-100">
                            <div class="card-header">
                                <div class="card-head-row">
                                    <div class="card-title">Classificação Completa</div>
                                    <div class="card-tools">
                                        <span class="badge bg-primary"><?= count($resultado) ?> alunos</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:60px">Pos.</th>
                                                <th>Aluno</th>
                                                <th>Turma</th>
                                                <th style="width:110px">Livros</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($resultado as $pos => $row):
                                                $posReal = $pos + 1;
                                                $badgeCls = match($posReal) {
                                                    1 => 'rank-badge-1',
                                                    2 => 'rank-badge-2',
                                                    3 => 'rank-badge-3',
                                                    default => 'bg-light text-secondary border'
                                                };
                                            ?>
                                            <tr>
                                                <td class="text-center">
                                                    <span class="badge <?= $badgeCls ?> rounded-circle"
                                                          style="width:28px;height:28px;line-height:20px;font-size:.8rem;">
                                                        <?= $posReal ?>
                                                    </span>
                                                </td>
                                                <td class="fw-semibold align-middle">
                                                    <?= htmlspecialchars($row['NomeAluno']) ?>
                                                </td>
                                                <td class="text-muted align-middle">
                                                    <?= htmlspecialchars($row['NomeTurma']) ?>
                                                </td>
                                                <td class="align-middle">
                                                    <?php
                                                    $maxLivros = (int)$resultado[0]['LivrosLidos'];
                                                    $pct = $maxLivros > 0 ? round(((int)$row['LivrosLidos'] / $maxLivros) * 100) : 0;
                                                    ?>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1" style="height:8px;">
                                                            <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                                                        </div>
                                                        <span class="fw-bold" style="min-width:18px;"><?= (int)$row['LivrosLidos'] ?></span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
        <?php include 'includes/footer.php'; ?>

        <?php if (count($resultado) > 0): ?>
        <script>
        (function () {
            var ctx = document.getElementById('chartRanking');
            if (!ctx) return;

            var labels = <?= $chartLabels ?>;
            var data   = <?= $chartData ?>;
            var turmas = <?= $chartTurmas ?>;

            // Cores por posição
            var cores = data.map(function(_, i) {
                if (i === 0) return 'rgba(255,193,7,0.85)';
                if (i === 1) return 'rgba(189,189,189,0.85)';
                if (i === 2) return 'rgba(255,138,101,0.85)';
                return 'rgba(75,192,192,0.7)';
            });

            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Livros lidos',
                        data: data,
                        backgroundColor: cores,
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ' ' + ctx.parsed.x + ' livro(s)';
                                },
                                afterLabel: function(ctx) {
                                    return ' ' + turmas[ctx.dataIndex];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        },
                        y: {
                            ticks: {
                                font: { size: 11 },
                                callback: function(val, idx) {
                                    var name = this.getLabelForValue(val);
                                    return name.length > 14 ? name.slice(0, 13) + '…' : name;
                                }
                            }
                        }
                    }
                }
            });
        })();
        </script>
        <?php endif; ?>
</body>
</html>
