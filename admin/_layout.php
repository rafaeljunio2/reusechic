<?php
// Layout/sidebar comum do admin
require_once __DIR__.'/../php/config/init.php';
requireAdmin();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin - <?= e($config['nome_site']) ?></title>
<link rel="stylesheet" href="<?= url('/css/style.css') ?>">
<style>:root{--primary-color:<?=e($config['cor_primaria'])?>;--secondary-color:<?=e($config['cor_secundaria'])?>;}</style>
</head><body>
<div class="admin-layout">
<aside class="sidebar">
  <h2><?= e($config['nome_site']) ?></h2>
  <a href="<?= url('/admin/index.php') ?>"       class="<?= $current==='index.php'?'active':'' ?>">📊 Dashboard</a>
  <a href="<?= url('/admin/produtos.php') ?>"    class="<?= str_starts_with($current,'produtos')?'active':'' ?>">🛍️ Produtos</a>
  <a href="<?= url('/admin/categorias.php') ?>"  class="<?= $current==='categorias.php'?'active':'' ?>">🏷️ Categorias</a>
  <a href="<?= url('/admin/banners.php') ?>"     class="<?= $current==='banners.php'?'active':'' ?>">🖼️ Banners</a>
  <a href="<?= url('/admin/personalizar.php') ?>" class="<?= $current==='personalizar.php'?'active':'' ?>">🎨 Personalizar</a>
  <a href="<?= url('/admin/perfil.php') ?>"      class="<?= $current==='perfil.php'?'active':'' ?>">👤 Perfil</a>
  <a href="<?= url('/admin/logout.php') ?>">🚪 Sair</a>
  <a href="<?= url('/index.php') ?>" style="margin-top:20px">↗ Ver site</a>
</aside>
<main class="admin-main">
