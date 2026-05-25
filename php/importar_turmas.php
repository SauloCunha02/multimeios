<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'turmas';
$pageTitle  = 'Importar Turmas em Lote';

// ── Download do modelo XLSX ───────────────────────────────────────
if (isset($_GET['download']) && $_GET['download'] === 'template') {
    $file = __DIR__ . '/exemplos/modelo_turmas.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="modelo_turmas.xlsx"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-cache');
    readfile($file);
    exit;
}

// ── Processamento do upload ───────────────────────────────────────
$resultado   = null;
$importados  = 0;
$ignorados   = [];
$erroArquivo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['planilha'])) {
    $arquivo  = $_FILES['planilha']['tmp_name'];
    $extensao = strtolower(pathinfo($_FILES['planilha']['name'], PATHINFO_EXTENSION));

    if ($extensao !== 'csv') {
        $erroArquivo = 'O arquivo deve ter extensão .csv';
    } elseif (!$arquivo || !file_exists($arquivo)) {
        $erroArquivo = 'Falha ao receber o arquivo.';
    } else {
        $handle = fopen($arquivo, 'r');
        // Remove BOM se presente
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") rewind($handle);

        $linha_num = 0;
        $stmtIns   = $pdo->prepare(
            "INSERT INTO turmas (nome, descricao, status) VALUES (:nome, :descricao, 1)"
        );

        while (($cols = fgetcsv($handle, 0, ',')) !== false) {
            $linha_num++;
            if ($linha_num === 1) continue; // pula cabeçalho

            $cols = array_pad($cols, 2, '');
            $cols = array_map('trim', $cols);

            $nome      = $cols[0];
            $descricao = $cols[1];

            if ($nome === '') {
                $ignorados[] = "Linha $linha_num: nome da turma vazio";
                continue;
            }

            try {
                $stmtIns->execute([
                    ':nome'      => $nome,
                    ':descricao' => $descricao ?: null,
                ]);
                $importados++;
            } catch (PDOException $e) {
                $ignorados[] = "Linha $linha_num ($nome): erro no banco — " . $e->getMessage();
            }
        }
        fclose($handle);
        $resultado = true;
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
                    <li class="nav-item"><a href="turmas.php">Turmas</a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="#">Importar em Lote</a></li>
                </ul>

                <!-- Resultado da importação -->
                <?php if ($resultado): ?>
                <div class="card card-round mb-3
                    <?= $importados > 0 ? 'border-success' : 'border-warning' ?>">
                    <div class="card-header <?= $importados > 0 ? 'bg-success text-white' : 'bg-warning' ?>">
                        <div class="card-title <?= $importados > 0 ? 'text-white' : '' ?>">
                            <i class="fas fa-<?= $importados > 0 ? 'check-circle' : 'exclamation-triangle' ?> me-1"></i>
                            Resultado da Importação
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light">
                                    <div class="fs-1 fw-bold text-success"><?= $importados ?></div>
                                    <div class="text-muted">turma<?= $importados !== 1 ? 's' : '' ?> importada<?= $importados !== 1 ? 's' : '' ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded bg-light">
                                    <div class="fs-1 fw-bold text-warning"><?= count($ignorados) ?></div>
                                    <div class="text-muted">linha<?= count($ignorados) !== 1 ? 's' : '' ?> ignorada<?= count($ignorados) !== 1 ? 's' : '' ?></div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center justify-content-center">
                                <a href="turmas.php" class="btn btn-primary">
                                    <i class="fas fa-chalkboard-teacher me-1"></i> Ver Turmas
                                </a>
                            </div>
                        </div>
                        <?php if (count($ignorados) > 0): ?>
                        <hr>
                        <p class="fw-semibold mb-2">Linhas ignoradas:</p>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($ignorados as $msg): ?>
                            <li class="list-group-item list-group-item-warning py-1">
                                <i class="fas fa-minus-circle me-1 text-warning"></i>
                                <?= htmlspecialchars($msg) ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($erroArquivo): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-1"></i> <?= htmlspecialchars($erroArquivo) ?>
                </div>
                <?php endif; ?>

                <!-- Formulário de upload -->
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title">
                                <i class="fas fa-file-excel text-success me-1"></i>
                                Importar Turmas em Lote
                            </div>
                            <div class="card-tools">
                                <a href="?download=template" class="btn btn-success btn-sm btn-round">
                                    <i class="fas fa-download me-1"></i> Baixar Modelo Excel (.xlsx)
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Passo a passo -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card h-100 border-0 bg-light text-center p-3">
                                    <div class="fs-1 mb-2">📥</div>
                                    <div class="badge bg-primary mb-2">Passo 1</div>
                                    <p class="mb-2 fw-semibold">Baixe o modelo</p>
                                    <p class="text-muted small mb-3">Clique em <strong>"Baixar Modelo Excel"</strong> acima para obter a planilha pronta.</p>
                                    <a href="?download=template" class="btn btn-success btn-sm btn-round mt-auto">
                                        <i class="fas fa-download me-1"></i> Baixar .xlsx
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 bg-light text-center p-3">
                                    <div class="fs-1 mb-2">✏️</div>
                                    <div class="badge bg-primary mb-2">Passo 2</div>
                                    <p class="mb-2 fw-semibold">Preencha no Excel</p>
                                    <p class="text-muted small">Abra no Excel ou LibreOffice e preencha as turmas. Não altere a primeira linha (cabeçalho).</p>
                                    <p class="mt-2 small"><span class="badge bg-danger">*</span> nome é obrigatório</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 bg-light text-center p-3">
                                    <div class="fs-1 mb-2">🔄</div>
                                    <div class="badge bg-warning text-dark mb-2">Passo 3</div>
                                    <p class="mb-2 fw-semibold">Converta para CSV</p>
                                    <p class="text-muted small mb-3">Acesse o site de conversão e envie seu .xlsx. O site converte gratuitamente para .csv.</p>
                                    <a href="https://convertio.co/pt/xls-csv/" target="_blank" class="btn btn-warning btn-sm btn-round mt-auto">
                                        <i class="fas fa-external-link-alt me-1"></i> Abrir Convertio
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 bg-light text-center p-3">
                                    <div class="fs-1 mb-2">📤</div>
                                    <div class="badge bg-success mb-2">Passo 4</div>
                                    <p class="mb-2 fw-semibold">Importe aqui</p>
                                    <p class="text-muted small">Faça o upload do arquivo <strong>.csv</strong> gerado pelo Convertio no campo abaixo.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-secondary py-2 px-3 mb-4" style="font-size:.82rem;">
                            <strong>Colunas do modelo:</strong>
                            <code>nome*, descricao</code>
                            &nbsp;— campo com <span class="badge bg-danger">*</span> é obrigatório. Turmas importadas ficam com status <strong>Ativa</strong>.
                        </div>

                        <!-- Upload -->
                        <form method="POST" enctype="multipart/form-data" class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">
                                    Arquivo CSV convertido <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" name="planilha"
                                       accept=".csv" required>
                                <div class="form-text">
                                    Apenas arquivos <code>.csv</code> (converta seu .xlsx em
                                    <a href="https://convertio.co/pt/xls-csv/" target="_blank">convertio.co</a>)
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-success btn-round flex-grow-1">
                                    <i class="fas fa-upload me-1"></i> Importar
                                </button>
                                <a href="turmas.php" class="btn btn-secondary btn-round">
                                    Voltar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
</body>
</html>
