<?php require_once __DIR__ . '/php/config/init.php'; require __DIR__ . '/php/includes/header.php'; ?>

<section class="banner">
  <small><?= e($config['subtitulo_home'] ?? 'Exclusivo') ?></small>
  <h2><?= e($config['titulo_home'] ?? '50% off') ?></h2>
  <a href="<?= url('/catalogo.php') ?>" class="btn">Compre agora!</a>
</section>

<h2 class="section-title">PRODUTOS</h2>
<div class="products-grid">
  <?php
  $stmt = $pdo->query("SELECT * FROM produtos WHERE status='disponivel' ORDER BY criado_em DESC LIMIT 12");
  foreach ($stmt as $p):
    $img = $p['imagem_principal'] ? url('/uploads/') . $p['imagem_principal'] : 'https://via.placeholder.com/300?text=Sem+imagem';
  ?>
    <a href="<?= url('/produto.php') ?>?id=<?= $p['id'] ?>" class="product-card">
      <img src="<?= e($img) ?>" alt="<?= e($p['nome']) ?>">
      <h3><?= e($p['nome']) ?></h3>
      <p class="price">R$ <?= number_format($p['preco'],2,',','.') ?></p>
    </a>
  <?php endforeach; ?>
</div>

<h2 class="section-title">GARIMPE PEÇAS ÚNICAS COM OS MELHORES PREÇOS!</h2>

<?php require __DIR__ . '/php/includes/footer.php'; ?>
