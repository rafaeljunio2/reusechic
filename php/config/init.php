<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

// Carrega configurações do banco e disponibiliza em $config
$config = [];
foreach ($pdo->query("SELECT chave, valor FROM configuracoes") as $row) {
    $config[$row['chave']] = $row['valor'];
}

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function url($p='') { return $p; }
function isLoggedCliente() { return !empty($_SESSION['usuario_id']); }
function isLoggedAdmin()   { return !empty($_SESSION['admin_id']); }
function requireAdmin() {
    if (!isLoggedAdmin()) { header('Location: /admin/login.php'); exit; }
}
function requireCliente() {
    if (!isLoggedCliente()) { header('Location: /login.php'); exit; }
}
