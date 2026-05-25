<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}
include '../bd.php';

$basePath   = '../';
$activePage = 'turmas';
$pageTitle  = 'Gerenciar Turma';

// ── Download do modelo XLSX de alunos (não precisa de idTurma) ───
if (isset($_GET['download']) && $_GET['download'] === 'alunos') {
    $file = __DIR__ . '/exemplos/modelo_alunos.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="modelo_alunos.xlsx"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-cache');
    readfile($file);
    exit;
}

if (!isset($_GET['idTurma'])) {
    header('Location: turmas.php');
    exit;
}

$idTurma = (int)$_GET['idTurma'];

$stmt = $pdo->prepare("SELECT * FROM turmas WHERE id = :idTurma");
$stmt->bindParam(':idTurma', $idTurma, PDO::PARAM_INT);
$stmt->execute();
$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma) {
    header('Location: turmas.php');
    exit;
}

// ── Upload CSV de alunos ──────────────────────────────────────────
if (isset($_POST['submitPlanilha']) && isset($_FILES['planilha'])) {
    $arquivo  = $_FILES['planilha']['tmp_name'];
    $extensao = strtolower(pathinfo($_FILES['planilha']['name'], PATHINFO_EXTENSION));

    if ($extensao !== 'csv') {
        $erroUpload = 'Arquivo deve ser .csv';
    } elseif (!$arquivo || !file_exists($arquivo)) {
        $erroUpload = 'Falha ao receber o arquivo.';
    } else {
        try {
            $handle    = fopen($arquivo, 'r');
            // Remove BOM UTF-8 se presente
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") rewind($handle);

            $stmtIns   = $pdo->prepare(
                "INSERT INTO alunos (matricula, nome, turma, status)
                 VALUES (:matricula, :nome, :turma, 1)"
            );
            $linha_num = 0;

            while (($linha = fgetcsv($handle, 0, ',')) !== false) {
                $linha_num++;
                if ($linha_num === 1) continue; // pula cabeçalho

                $linha = array_pad(array_map('trim', $linha), 2, '');
                $nome  = $linha[1]; // coluna B = nome
                if ($nome === '') continue;

                $stmtIns->execute([
                    ':matricula' => $linha[0] ?: null,
                    ':nome'      => $nome,
                    ':turma'     => $idTurma,
                ]);
            }
            fclose($handle);
            header('Location: alterar_turma.php?idTurma=' . $idTurma . '&msg=planilha_ok');
            exit;
        } catch (Exception $e) {
            $erroUpload = 'Erro ao processar CSV: ' . $e->getMessage();
        }
    }
}

// ── Editar aluno ──────────────────────────────────────────────────
if (isset($_POST['editarAluno'])) {
    $stmtEdit = $pdo->prepare(
        "UPDATE alunos SET nome = :nome, matricula = :matricula WHERE idAluno = :idAluno"
    );
    $stmtEdit->execute([
        ':nome'      => trim($_POST['nome']),
        ':matricula' => trim($_POST['matricula']),
        ':idAluno'   => (int)$_POST['idAluno'],
    ]);
    header('Location: alterar_turma.php?idTurma=' . $idTurma . '&msg=editado');
    exit;
}

// ── Excluir aluno ─────────────────────────────────────────────────
if (isset($_POST['excluirAluno'])) {
    $stmtDel = $pdo->prepare("DELETE FROM alunos WHERE idAluno = :idAluno");
    $stmtDel->bindParam(':idAluno', $_POST['idAluno'], PDO::PARAM_INT);
    $stmtDel->execute();
    header('Location: alterar_turma.php?idTurma=' . $idTurma . '&msg=excluido');
    exit;
}

// ── Lista de alunos ───────────────────────────────────────────────
$stmtAlunos = $pdo->prepare("SELECT * FROM alunos WHERE turma = :idTurma ORDER BY nome ASC");
$stmtAlunos->bindParam(':idTurma', $idTurma, PDO::PARAM_INT);
$stmtAlunos->execute();
$alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);
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
                    <li class="nav-item"><a href="#"><?= htmlspecialchars($turma['nome']) ?></a></li>
                </ul>

                <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Operação realizada com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                <?php if (isset($erroUpload)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erroUpload) ?></div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="turmas.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <button type="button" class="btn btn-primary"
                            data-bs-toggle="modal" data-bs-target="#modalCadastrarAluno">
                        <i class="fas fa-user-plus"></i> Adicionar Aluno
                    </button>
                    <button type="button" class="btn btn-info"
                            data-bs-toggle="modal" data-bs-target="#modalPlanilha">
                        <i class="fas fa-file-csv"></i> Importar Planilha
                    </button>
                </div>

                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-title">
                            Turma: <?= htmlspecialchars($turma['nome']) ?>
                            <span class="badge <?= $turma['status'] == 1 ? 'bg-success' : 'bg-secondary' ?> ms-2">
                                <?= $turma['status'] == 1 ? 'Ativa' : 'Arquivada' ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5>Alunos (<?= count($alunos) ?>)</h5>
                        <?php if (count($alunos) > 0): ?>
                        <ul class="list-group mt-2">
                            <?php foreach ($alunos as $aluno): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <strong><?= htmlspecialchars($aluno['nome']) ?></strong>
                                    <?php if ($aluno['matricula']): ?>
                                        &mdash; Matrícula: <?= htmlspecialchars($aluno['matricula']) ?>
                                    <?php endif; ?>
                                    <span class="badge <?= $aluno['status'] == 1 ? 'bg-success' : 'bg-secondary' ?> ms-1">
                                        <?= $aluno['status'] == 1 ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </span>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editarAlunoModal"
                                            data-id="<?= (int)$aluno['idAluno'] ?>"
                                            data-nome="<?= htmlspecialchars($aluno['nome'], ENT_QUOTES) ?>"
                                            data-matricula="<?= htmlspecialchars($aluno['matricula'] ?? '', ENT_QUOTES) ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteModal"
                                            data-id="<?= (int)$aluno['idAluno'] ?>"
                                            data-nome="<?= htmlspecialchars($aluno['nome'], ENT_QUOTES) ?>">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p class="text-warning mt-3">Nenhum aluno cadastrado nesta turma.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>

        <!-- ═══ MODAIS — devem ficar dentro do <body>, após os scripts do footer ═══ -->

        <!-- Modal Editar Aluno -->
        <div class="modal fade" id="editarAlunoModal" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Aluno</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="idAluno" id="editIdAluno">
                        <div class="mb-3">
                            <label class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editNomeAluno" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="editMatricula" name="matricula">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="editarAluno" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div></div>
        </div>

        <!-- Modal Confirmar Exclusão -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza que deseja excluir o aluno <strong id="nomeAlunoDelete"></strong>?</p>
                        <input type="hidden" name="idAluno" id="idAlunoDelete">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="excluirAluno" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Excluir
                        </button>
                    </div>
                </form>
            </div></div>
        </div>

        <!-- Modal Cadastrar Aluno -->
        <div class="modal fade" id="modalCadastrarAluno" tabindex="-1">
            <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Aluno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCadastrarAluno">
                        <input type="hidden" name="idTurma" value="<?= $idTurma ?>">
                        <div class="mb-3">
                            <label class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Matrícula (opcional)</label>
                            <input type="text" class="form-control" name="matricula">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha (opcional)</label>
                            <input type="password" class="form-control" name="senha">
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Cadastrar</button>
                        </div>
                    </form>
                    <div id="mensagemCadastro" class="mt-2 text-center"></div>
                </div>
            </div></div>
        </div>

        <!-- Modal Importar Planilha CSV -->
        <div class="modal fade" id="modalPlanilha" tabindex="-1">
            <div class="modal-dialog modal-lg"><div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-file-excel text-success me-1"></i> Importar Alunos por Planilha</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3 text-center">
                            <div class="col-3">
                                <div class="bg-light rounded p-2">
                                    <div class="fs-3">📥</div>
                                    <span class="badge bg-primary mb-1 d-block">Passo 1</span>
                                    <p class="small mb-2 fw-semibold">Baixe o modelo</p>
                                    <a href="?download=alunos"
                                       class="btn btn-success btn-sm w-100">
                                        <i class="fas fa-download me-1"></i>.xlsx
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="bg-light rounded p-2 h-100">
                                    <div class="fs-3">✏️</div>
                                    <span class="badge bg-primary mb-1 d-block">Passo 2</span>
                                    <p class="small mb-0 fw-semibold">Preencha no Excel</p>
                                    <p class="small text-muted mt-1">Col. A: <code>matricula</code><br>Col. B: <code>nome</code></p>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="bg-light rounded p-2">
                                    <div class="fs-3">🔄</div>
                                    <span class="badge bg-warning text-dark mb-1 d-block">Passo 3</span>
                                    <p class="small mb-2 fw-semibold">Converta para CSV</p>
                                    <a href="https://convertio.co/pt/xls-csv/" target="_blank"
                                       class="btn btn-warning btn-sm w-100">
                                        <i class="fas fa-external-link-alt me-1"></i>Convertio
                                    </a>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="bg-light rounded p-2 h-100">
                                    <div class="fs-3">📤</div>
                                    <span class="badge bg-success mb-1 d-block">Passo 4</span>
                                    <p class="small mb-0 fw-semibold">Envie o .csv</p>
                                    <p class="small text-muted mt-1">Use o campo abaixo</p>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="idTurma" value="<?= $idTurma ?>">
                        <label class="form-label fw-semibold">Arquivo CSV <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="planilha" accept=".csv" required>
                        <div class="form-text">
                            Apenas <code>.csv</code> — converta em
                            <a href="https://convertio.co/pt/xls-csv/" target="_blank">convertio.co</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="submitPlanilha" class="btn btn-success">
                            <i class="fas fa-upload me-1"></i> Importar
                        </button>
                    </div>
                </form>
            </div></div>
        </div>

        <!-- Scripts dos modais — APÓS os modais no DOM -->
        <script>
        // Modal editar — preenche campos com dados do aluno
        document.getElementById('editarAlunoModal').addEventListener('show.bs.modal', function(event) {
            var btn = event.relatedTarget;
            document.getElementById('editIdAluno').value   = btn.getAttribute('data-id');
            document.getElementById('editNomeAluno').value = btn.getAttribute('data-nome');
            document.getElementById('editMatricula').value = btn.getAttribute('data-matricula');
        });

        // Modal excluir — preenche ID e nome para confirmação
        document.getElementById('confirmDeleteModal').addEventListener('show.bs.modal', function(event) {
            var btn = event.relatedTarget;
            document.getElementById('idAlunoDelete').value    = btn.getAttribute('data-id');
            document.getElementById('nomeAlunoDelete').textContent = btn.getAttribute('data-nome');
        });

        // Cadastrar aluno via AJAX
        document.getElementById('formCadastrarAluno').addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            fetch('cadastrar_aluno.php', { method: 'POST', body: formData })
                .then(res => res.text())
                .then(msg => {
                    document.getElementById('mensagemCadastro').innerHTML =
                        '<div class="alert alert-success">' + msg + '</div>';
                    setTimeout(() => location.reload(), 1200);
                })
                .catch(() => {
                    document.getElementById('mensagemCadastro').innerHTML =
                        '<div class="alert alert-danger">Erro ao cadastrar aluno.</div>';
                });
        });
        </script>
</body>
</html>
