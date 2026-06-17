<?php
require_once __DIR__.'/../php/config/init.php';
requireCliente();
require __DIR__.'/../php/includes/header.php';
$stmt=$pdo->prepare("SELECT * FROM usuarios WHERE id=?"); $stmt->execute([$_SESSION['usuario_id']]);
$u=$stmt->fetch();
?>
<h1 class="section-title">Meu Perfil</h1>
<div class="card" style="max-width:500px;margin:auto">
  <p><strong>Nome:</strong> <?=e($u['nome'])?></p>
  <p><strong>E-mail:</strong> <?=e($u['email'])?></p>
  <p><strong>Contato:</strong> <?=e($u['contato'])?></p>
  <a href="/logout.php" class="btn" style="margin-top:14px">Sair</a>
</div>
<?php require __DIR__.'/../php/includes/footer.php'; ?>
