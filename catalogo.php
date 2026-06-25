<?php
require_once __DIR__.'/php/config/init.php';
require __DIR__.'/php/includes/header.php';

$q = trim($_GET['q'] ?? '');
$cat = $_GET['categoria'] ?? '';
$tam = $_GET['tamanho'] ?? '';
$ord = $_GET['ord'] ?? 'recentes';
$min = isset($_GET['min']) && $_GET['min'] !== '' ? (float)$_GET['min'] : 0;
$max = isset($_GET['max']) && $_GET['max'] !== '' ? (float)$_GET['max'] : 99999;

$where = ["status='disponivel'", "preco BETWEEN ? AND ?"];
$params = [$min,$max];
if ($q)   { $where[]="nome LIKE ?";       $params[]="%$q%"; }
if ($cat) { $where[]="categoria_id=?";    $params[]=(int)$cat; }
if ($tam) { $where[]="tamanho=?";         $params[]=$tam; }

$order = ['recentes'=>'criado_em DESC','menor'=>'preco ASC','maior'=>'preco DESC'][$ord] ?? 'criado_em DESC';
$sql = "SELECT * FROM produtos WHERE ".implode(' AND ',$where)." ORDER BY $order";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$produtos = $stmt->fetchAll();

$cats = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
?>
<h1 class="section-title">PRODUTOS</h1>
<div class="products-form">
  <form class="filters" method="get">
    <input name="q" value="<?=e($q)?>" placeholder="Buscar...">
    <select name="categoria">
      <option value="">Categoria</option>
      <?php foreach($cats as $c):?>
      <option value="<?=$c['id']?>" <?=$cat==$c['id']?'selected':''?>><?=e($c['nome'])?></option>
      <?php endforeach;?>
    </select>
    <select name="tamanho">
      <option value="">Tamanho</option>
      <?php foreach(['PP','P','M','G','GG'] as $t):?>
      <option <?=$tam===$t?'selected':''?>><?=$t?></option>
      <?php endforeach;?>
    </select>
    <input type="number" name="min" placeholder="Min R$" value="<?=$min?:''?>">
    <input type="number" name="max" placeholder="Max R$" value="<?=$max!=99999?$max:''?>">
    <select name="ord">
      <option value="recentes" <?=$ord==='recentes'?'selected':''?>>Mais recentes</option>
      <option value="menor" <?=$ord==='menor'?'selected':''?>>Menor preço</option>
      <option value="maior" <?=$ord==='maior'?'selected':''?>>Maior preço</option>
    </select>
    <button class="btn">Filtrar</button>
  </form>
</div>


<div class="products-grid">
  <?php foreach($produtos as $p):
    $img = uploadUrl($p['imagem_principal']) ?? 'https://via.placeholder.com/300';
  ?>
    <a href="<?= url('/produto.php') ?>?id=<?=$p['id']?>" class="product-card">
      <img src="<?=e($img)?>" alt="<?=e($p['nome'])?>">
      <h3><?=e($p['nome'])?> (<?=e($p['tamanho'])?>)</h3>
      <p class="price">R$ <?=number_format($p['preco'],2,',','.')?></p>
    </a>
  <?php endforeach; if(!$produtos):?>
    <p>Nenhum produto encontrado.</p>
  <?php endif;?>
</div>

<?php require __DIR__.'/php/includes/footer.php'; ?>
