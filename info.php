<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== ReuseChic — Teste PHP ===\n\n";

echo "PHP: " . PHP_VERSION . "\n";
echo "Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n\n";

$extensoes = ['pdo', 'pdo_mysql', 'mbstring', 'session'];
foreach ($extensoes as $ext) {
    echo "ext-$ext: " . (extension_loaded($ext) ? 'OK' : 'FALTA') . "\n";
}

echo "\n--- Banco de dados ---\n";

try {
    require_once __DIR__ . '/php/config/db.php';
    $tabelas = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Conexão: OK\n";
    echo "Tabelas: " . count($tabelas) . "\n";
} catch (Throwable $e) {
    echo "Conexão: ERRO — " . $e->getMessage() . "\n";
}

echo "\n=== Fim do teste ===\n";
