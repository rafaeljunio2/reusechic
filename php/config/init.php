<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

// Carrega configurações do banco e disponibiliza em $config
$config = [];
foreach ($pdo->query("SELECT chave, valor FROM configuracoes") as $row) {
    $config[$row['chave']] = $row['valor'];
}

// Base path prefix (e.g. '/reusechic' when served from a subdirectory)
define('BASE', rtrim(str_replace(rtrim($_SERVER['DOCUMENT_ROOT'], '/'), '', dirname(dirname(__DIR__))), '/'));

function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function formatTelefone(?string $numero): string {
    $d = preg_replace('/\D/', '', $numero ?? '');
    if (str_starts_with($d, '55') && strlen($d) > 11) $d = substr($d, 2);
    if (strlen($d) === 11) return sprintf('(%s) %s-%s', substr($d, 0, 2), substr($d, 2, 5), substr($d, 7));
    if (strlen($d) === 10) return sprintf('(%s) %s-%s', substr($d, 0, 2), substr($d, 2, 4), substr($d, 6));
    return $numero ?? '';
}
function url($p = '') { return BASE . $p; }
function isLoggedCliente() { return !empty($_SESSION['usuario_id']); }
function isLoggedAdmin()   { return !empty($_SESSION['admin_id']); }
function requireAdmin() {
    if (!isLoggedAdmin()) { header('Location: ' . url('/admin/login.php')); exit; }
}
function requireCliente() {
    if (!isLoggedCliente()) { header('Location: ' . url('/login.php')); exit; }
}

function uploadsPath(): string {
    static $path;
    return $path ??= dirname(dirname(__DIR__)) . '/uploads/';
}

function saveUpload(array $file, string $prefix): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($file['tmp_name'])) return null;
    $dir = uploadsPath();
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) return null;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $name = uniqid($prefix) . ($ext ? '.' . $ext : '');
    return move_uploaded_file($file['tmp_name'], $dir . $name) ? $name : null;
}
