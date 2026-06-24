<?php require __DIR__.'/_layout.php';
$msg='';
if (isset($_GET['del'])) { $pdo->prepare("DELETE FROM banners WHERE id=?")->execute([(int)$_GET['del']]); $msg='Excluído.'; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $img = $_POST['img_existente']??'';
    if (!empty($_FILES['imagem']['name'])) {
        $saved = saveUpload($_FILES['imagem'], 'b_');
        if ($saved) $img = $saved;
    }
    $id=(int)($_POST['id']??0);
    if ($id) $pdo->prepare("UPDATE banners SET titulo=?,subtitulo=?,imagem=?,link=?,ativo=?,ordem=? WHERE id=?")
        ->execute([$_POST['titulo'],$_POST['subtitulo'],$img,$_POST['link'],(int)!empty($_POST['ativo']),(int)$_POST['ordem'],$id]);
    else $pdo->prepare("INSERT INTO banners (titulo,subtitulo,imagem,link,ativo,ordem) VALUES (?,?,?,?,?,?)")
        ->execute([$_POST['titulo'],$_POST['subtitulo'],$img,$_POST['link'],(int)!empty($_POST['ativo']),(int)$_POST['ordem']]);
    $msg='Salvo.';
}
$edit = isset($_GET['edit']) ? $pdo->query("SELECT * FROM banners WHERE id=".(int)$_GET['edit'])->fetch():null;
$banners = $pdo->query("SELECT * FROM banners ORDER BY ordem")->fetchAll();
?>
<h1>Banners / Carrossel</h1>
<?php if($msg):?><div class="alert alert-success"><?=e($msg)?></div><?php endif;?>
<div class="card" style="margin-bottom:20px">
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?=$edit['id']??''?>">
    <input type="hidden" name="img_existente" value="<?=$edit['imagem']??''?>">
    <div class="form-row"><label>Título</label><input name="titulo" value="<?=e($edit['titulo']??'')?>"></div>
    <div class="form-row"><label>Subtítulo</label><input name="subtitulo" value="<?=e($edit['subtitulo']??'')?>"></div>
    <div class="form-row"><label>Link</label><input name="link" value="<?=e($edit['link']??'')?>"></div>
    <div class="form-row"><label>Ordem</label><input type="number" name="ordem" value="<?=e($edit['ordem']??0)?>"></div>
    <div class="form-row"><label><input type="checkbox" name="ativo" <?=($edit['ativo']??1)?'checked':''?>> Ativo</label></div>
    <div class="form-row"><label>Imagem</label><input type="file" name="imagem" accept="image/*"></div>
    <button class="btn">Salvar</button>
  </form>
</div>
<table>
  <tr><th>Imagem</th><th>Título</th><th>Ordem</th><th>Ativo</th><th>Ações</th></tr>
  <?php foreach($banners as $b):?>
    <tr><td><?php if($b['imagem']):?><img src="<?= e(uploadUrl($b['imagem'])) ?>" style="width:80px"><?php endif;?></td>
    <td><?=e($b['titulo'])?></td><td><?=$b['ordem']?></td><td><?=$b['ativo']?'✅':'❌'?></td>
    <td><a href="?edit=<?=$b['id']?>">✏️</a> <a href="?del=<?=$b['id']?>" data-confirm="Excluir?">🗑️</a></td></tr>
  <?php endforeach;?>
</table>
<?php require __DIR__.'/_footer.php'; ?>
