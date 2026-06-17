<?php
require_once __DIR__.'/../php/config/init.php';
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    try {
        $stmt=$pdo->prepare("INSERT INTO administradores (nome,email,contato,senha) VALUES (?,?,?,?)");
        $stmt->execute([$_POST['nome'],$_POST['email'],$_POST['contato']??'',password_hash($_POST['senha'],PASSWORD_DEFAULT)]);
        $msg='Cadastro realizado!';
    } catch(Exception $e){ $msg='E-mail já existe.'; }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Cadastro Admin</title>
<link rel="stylesheet" href="/css/style.css">
<style>:root{--primary-color:<?=e($config['cor_primaria'])?>;--secondary-color:<?=e($config['cor_secundaria'])?>;}</style>
</head><body>
<a href="/admin/login.php" class="back-link">← Voltar</a>
<div class="auth-wrap"><form class="auth-card" method="post">
  <h1>SEJA BEM VINDO</h1><p class="sub">Realize o cadastro</p>
  <?php if($msg):?><div class="alert alert-success"><?=e($msg)?></div><?php endif;?>
  <label>Nome Completo</label><input name="nome" required>
  <label>Email</label><input type="email" name="email" required>
  <label>Contato</label><input name="contato">
  <label>Senha</label><input type="password" name="senha" required>
  <div class="links"><a href="/admin/login.php" style="color:#fff">Já possui uma conta? Faça login.</a></div>
  <button class="btn btn-block">Cadastrar</button>
</form></div></body></html>
