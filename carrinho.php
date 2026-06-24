<?php
require_once __DIR__.'/php/config/init.php';
if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho']=[];

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
if ($acao==='add') {
    $pid=(int)$_POST['produto_id']; $_SESSION['carrinho'][$pid]=($_SESSION['carrinho'][$pid]??0)+1;
    header('Location:' . url('/carrinho.php')); exit;
}
if ($acao==='remove') { unset($_SESSION['carrinho'][(int)$_GET['id']]); header('Location:' . url('/carrinho.php')); exit; }
if ($acao==='clear')  { $_SESSION['carrinho']=[]; header('Location:' . url('/carrinho.php')); exit; }
if ($acao==='qtd')    {
    $pid=(int)$_POST['id']; $q=max(1,(int)$_POST['qtd']);
    $_SESSION['carrinho'][$pid]=$q; header('Location:' . url('/carrinho.php')); exit;
}

require __DIR__.'/php/includes/header.php';

$itens=[]; $total=0;
if ($_SESSION['carrinho']) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['carrinho'])));
    foreach ($pdo->query("SELECT * FROM produtos WHERE id IN ($ids)") as $p) {
        $p['qtd']=$_SESSION['carrinho'][$p['id']];
        $p['subtotal']=$p['qtd']*$p['preco'];
        $total += $p['subtotal'];
        $itens[]=$p;
    }
}

$msg = "Olá, tenho interesse nos seguintes produtos:%0A";
foreach ($itens as $i) $msg .= "- {$i['nome']} (tam {$i['tamanho']}) x{$i['qtd']}%0A";
$msg .= "Total: R$ ".number_format($total,2,',','.');
$wppUrl = "https://wa.me/{$config['whatsapp']}?text=".$msg;
?>
<h1 class="section-title">Carrinho</h1>
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px" class="cart-layout">
  <div>
    <?php foreach ($itens as $p):
      $img = uploadUrl($p['imagem_principal']) ?? 'https://via.placeholder.com/100'; ?>
      <div class="cart-row">
        <img src="<?=e($img)?>">
        <div class="info">
          <strong><?=e($p['nome'])?></strong>
          <div><span class="tag"><?=e($p['estado'])?></span><span class="tag"><?=e($p['tamanho'])?></span></div>
          <form method="post" style="margin-top:6px">
            <input type="hidden" name="acao" value="qtd"><input type="hidden" name="id" value="<?=$p['id']?>">
            Qtd: <input type="number" name="qtd" value="<?=$p['qtd']?>" min="1" style="width:60px" onchange="this.form.submit()">
          </form>
        </div>
        <div>R$ <?=number_format($p['subtotal'],2,',','.')?></div>
        <a href="?acao=remove&id=<?=$p['id']?>" data-confirm="Remover item?">🗑️</a>
      </div>
    <?php endforeach; if(!$itens):?>
      <p>Seu carrinho está vazio. <a href="<?= url('/catalogo.php') ?>">Ver produtos</a></p>
    <?php endif;?>
  </div>
  <div class="summary">
    <h3>Resumo do Pedido</h3>
    <p style="font-size:22px;margin:14px 0">Total: <strong>R$ <?=number_format($total,2,',','.')?></strong></p>
    <a href="<?= url('/catalogo.php') ?>" class="btn btn-outline btn-block" style="margin-bottom:8px">Continuar Comprando</a>
    <a href="?acao=clear" class="btn btn-outline btn-block" data-confirm="Esvaziar carrinho?">Esvaziar Carrinho</a>
    <?php if($itens):?>
      <a href="<?=$wppUrl?>" target="_blank" class="btn btn-block" style="margin-top:14px">Finalizar a compra</a>
    <?php endif;?>
  </div>
</div>
<?php require __DIR__.'/php/includes/footer.php'; ?>
