<?php 
require_once __DIR__ . '/../config/init.php'; 

$qtd_carrinho = isset($itens_carrinho) ? count($itens_carrinho) : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($config['nome_site'] ?? 'Reuse Chic') ?></title>
<link rel="stylesheet" href="<?= url('/css/style.css') ?>">
<link rel="stylesheet" href="<?= url('/css/header.css') ?>">
<link rel="stylesheet" href="<?= url('/css/conteudo-loja.css') ?>">
<link rel="stylesheet" href="<?= url('/css/card-produto.css') ?>">
<link rel="stylesheet" href="<?= url('/css/rodape-loja.css') ?>">
<link rel="stylesheet" href="<?= url('/css/banner-promocional.css') ?>">
<style>
  :root {
    --primary-color: <?= e($config['cor_primaria'] ?? '#c98b7a') ?>;
    --secondary-color: <?= e($config['cor_secundaria'] ?? '#f6dcd5') ?>;
  }
</style>
</head>
<body>

<header class="topo-loja">
    <!-- LOGO -->
    <a href="<?= url('/index.php') ?>" class="topo-loja__logo">
        <?php $logoSrc = uploadUrl($config['logo'] ?? null) ?? url('/img/logo.png'); ?>
        <img src="<?= e($logoSrc) ?>" alt="<?= e($config['nome_site'] ?? 'ReuseChic') ?>">
    </a>

    <!-- BUSCA -->
    <!-- A busca aponta para catalogo.php (tela "Produtos" do Figma),
         que ainda será desenvolvida em uma próxima etapa. -->
    <form class="topo-loja__busca" action="catalogo.php" method="GET">
        <input type="text" name="busca" placeholder="Digite aqui o que deseja buscar" autocomplete="off">
        <button type="submit" aria-label="Buscar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>
    </form>

    <!-- AÇÕES: ENTRAR / PRODUTOS / CARRINHO -->
    <nav class="topo-loja__acoes">
        <a href="./admin">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            Entrar
        </a>

        <!-- Catálogo completo (tela "Produtos" do Figma) — próxima etapa -->
        <a href="catalogo.php">Produtos</a>

        <a href="carrinho.php" class="topo-loja__carrinho" aria-label="Ver carrinho">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <?php if ($qtd_carrinho > 0): ?>
                <span class="topo-loja__carrinho-badge"><?= (int) $qtd_carrinho ?></span>
            <?php endif; ?>
        </a>
    </nav>
</header>

<main class="conteudo-loja">
