<?php
require_once __DIR__ . '/php/config/init.php';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($senha, $u['senha'])) {
        $_SESSION['usuario_id'] = $u['id'];
        $_SESSION['usuario_nome'] = $u['nome'];
        header('Location: /index.php'); exit;
    } else { $erro = 'E-mail ou senha inválidos.'; }
}
?>
<!DOCTYPE html><html lang="pt-br"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Cliente</title><link rel="stylesheet" href="/css/style.css">
<style>:root{--primary-color:<?=e($config['cor_primaria'])?>;--secondary-color:<?=e($config['cor_secundaria'])?>;}</style>
</head><body>
<a href="/inicial.php" class="back-link">← Voltar</a>
<div class="auth-wrap">
  <form class="auth-card" method="post" data-validate>
    <h1>ACESSAR</h1>
    <?php if($erro): ?><div class="alert alert-error"><?= e($erro) ?></div><?php endif; ?>
    <label>Email</label><input type="email" name="email" required>
    <label>Senha</label><input type="password" name="senha" required>
    <div class="links"><a href="/recuperar.php" style="color:#fff">Esqueceu a senha?</a></div>
    <button class="btn btn-block">Entrar</button>
    <div class="links"><a href="/cadastro.php" style="color:#fff">Criar conta</a></div>
  </form>
</div>
</body></html>
