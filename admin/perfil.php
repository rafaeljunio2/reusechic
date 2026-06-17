<?php require __DIR__.'/_layout.php';
$msg='';
$stmt=$pdo->prepare("SELECT * FROM administradores WHERE id=?"); $stmt->execute([$_SESSION['admin_id']]); $a=$stmt->fetch();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $senha = $a['senha'];
    if (!empty($_POST['senha'])) $senha = password_hash($_POST['senha'],PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE administradores SET email=?,contato=?,senha=? WHERE id=?")
        ->execute([$_POST['email'],$_POST['contato'],$senha,$a['id']]);
    $msg='Atualizado!'; $stmt->execute([$a['id']]); $a=$stmt->fetch();
}
?>
<h1>Meu Perfil</h1>
<?php if($msg):?><div class="alert alert-success"><?=e($msg)?></div><?php endif;?>
<div class="card" style="max-width:500px">
  <form method="post">
    <div class="form-row"><label>E-mail</label><input type="email" name="email" value="<?=e($a['email'])?>" required></div>
    <div class="form-row"><label>Telefone</label><input name="contato" value="<?=e($a['contato'])?>"></div>
    <div class="form-row"><label>Nova senha (deixe em branco para manter)</label><input type="password" name="senha"></div>
    <button class="btn">Salvar</button>
    <a href="/admin/logout.php" class="btn btn-outline">Logout</a>
  </form>
</div>
<?php require __DIR__.'/_footer.php'; ?>
