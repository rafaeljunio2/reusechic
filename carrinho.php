<?php
require_once __DIR__.'/php/config/init.php';
if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho']=[];

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
if ($acao==='add') {
    $pid=(int)$_POST['produto_id'];
    $stmt=$pdo->prepare("SELECT nome,estoque FROM produtos WHERE id=?");
    $stmt->execute([$pid]);
    if ($prod=$stmt->fetch()) {
        $estoque=(int)$prod['estoque'];
        $atual=$_SESSION['carrinho'][$pid]??0;
        if ($atual>=$estoque) {
            $_SESSION['carrinho_aviso']="Estoque insuficiente: só há {$estoque} un. disponível de \"{$prod['nome']}\".";
        } else {
            $_SESSION['carrinho'][$pid]=$atual+1;
            if ($atual+1>=$estoque) {
                $_SESSION['carrinho_aviso']="Quantidade máxima em estoque atingida ({$estoque} un.) para \"{$prod['nome']}\".";
            }
        }
    }
    header('Location:' . url('/carrinho.php')); exit;
}
if ($acao==='remove') { unset($_SESSION['carrinho'][(int)$_GET['id']]); header('Location:' . url('/carrinho.php')); exit; }
if ($acao==='clear')  { $_SESSION['carrinho']=[]; header('Location:' . url('/carrinho.php')); exit; }
if ($acao==='qtd')    {
    $pid=(int)$_POST['id']; $q=max(1,(int)$_POST['qtd']);
    $stmt=$pdo->prepare("SELECT nome,estoque FROM produtos WHERE id=?");
    $stmt->execute([$pid]);
    if ($prod=$stmt->fetch()) {
        $estoque=(int)$prod['estoque'];
        if ($q>$estoque) {
            $q=$estoque;
            $_SESSION['carrinho_aviso']="A quantidade foi limitada ao estoque disponível ({$estoque} un.) de \"{$prod['nome']}\".";
        }
        $_SESSION['carrinho'][$pid]=$q;
    }
    header('Location:' . url('/carrinho.php')); exit;
}

$aviso=$_SESSION['carrinho_aviso']??'';
unset($_SESSION['carrinho_aviso']);

require __DIR__.'/php/includes/header.php';

$itens=[]; $total=0;
if ($_SESSION['carrinho']) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['carrinho'])));
    foreach ($pdo->query("SELECT * FROM produtos WHERE id IN ($ids)") as $p) {
        $estoque=(int)$p['estoque'];
        $qtd=min($_SESSION['carrinho'][$p['id']], max(1,$estoque));
        if ($qtd!==$_SESSION['carrinho'][$p['id']]) $_SESSION['carrinho'][$p['id']]=$qtd;
        $p['qtd']=$qtd;
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

<?php if($aviso):?><div class="alert alert-error"><?=e($aviso)?></div><?php endif;?>
<div class="cart-layout">
  <div class="cart-items">
    <?php foreach ($itens as $p):
      $img = uploadUrl($p['imagem_principal']) ?? 'https://via.placeholder.com/100'; ?>
      <div class="cart-row">
        <img src="<?=e($img)?>" alt="<?=e($p['nome'])?>">
        <div class="info">
          <strong><?=e($p['nome'])?></strong>
          <div><span class="tag"><?=e($p['estado'])?></span><span class="tag"><?=e($p['tamanho'])?></span></div>
          <form method="post" class="cart-row__qtd-form">
            <input type="hidden" name="acao" value="qtd"><input type="hidden" name="id" value="<?=$p['id']?>">
            Qtd: <input type="number" name="qtd" value="<?=$p['qtd']?>" min="1" max="<?=(int)$p['estoque']?>" class="cart-row__qtd-input" onchange="this.form.submit()">
            <small class="cart-row__estoque">Estoque: <?=(int)$p['estoque']?> un.</small>
          </form>
        </div>
        <div class="cart-row__price">R$ <?=number_format($p['subtotal'],2,',','.')?></div>
        <a href="?acao=remove&id=<?=$p['id']?>" class="cart-row__remove" data-confirm="Remover item?">🗑️</a>
      </div>
    <?php endforeach; if(!$itens):?>
      <p class="cart-empty">Seu carrinho está vazio. <a href="<?= url('/catalogo.php') ?>">Ver produtos</a></p>
    <?php endif;?>
  </div>
  <div class="cart-summary">
    <h3>Resumo do Pedido</h3>
    <p class="cart-summary__total">Total: <strong>R$ <?=number_format($total,2,',','.')?></strong></p>
    <a href="<?= url('/catalogo.php') ?>" class="btn btn-outline btn-block cart-summary__btn">Continuar Comprando</a>
    <a href="?acao=clear" class="btn btn-outline btn-block cart-summary__btn" data-confirm="Esvaziar carrinho?">Esvaziar Carrinho</a>
    <?php if($itens):?>
      <a href="<?=$wppUrl?>" target="_blank" class="btn btn-block cart-summary__checkout">Finalizar a compra</a>
    <?php endif;?>
  </div>
</div>
<?php require __DIR__.'/php/includes/footer.php'; ?>
