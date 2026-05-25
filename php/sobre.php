<?php
session_start();
if (!isset($_SESSION['autenticado'])) {
    header('Location: ../index.php');
    exit;
}

$basePath   = '../';
$activePage = 'sobre';
$pageTitle  = 'Sobre';
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
                    <li class="nav-item"><a href="#">Sobre</a></li>
                </ul>

                <!-- Card principal -->
                <div class="card card-round mb-3">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                <img src="../assets/img/kaiadmin/multimeioseeep.png"
                                     alt="Logo Multimeios" class="img-fluid"
                                     style="max-width: 180px;">
                            </div>
                            <div class="col-md-9">
                                <h4 class="fw-bold">Sistema Multimeios</h4>
                                <p class="text-muted">
                                    Sistema de gerenciamento de biblioteca escolar desenvolvido para facilitar
                                    o controle de empréstimos, acervo, turmas e alunos.
                                </p>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-1"><i class="fas fa-book-open text-primary me-2"></i>Cadastro e busca de livros</li>
                                            <li class="mb-1"><i class="fas fa-th-list text-success me-2"></i>Controle de empréstimos e devoluções</li>
                                            <li class="mb-1"><i class="fas fa-users text-info me-2"></i>Gerenciamento de turmas e alunos</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-1"><i class="fas fa-list-ol text-warning me-2"></i>Ranking de leitura por mês</li>
                                            <li class="mb-1"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Alertas de atraso automáticos</li>
                                            <li class="mb-1"><i class="fas fa-file-csv text-secondary me-2"></i>Importação em lote via planilha</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desenvolvido por -->
                <div class="row g-3">
                    <!-- Escola -->
                    <div class="col-md-6">
                        <div class="card card-round h-100">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <img src="../assets/img/logo_djmm.png"
                                         alt="EEEP Dep. José Maria Melo"
                                         class="img-fluid"
                                         style="max-height:100px; object-fit:contain;">
                                </div>
                                <h5 class="fw-bold">EEEP Dep. José Maria Melo</h5>
                                <p class="text-muted small mb-3">
                                    Escola Estadual de Educação Profissional<br>
                                    Deputado José Maria Melo
                                </p>
                                <a href="https://www.instagram.com/eeepdepjosemariamelo/"
                                   target="_blank"
                                   class="btn btn-outline-danger btn-round">
                                    <i class="fab fa-instagram me-1"></i>
                                    @eeepdepjosemariamelo
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Curso -->
                    <div class="col-md-6">
                        <div class="card card-round h-100">
                            <div class="card-body text-center py-4">
                                <div class="mb-3">
                                    <img src="../assets/img/logo_infor.png"
                                         alt="Curso Técnico em Informática"
                                         class="img-fluid"
                                         style="max-height:100px; object-fit:contain;">
                                </div>
                                <h5 class="fw-bold">Curso Técnico em Informática</h5>
                                <p class="text-muted small mb-3">
                                    Desenvolvido pelos alunos do<br>
                                    Curso Técnico em Informática — 2024
                                </p>
                                <a href="https://www.instagram.com/infor_epdjmm/"
                                   target="_blank"
                                   class="btn btn-outline-danger btn-round">
                                    <i class="fab fa-instagram me-1"></i>
                                    @infor_epdjmm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Versão -->
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Multimeios v2.0 &mdash; 2025 &mdash;
                        <a href="manual.php">Ver Manual do Usuário</a>
                    </small>
                </div>

            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
</body>
</html>
