<?php require_once __DIR__ . '/php/config/init.php'; ?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Entrar - <?= e($config['nome_site']) ?></title>
<link rel="stylesheet" href="<?= url('/css/style.css') ?>">
<style>:root{--primary-color:<?=e($config['cor_primaria'])?>;--secondary-color:<?=e($config['cor_secundaria'])?>;}</style>
</head><body>
<a href="<?= url('/index.php') ?>" class="back-link">← Home</a>
<div class="auth-wrap">
  <div class="auth-card" style="text-align:center">
    <h1>Sou</h1>
    <a href="<?= url('/catalogo.php') ?>" class="btn btn-block" style="margin:20px 0">Cliente</a>
    <p>ou</p>
    <a href="<?= url('/admin/login.php') ?>" class="btn btn-block" style="margin-top:20px">Administrador</a>
  </div>
</div>
</body></html>
