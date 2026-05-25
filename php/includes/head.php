<?php
// Variáveis esperadas: $pageTitle (string), $basePath ('' ou '../')
?><head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Multimeios') ?> | Multimeios</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="<?= $basePath ?>assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Ícones e fontes personalizadas — 100% local, sem chamadas externas -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/fonts.min.css" />

    <link rel="stylesheet" href="<?= $basePath ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/plugins.min.css" />
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/kaiadmin.min.css" />

    <!-- Substitui Public Sans (Google Fonts) por fonte do sistema — funciona offline -->
    <style>
      body, html, .navbar, .card-title, .card-category,
      .sidebar-wrapper, h1,h2,h3,h4,h5,h6, .btn, input, select, textarea {
        font-family: 'Segoe UI', Arial, sans-serif !important;
      }
    </style>
</head>
