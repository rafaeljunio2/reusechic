<?php require_once __DIR__ . '/../config/init.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($config['nome_site'] ?? 'ReuseChic') ?></title>
<link rel="stylesheet" href="<?= url('/css/style.css') ?>">
<style>
  :root {
    --primary-color: <?= e($config['cor_primaria'] ?? '#c98b7a') ?>;
    --secondary-color: <?= e($config['cor_secundaria'] ?? '#f6dcd5') ?>;
  }
</style>
</head>
<body>
<header class="navbar">
  <a href="<?= url('/index.php') ?>" class="logo">
    <?php if (!empty($config['logo'])): ?>
      <img src="<?= url('/') . e($config['logo']) ?>" alt="logo" onerror="this.style.display='none'">
    <?php endif; ?>
    <span><?= e($config['nome_site'] ?? 'Reuse Chic') ?></span>
  </a>
  <form class="search" action="<?= url('/catalogo.php') ?>" method="get">
    <input type="text" name="q" placeholder="Digite aqui o que deseja buscar">
    <button type="submit">🔍</button>
  </form>
  <nav class="nav-links">
    <?php if (isLoggedCliente()): ?>
      <a href="<?= url('/perfil.php') ?>">👤 <?= e($_SESSION['usuario_nome']) ?></a>
      <a href="<?= url('/logout.php') ?>">Sair</a>
    <?php else: ?>
      <a href="<?= url('/inicial.php') ?>">👤 Entrar</a>
    <?php endif; ?>
    <a href="<?= url('/catalogo.php') ?>">Produtos</a>
    <a href="<?= url('/carrinho.php') ?>">🛒</a>
  </nav>
</header>
<main class="container">
