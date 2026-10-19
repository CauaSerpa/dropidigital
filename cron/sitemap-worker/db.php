<?php

function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception(".env não encontrado");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;

        list($name, $value) = explode('=', $line, 2);

        $value = trim($value);
        $value = trim($value, "'\"");

        $_ENV[trim($name)] = $value;
    }
}

function connectDB() {
    loadEnv(__DIR__ . '/.env');

    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $db   = $_ENV['DB_NAME'] ?? '';
    $user = $_ENV['DB_USER'] ?? '';
    $pass = $_ENV['DB_PASS'] ?? '';

    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true, // melhora performance
            ]
        );

        return $pdo;

    } catch (PDOException $e) {
        throw new Exception("Erro na conexão com banco: " . $e->getMessage());
    }
}

function connectProdDB() {
    loadEnv(__DIR__ . '/.env');

    $host = $_ENV['DB_PROD_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PROD_PORT'] ?? '3306';
    $db   = $_ENV['DB_PROD_NAME'] ?? '';
    $user = $_ENV['DB_PROD_USER'] ?? '';
    $pass = $_ENV['DB_PROD_PASS'] ?? '';

    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true, // melhora performance
            ]
        );

        return $pdo;

    } catch (PDOException $e) {
        throw new Exception("Erro na conexão com banco: " . $e->getMessage());
    }
}

function ensureColumn(PDO $pdo, string $table, string $column, string $definition, string $after = null)
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");

    $stmt->execute([$table, $column]);

    if ($stmt->fetchColumn() == 0) {

        echo "Adicionando coluna {$column}...\n";

        // monta query dinamicamente
        $sql = "ALTER TABLE {$table} ADD COLUMN {$column} {$definition}";

        if (!empty($after)) {
            $sql .= " AFTER {$after}";
        }

        $pdo->exec($sql);
    }
}