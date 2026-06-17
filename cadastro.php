<?php
require_once __DIR__ . '/php/config/init.php';
$erro=''; $ok='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nome = trim($_POST['nome']??''); $email = trim($_POST['email']??'');
    $contato = trim($_POST['contato']??''); $senha = $_POST['senha']??'';
    if (!$nome || !$email || !$senha) $erro = 'Preencha todos os campos.';
    elseif (strlen($senha)<6) $erro='Senha deve ter ao menos 6 caracteres.';
    else {
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome,email,contato,senha) VALUES (?,?,?,?)");
            $stmt->execute([$nome,$email,$contato,password_hash($senha,PASSWORD_DEFAULT)]);
            $ok='Cadastro realizado! Faça login.';
        } catch (PDOException $e) { $erro='E-mail já cadastrado.'; }
    }
}
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cadastro</title><link rel="stylesheet" href="<?= url('/css/style.css') ?>">
<style>:root{--primary-color:<?=e($config['cor_primaria'])?>;--secondary-color:<?=e($config['cor_secundaria'])?>;}</style>
</head><body>
<a href="<?= url('/login.php') ?>" class="back-link">← Voltar</a>
<div class="auth-wrap">
  <form class="auth-card" method="post" data-validate>
    <h1>SEJA BEM VINDO</h1>
    <p class="sub">Realize o cadastro</p>
    <?php if($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>
    <?php if($ok): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>
    <label>Nome Completo</label><input name="nome" required>
    <label>Email</label><input type="email" name="email" required>
    <label>Contato</label><input name="contato">
    <label>Senha</label><input type="password" name="senha" required>
    <div class="links"><a href="<?= url('/login.php') ?>" style="color:#fff">Já possui uma conta? Faça login.</a></div>
    <button class="btn btn-block">Cadastrar</button>
  </form>
</div>
</body></html>
