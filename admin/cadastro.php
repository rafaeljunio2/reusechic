<?php
require_once __DIR__.'/../php/config/init.php';
$msg='';
$erros=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nome   = trim($_POST['nome'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $senha  = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    // Validação de e-mail
    if ($email === '') {
        $erros[] = 'O e-mail é obrigatório'; // MSG-04
    } elseif (mb_strlen($email) > 255) {
        $erros[] = 'O e-mail deve ter no máximo 255 caracteres'; // MSG-54
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Digite um e-mail válido (ex: contato@brecho.com)'; // MSG-10
    } else {
        $stmt = $pdo->prepare("SELECT id FROM administradores WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $erros[] = 'Este e-mail já está cadastrado em outro brechó'; // MSG-09
    }

    // Validação de senha
    if ($senha === '') {
        $erros[] = 'A senha é obrigatória'; // MSG-05
    } elseif (mb_strlen($senha) < 6) {
        $erros[] = 'A senha deve ter no mínimo 6 caracteres'; // MSG-11
    } elseif (mb_strlen($senha) > 20) {
        $erros[] = 'A senha deve ter no máximo 20 caracteres'; // MSG-52
    } elseif (!preg_match('/[A-Z]/', $senha)) {
        $erros[] = 'A senha deve conter pelo menos 1 letra maiúscula'; // MSG-12
    } elseif (!preg_match('/[a-z]/', $senha)) {
        $erros[] = 'A senha deve conter pelo menos 1 letra minúscula'; // MSG-13
    } elseif (!preg_match('/\d/', $senha)) {
        $erros[] = 'A senha deve conter pelo menos 1 número'; // MSG-14
    } elseif ($senha !== $confirmar) {
        $erros[] = 'As senhas não coincidem'; // MSG-15
    }

    if (empty($erros)) {
        try {
            $stmt=$pdo->prepare("INSERT INTO administradores (nome,email,contato,senha) VALUES (?,?,?,?)");
            $stmt->execute([$nome,$email,$_POST['contato']??'',password_hash($senha,PASSWORD_DEFAULT)]);
            $msg='Cadastro realizado!';
        } catch(Exception $e){ $erros[] = 'Este e-mail já está cadastrado em outro brechó'; } // MSG-09
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8"><title>Cadastro Admin</title>
    <link rel="stylesheet" href="<?= url('/css/style.css') ?>">
    <style>
    :root{
      --primary-color:<?=e($config['cor_primaria'])?>;
      --secondary-color:<?=e($config['cor_secundaria'])?>;
    }
    </style>
  </head>
  <body>
    <a href="<?= url('/admin/login.php') ?>" class="back-link">← Voltar</a>

    <div class="auth-wrap">
      <?php if ($logoUrl = siteLogoUrl()): ?>
      <img src="<?= e($logoUrl) ?>" alt="<?= e($config['nome_site'] ?? 'Reuse Chic') ?>" class="auth-logo">
      <?php endif; ?>

      <form class="auth-card" method="post">
        <h1>SEJA BEM VINDO</h1><p class="sub">Realize o cadastro</p>
        <?php if($erros):?>
          <div class="alert alert-error"><?= e(implode('<br>', $erros)) ?></div>
        <?php elseif($msg):?>
          <div class="alert alert-success"><?=e($msg)?></div>
        <?php endif;?>
        <label>Nome Completo</label><input name="nome" value="<?= e($_POST['nome'] ?? '') ?>" required>
        <label>Email</label><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
        <label>Contato</label><input name="contato" value="<?= e($_POST['contato'] ?? '') ?>">
        <label>Senha <small style="opacity:.7">(6–20 car., maiúsc., minúsc. e número)</small></label>
        <input type="password" name="senha" required>
        <label>Confirmar Senha</label>
        <input type="password" name="confirmar_senha" required>
        <div class="links"><a href="<?= url('/admin/login.php') ?>" style="color:#fff">Já possui uma conta? Faça login.</a></div>
        <button class="btn btn-block">Cadastrar</button>
      </form>
    </div>
  </body>
</html>
