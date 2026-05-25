<?php
session_start();
if (isset($_SESSION['autenticado'])) {
    header('Location: principal.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Multimeios</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { height: 100%; }
        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }
        .form-signin {
            width: 100%;
            max-width: 360px;
            padding: 15px;
            margin: auto;
        }
        .form-signin .form-control {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 10px;
            font-size: 16px;
        }
        .form-signin .form-control:focus { z-index: 2; }
        .form-signin input[type="text"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>
<body class="text-center">
    <form class="form-signin" action="php/autenticacao.php" method="post">
        <img class="mb-4" src="assets/img/kaiadmin/multimeioseeep.png" alt="Logo Multimeios" width="200" height="200">
        <h1 class="h3 mb-3 font-weight-normal">Entre com suas credenciais</h1>

        <label for="usuario" class="sr-only">Usuário</label>
        <input type="text" name="usuario" id="usuario" class="form-control"
               placeholder="Usuário" required autofocus>

        <label for="senha" class="sr-only">Senha</label>
        <input type="password" name="senha" id="senha" class="form-control"
               placeholder="Senha" required>

        <button class="btn btn-lg btn-primary btn-block mt-2" type="submit">Entrar</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2023–2024</p>
    </form>

    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
</body>
</html>
