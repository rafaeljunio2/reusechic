<?php
require_once __DIR__ . '/php/config/init.php';
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    // Em produção, envie e-mail com token. Aqui apenas confirma existência.
    $stmt=$pdo->prepare("SELECT id FROM usuarios WHERE email=?");
    $stmt->execute([$_POST['email']??'']);
    $msg = $stmt->fetch() ? 'Instruções enviadas para seu e-mail.' : 'E-mail não encontrado.';
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Recuperar senha</title>
<link rel="stylesheet" href="/css/style.css">
<style>:root{--primary-color:<?=e($config['cor_primaria'])?>;--secondary-color:<?=e($config['cor_secundaria'])?>;}</style>
</head><body>
<a href="/login.php" class="back-link">← Voltar</a>
<div class="auth-wrap"><form class="auth-card" method="post">
  <h1>RECUPERAR SENHA</h1>
  <?php if($msg):?><div class="alert alert-success"><?=e($msg)?></div><?php endif;?>
  <label>Email</label><input type="email" name="email" required>
  <button class="btn btn-block">Enviar</button>
</form></div></body></html>
