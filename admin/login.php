<?php
require_once __DIR__.'/../php/config/init.php';
$erro='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $stmt=$pdo->prepare("SELECT * FROM administradores WHERE email=?");
    $stmt->execute([$_POST['email']??'']);
    $a=$stmt->fetch();
    if ($a && password_verify($_POST['senha']??'',$a['senha'])) {
        $_SESSION['admin_id']=$a['id']; $_SESSION['admin_nome']=$a['nome'];
        header('Location:/admin/index.php'); exit;
    }
    $erro='Credenciais inválidas. (admin@reusechic.com / admin123)';
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Login Admin</title>
<link rel="stylesheet" href="/css/style.css">
<style>:root{--primary-color:<?=e($config['cor_primaria'])?>;--secondary-color:<?=e($config['cor_secundaria'])?>;}</style>
</head><body>
<a href="/inicial.php" class="back-link">← Voltar</a>
<div class="auth-wrap"><form class="auth-card" method="post">
  <h1>ACESSAR</h1>
  <?php if($erro):?><div class="alert alert-error"><?=e($erro)?></div><?php endif;?>
  <label>Email</label><input type="email" name="email" required>
  <label>Senha</label><input type="password" name="senha" required>
  <div class="links"><a href="/admin/cadastro.php" style="color:#fff">Criar conta admin</a></div>
  <button class="btn btn-block">Entrar</button>
</form></div></body></html>
