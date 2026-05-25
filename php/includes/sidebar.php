<?php
// Variáveis esperadas: $activePage (string), $basePath ('' ou '../')
$_active = $activePage ?? '';
?>
<div class="sidebar" data-background-color="white">
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="green">
            <a href="<?= $basePath ?>principal.php" class="logo">
                <img src="<?= $basePath ?>assets/img/kaiadmin/logo_light.svg"
                     alt="Multimeios" class="navbar-brand" height="180" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
            </div>
            <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
        </div>
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item <?= $_active === 'inicio' ? 'active' : '' ?>">
                    <a href="<?= $basePath ?>principal.php">
                        <i class="fas fa-home"></i>
                        <p>Início</p>
                    </a>
                </li>
                <li class="nav-item <?= $_active === 'livros' ? 'active' : '' ?>">
                    <a href="<?= $basePath ?>livros.php">
                        <i class="fas fa-book-open"></i>
                        <p>Livros</p>
                    </a>
                </li>
                <li class="nav-item <?= $_active === 'emprestimos' ? 'active' : '' ?>">
                    <a href="<?= $basePath ?>php/emprestimo.php">
                        <i class="fas fa-th-list"></i>
                        <p>Empréstimos</p>
                    </a>
                </li>
                <li class="nav-item <?= $_active === 'turmas' ? 'active' : '' ?>">
                    <a href="<?= $basePath ?>php/turmas.php">
                        <i class="far fa-chart-bar"></i>
                        <p>Turmas</p>
                    </a>
                </li>
                <li class="nav-item <?= $_active === 'ranking' ? 'active' : '' ?>">
                    <a href="<?= $basePath ?>php/ranking.php">
                        <i class="fas fa-list-ol"></i>
                        <p>Ranking</p>
                    </a>
                </li>
                <li class="nav-item <?= $_active === 'manual' ? 'active' : '' ?>">
                    <a href="<?= $basePath ?>php/manual.php">
                        <i class="fas fa-book"></i>
                        <p>Manual</p>
                    </a>
                </li>
                <li class="nav-item <?= $_active === 'sobre' ? 'active' : '' ?>">
                    <a href="<?= $basePath ?>php/sobre.php">
                        <i class="fas fa-info-circle"></i>
                        <p>Sobre</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
