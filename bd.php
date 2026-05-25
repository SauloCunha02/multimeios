<?php
// Configurações do banco de dados
$host = 'localhost'; // endereço do servidor MySQL (normalmente é localhost)
$db   = 'bibliotecaep'; // nome do banco de dados
$user = 'root'; // usuário do banco de dados
$pass = ''; // senha do banco de dados
$charset = 'utf8mb4'; // conjunto de caracteres

// Configurações da Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opções adicionais para PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // modo de erro: exceção
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // modo de fetch padrão: array associativo
    PDO::ATTR_EMULATE_PREPARES   => false, // desabilitar emulação de prepared statements
];

try {
    // Criação da conexão PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Tratamento de erros
    echo "Falha na conexão: " . $e->getMessage();
    exit;
}
?>
