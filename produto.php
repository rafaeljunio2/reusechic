<?php
require_once __DIR__.'/php/config/init.php';
$id = (int)($_GET['id']??0);
$stmt = $pdo->prepare("SELECT p.*, c.nome cat_nome FROM produtos p LEFT JOIN categorias c ON c.id=p.categoria_id WHERE p.id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if(!$p){ http_response_code(404); echo 'Produto não encontrado'; exit; }
$gal = $pdo->prepare("SELECT * FROM imagens_produtos WHERE produto_id=?");
$gal->execute([$id]); $galeria = $gal->fetchAll();
require __DIR__.'/php/includes/header.php';

$wppMsg = urlencode("Olá, tenho interesse no produto: {$p['nome']} ({$p['tamanho']}) - R$ ".number_format($p['preco'],2,',','.'));
$wppUrl = "https://wa.me/{$config['whatsapp']}?text={$wppMsg}";
$mainImg = $p['imagem_principal'] ? '/uploads/'.$p['imagem_principal'] : 'https://via.placeholder.com/500';
?>
<div class="product-detail">
  <div class="gallery">
    <img src="<?=e($mainImg)?>" alt="<?=e($p['nome'])?>">
    <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap">
      <?php foreach($galeria as $g):?>
        <img src="/uploads/<?=e($g['caminho'])?>" style="width:80px;height:80px;object-fit:cover;border-radius:8px">
      <?php endforeach;?>
    </div>
  </div>
  <div>
    <h1><?=e($p['nome'])?></h1>
    <p>Categoria: <?=e($p['cat_nome']??'-')?></p>
    <p>Estado: <span class="tag"><?=e($p['estado'])?></span> Tamanho: <span class="tag"><?=e($p['tamanho'])?></span></p>
    <div class="price">R$ <?=number_format($p['preco'],2,',','.')?></div>
    <p style="margin:16px 0"><?=nl2br(e($p['descricao']))?></p>
    <form method="post" action="/carrinho.php" style="display:inline">
      <input type="hidden" name="acao" value="add">
      <input type="hidden" name="produto_id" value="<?=$p['id']?>">
      <button class="btn">Adicionar ao Carrinho</button>
    </form>
    <a href="<?=$wppUrl?>" target="_blank" class="btn btn-outline">Comprar via WhatsApp</a>
  </div>
</div>
<?php require __DIR__.'/php/includes/footer.php'; ?>
