<?php require __DIR__.'/_layout.php';
$msg='';
if (isset($_GET['del'])) { $pdo->prepare("DELETE FROM categorias WHERE id=?")->execute([(int)$_GET['del']]); $msg='Excluída.'; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=(int)($_POST['id']??0); $nome=trim($_POST['nome']);
    if ($id) $pdo->prepare("UPDATE categorias SET nome=? WHERE id=?")->execute([$nome,$id]);
    else     $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)")->execute([$nome]);
    $msg='Salvo.';
}
$edit = isset($_GET['edit']) ? $pdo->query("SELECT * FROM categorias WHERE id=".(int)$_GET['edit'])->fetch() : null;
$cats = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
?>
<h1>Categorias</h1>
<?php if($msg):?><div class="alert alert-success"><?=e($msg)?></div><?php endif;?>
<div class="card" style="margin-bottom:20px;max-width:500px">
  <form method="post">
    <input type="hidden" name="id" value="<?=$edit['id']??''?>">
    <div class="form-row"><label>Nome</label><input name="nome" required value="<?=e($edit['nome']??'')?>"></div>
    <button class="btn"><?= $edit?'Atualizar':'Adicionar' ?></button>
  </form>
</div>
<table>
  <tr><th>Nome</th><th>Ações</th></tr>
  <?php foreach($cats as $c):?>
    <tr><td><?=e($c['nome'])?></td>
    <td><a href="?edit=<?=$c['id']?>">✏️</a> <a href="?del=<?=$c['id']?>" data-confirm="Excluir?">🗑️</a></td></tr>
  <?php endforeach;?>
</table>
<?php require __DIR__.'/_footer.php'; ?>
