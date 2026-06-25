<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Carrega .env se existir (local); em produção o Dokploy injeta as vars diretamente.
// createUnsafeImmutable também popula getenv() — o SDK do Cloudinary lê CLOUDINARY_URL assim.
$dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/cloudinary.php';

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
function uploadUrl(?string $file): ?string {
    if (!$file) return null;
    // URL do Cloudinary — retorna diretamente
    if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) return $file;
    // Legado: caminho local (imagens antigas antes da migração)
    $file = basename(preg_replace('#^uploads/#', '', ltrim(str_replace('\\', '/', $file), '/')));
    return url('/uploads/' . $file);
}
function siteLogoUrl(): ?string {
    global $config;
    if (empty($config['logo'])) return null;
    return uploadUrl($config['logo']);
}
function isLoggedCliente() { return !empty($_SESSION['usuario_id']); }
function isLoggedAdmin()   { return !empty($_SESSION['admin_id']); }
function requireAdmin() {
    if (!isLoggedAdmin()) { header('Location: ' . url('/admin/login.php')); exit; }
}
function requireCliente() {
    if (!isLoggedCliente()) { header('Location: ' . url('/login.php')); exit; }
}

// Mapeia o prefixo legado para uma subpasta no Cloudinary e delega o upload.
function saveUpload(array $file, string $prefix): ?string {
    $folder = match($prefix) {
        'p_'    => 'reusechic/produtos',
        'g_'    => 'reusechic/galeria',
        'b_'    => 'reusechic/banners',
        'logo_' => 'reusechic/logo',
        default => 'reusechic',
    };
    return cloudinaryUpload($file, $folder);
}
