<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'livros';
$pageTitle  = 'Importar Livros em Lote';

// ── Download do modelo XLSX ───────────────────────────────────────
if (isset($_GET['download']) && $_GET['download'] === 'template') {
    $file = __DIR__ . '/exemplos/modelo_livros.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="modelo_livros.xlsx"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-cache');
    readfile($file);
    exit;
}

// ── Processamento do upload ───────────────────────────────────────
$resultado   = null;
$importados  = 0;
$duplicados  = 0;
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

        // Prepared statements
        $stmtIns = $pdo->prepare(
            "INSERT INTO livros (titulo, autor, editora, edicao, dataPublicacao, lugar, origem, categoria, ISBN, foto)
             VALUES (:titulo, :autor, :editora, :edicao, :dataPublicacao, :lugar, :origem, :categoria, :ISBN, '')"
        );
        // Verifica duplicata por ISBN (quando ISBN informado)
        $stmtDupISBN = $pdo->prepare(
            "SELECT COUNT(*) FROM livros WHERE ISBN = :isbn AND ISBN != ''"
        );
        // Verifica duplicata por título + autor (quando ISBN vazio)
        $stmtDupTit = $pdo->prepare(
            "SELECT COUNT(*) FROM livros WHERE LOWER(titulo) = LOWER(:titulo) AND LOWER(autor) = LOWER(:autor)"
        );

        // Rastreia o que já foi inserido neste lote para evitar duplicatas internas ao CSV
        $vistos = []; // 'isbn:VALOR' ou 'tit:titulo|autor'

        while (($cols = fgetcsv($handle, 0, ',')) !== false) {
            $linha_num++;
            if ($linha_num === 1) continue; // pula cabeçalho

            // Normaliza: garante ao menos 9 elementos
            $cols = array_pad($cols, 9, '');
            $cols = array_map('trim', $cols);

            $titulo = $cols[0];
            $autor  = $cols[1];
            $isbn   = $cols[8];

            // ── Validação obrigatória ─────────────────────────────
            if ($titulo === '' && $autor === '') {
                $ignorados[] = "Linha $linha_num: título e autor vazios — linha ignorada";
                continue;
            }
            if ($titulo === '') {
                $ignorados[] = "Linha $linha_num: título vazio (autor: $autor)";
                continue;
            }
            if ($autor === '') {
                $ignorados[] = "Linha $linha_num: autor vazio (título: $titulo)";
                continue;
            }

            // ── Detecção de duplicata no próprio arquivo ──────────
            $chaveISBN = $isbn !== '' ? 'isbn:' . strtolower($isbn) : null;
            $chaveTit  = 'tit:' . strtolower($titulo) . '|' . strtolower($autor);

            if ($chaveISBN && isset($vistos[$chaveISBN])) {
                $ignorados[] = "Linha $linha_num ($titulo): duplicada no arquivo — ISBN $isbn já apareceu";
                $duplicados++;
                continue;
            }
            if (isset($vistos[$chaveTit])) {
                $ignorados[] = "Linha $linha_num ($titulo): duplicada no arquivo — mesmo título e autor";
                $duplicados++;
                continue;
            }

            // ── Detecção de duplicata no banco ────────────────────
            if ($isbn !== '') {
                // ISBN preenchido: ISBN é identificador único
                $stmtDupISBN->execute([':isbn' => $isbn]);
                if ($stmtDupISBN->fetchColumn() > 0) {
                    $ignorados[] = "Linha $linha_num ($titulo): já existe no banco — ISBN $isbn";
                    $duplicados++;
                    continue;
                }
            } else {
                // Sem ISBN: compara título + autor (case-insensitive)
                $stmtDupTit->execute([':titulo' => $titulo, ':autor' => $autor]);
                if ($stmtDupTit->fetchColumn() > 0) {
                    $ignorados[] = "Linha $linha_num ($titulo): já existe no banco — mesmo título e autor";
                    $duplicados++;
                    continue;
                }
            }

            // ── Inserção ──────────────────────────────────────────
            try {
                $stmtIns->execute([
                    ':titulo'         => $titulo,
                    ':autor'          => $autor,
                    ':editora'        => $cols[2],
                    ':edicao'         => $cols[3],
                    ':dataPublicacao' => $cols[4],
                    ':lugar'          => $cols[5],
                    ':origem'         => $cols[6],
                    ':categoria'      => $cols[7],
                    ':ISBN'           => $isbn,
                ]);
                $importados++;
                // Marca como visto neste lote
                if ($chaveISBN) $vistos[$chaveISBN] = true;
                $vistos[$chaveTit] = true;
            } catch (PDOException $e) {
                $ignorados[] = "Linha $linha_num ($titulo): erro no banco — " . $e->getMessage();
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
                    <li class="nav-item"><a href="../livros.php">Livros</a></li>
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
                            <div class="col-md-3">
                                <div class="p-3 rounded bg-light">
                                    <div class="fs-1 fw-bold text-success"><?= $importados ?></div>
                                    <div class="text-muted">livro<?= $importados !== 1 ? 's' : '' ?> importado<?= $importados !== 1 ? 's' : '' ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded bg-light">
                                    <div class="fs-1 fw-bold text-danger"><?= $duplicados ?></div>
                                    <div class="text-muted">duplicada<?= $duplicados !== 1 ? 's' : '' ?> ignorada<?= $duplicados !== 1 ? 's' : '' ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 rounded bg-light">
                                    <div class="fs-1 fw-bold text-warning"><?= count($ignorados) - $duplicados ?></div>
                                    <div class="text-muted">outro<?= (count($ignorados) - $duplicados) !== 1 ? 's' : '' ?> erro<?= (count($ignorados) - $duplicados) !== 1 ? 's' : '' ?></div>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-center justify-content-center">
                                <a href="../livros.php" class="btn btn-primary">
                                    <i class="fas fa-book me-1"></i> Ver Livros
                                </a>
                            </div>
                        </div>
                        <?php if (count($ignorados) > 0): ?>
                        <hr>
                        <p class="fw-semibold mb-2">Linhas ignoradas:</p>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($ignorados as $msg):
                                $isDup = str_contains($msg, 'duplicada') || str_contains($msg, 'já existe'); ?>
                            <li class="list-group-item py-1 <?= $isDup ? 'list-group-item-info' : 'list-group-item-warning' ?>">
                                <i class="fas fa-<?= $isDup ? 'copy text-info' : 'exclamation-circle text-warning' ?> me-1"></i>
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
                                Importar Livros em Lote
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
                                    <p class="text-muted small">Abra o arquivo no Excel ou LibreOffice e preencha os livros. Não altere o cabeçalho (primeira linha).</p>
                                    <p class="mt-2 small"><span class="badge bg-danger">*</span> titulo e autor são obrigatórios</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card h-100 border-0 bg-light text-center p-3">
                                    <div class="fs-1 mb-2">🔄</div>
                                    <div class="badge bg-warning text-dark mb-2">Passo 3</div>
                                    <p class="mb-2 fw-semibold">Converta para CSV</p>
                                    <p class="text-muted small mb-3">Acesse o site de conversão e envie seu arquivo .xlsx. O site converte gratuitamente para .csv.</p>
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
                                    <p class="text-muted small">Faça o upload do arquivo <strong>.csv</strong> gerado pelo Convertio no campo abaixo e clique em Importar.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Exemplo visual das colunas -->
                        <div class="alert alert-secondary py-2 px-3 mb-4" style="font-size:.82rem;">
                            <strong>Colunas do modelo:</strong>
                            <code>titulo*, autor*, editora, edicao, dataPublicacao, lugar, origem, categoria, ISBN</code>
                            &nbsp;— campos com <span class="badge bg-danger">*</span> são obrigatórios
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
                                <a href="../livros.php" class="btn btn-secondary btn-round">
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
