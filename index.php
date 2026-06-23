<?php 
require_once __DIR__ . '/php/config/init.php'; 
require __DIR__ . '/php/includes/header.php'; 
// $banners = $pdo->query("SELECT * FROM banners ORDER BY ordem")->fetchAll();

$banners = [
    [
        'selo'       => 'Exclusivo: 09 a 24 de Abril',
        'linha1'     => '50%',
        'linha2'     => 'Off',
        'cta_texto'  => 'Compre agora!',
        'cta_link'   => 'catalogo.php',
    ],
    [
        'selo'       => 'Coleção desta semana',
        'linha1'     => 'Peças',
        'linha2'     => 'Novas',
        'cta_texto'  => 'Conferir agora!',
        'cta_link'   => 'catalogo.php',
    ],
    [
        'selo'       => 'Todo dia tem garimpo',
        'linha1'     => 'Achados',
        'linha2'     => 'Únicos',
        'cta_texto'  => 'Ver catálogo!',
        'cta_link'   => 'catalogo.php',
    ],
];
?>

<section class="carrossel-banners" id="carrossel-banners" aria-label="Promoções em destaque">
    <?php foreach ($banners as $indice => $banner): ?>
        <div
            class="banner-slide <?= $indice === 0 ? 'banner-slide--ativo' : '' ?>"
            data-slide="<?= $indice ?>"
        >
            <!-- "%" decorativos, conforme o fundo do banner no Figma -->
            <div class="banner-slide__decoracao" aria-hidden="true">
                <span style="top:10%; left:6%;">%</span>
                <span style="top:55%; left:14%; font-size:40px;">%</span>
                <span style="top:15%; right:8%;">%</span>
                <span style="top:60%; right:14%; font-size:40px;">%</span>
            </div>

            <span class="banner-slide__selo"><?= e($banner['selo']) ?></span>
            <h1 class="banner-slide__titulo"><?= e($banner['linha1']) ?></h1>
            <p class="banner-slide__subtitulo"><?= e($banner['linha2']) ?></p>
            <a href="<?= e($banner['cta_link']) ?>" class="banner-slide__cta"><?= e($banner['cta_texto']) ?></a>
        </div>
    <?php endforeach; ?>

    <!-- Bolinhas de navegação (controladas por js/script_loja.js) -->
    <div class="carrossel-banners__pontos">
        <?php foreach ($banners as $indice => $banner): ?>
            <button
                type="button"
                class="ponto <?= $indice === 0 ? 'ponto--ativo' : '' ?>"
                data-ir-para-slide="<?= $indice ?>"
                aria-label="Ir para o banner <?= $indice + 1 ?>"
            ></button>
        <?php endforeach; ?>
    </div>

</section>

<h2 class="section-title">PRODUTOS</h2>
<div class="products-grid">
  <?php
  $stmt = $pdo->query("SELECT * FROM produtos WHERE status='disponivel' ORDER BY criado_em DESC LIMIT 12");
  foreach ($stmt as $produto):
    require __DIR__ . '/php/includes/card-produto.php';
  endforeach;
  ?>
</div>

<!-- <h2 class="section-title">GARIMPE PEÇAS ÚNICAS COM OS MELHORES PREÇOS!</h2> -->
<section class="banner-promocional">
    <p>Garimpe agora peças únicas e com os melhores preços!</p>
</section>
<?php require __DIR__ . '/php/includes/footer.php'; ?>
