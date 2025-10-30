<?php

declare(strict_types=1);

use Core\Env;
use PDO;
use PDOException;

/** @var PDO|null $connection */
static $connection = null;

if ($connection instanceof PDO) {
    return $connection;
}

Env::load();

$host = Env::get('DB_HOST', '127.0.0.1');
$port = Env::get('DB_PORT', '3306');
$name = Env::get('DB_NAME', 'profila');
$user = Env::get('DB_USER', 'root');
$password = Env::get('DB_PASS', '');

$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $connection = new PDO($dsn, $user, $password, $options);
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados. Verifique as configurações.');
}

return $connection;
