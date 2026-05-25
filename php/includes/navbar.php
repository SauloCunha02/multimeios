<?php
// Variáveis esperadas: $basePath ('' ou '../')
?>
<div class="main-header">
    <div class="main-header-logo">
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
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
         data-background-color="green">
        <div class="container-fluid">
            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown"
                       href="#" aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="<?= $basePath ?>php/<?= htmlspecialchars($_SESSION['foto'] ?? '') ?>"
                                 alt="Foto do usuário" class="avatar-img rounded-circle" />
                        </div>
                        <span class="profile-username">
                            <span class="fw-bold">Prof. <?= htmlspecialchars($_SESSION['nome'] ?? '') ?></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= $basePath ?>php/sair.php">Sair</a>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>
