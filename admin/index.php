<?php require __DIR__.'/_layout.php';
$total = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
$disp  = $pdo->query("SELECT COUNT(*) FROM produtos WHERE status='disponivel'")->fetchColumn();
$vend  = $pdo->query("SELECT COUNT(*) FROM produtos WHERE status='vendido'")->fetchColumn();
$cats  = $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn();
?>
<h1>Dashboard</h1>
<div class="stats">
  <div class="stat-card"><h4>Total de produtos</h4><p><?=$total?></p></div>
  <div class="stat-card"><h4>Disponíveis</h4><p><?=$disp?></p></div>
  <div class="stat-card"><h4>Vendidos</h4><p><?=$vend?></p></div>
  <div class="stat-card"><h4>Categorias</h4><p><?=$cats?></p></div>
</div>
<h2 style="margin-top:30px">Resumo</h2>
<p>Bem-vindo(a), <?=e($_SESSION['admin_nome'])?>!</p>
<?php require __DIR__.'/_footer.php'; ?>
