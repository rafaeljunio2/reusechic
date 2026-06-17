<?php require __DIR__.'/_layout.php';
$msg='';

// Excluir
if (isset($_GET['del'])) {
    $stmt=$pdo->prepare("DELETE FROM produtos WHERE id=?");
    $stmt->execute([(int)$_GET['del']]);
    $msg='Produto removido.';
}

// Salvar (criar/editar)
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id   = (int)($_POST['id']??0);
    $imgPrincipal = $_POST['imagem_existente'] ?? null;

    if (!empty($_FILES['imagem']['name'])) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $nome = uniqid('p_').'.'.$ext;
        move_uploaded_file($_FILES['imagem']['tmp_name'], __DIR__.'/../uploads/'.$nome);
        $imgPrincipal = $nome;
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE produtos SET nome=?,descricao=?,preco=?,tamanho=?,estado=?,estoque=?,status=?,categoria_id=?,imagem_principal=? WHERE id=?");
        $stmt->execute([$_POST['nome'],$_POST['descricao'],$_POST['preco'],$_POST['tamanho'],$_POST['estado'],$_POST['estoque'],$_POST['status'],$_POST['categoria_id']?:null,$imgPrincipal,$id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO produtos (nome,descricao,preco,tamanho,estado,estoque,status,categoria_id,imagem_principal) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['nome'],$_POST['descricao'],$_POST['preco'],$_POST['tamanho'],$_POST['estado'],$_POST['estoque'],$_POST['status'],$_POST['categoria_id']?:null,$imgPrincipal]);
        $id = $pdo->lastInsertId();
    }

    // galeria múltipla
    if (!empty($_FILES['galeria']['name'][0])) {
        foreach ($_FILES['galeria']['name'] as $k=>$nm) {
            $ext = pathinfo($nm, PATHINFO_EXTENSION);
            $fn = uniqid('g_').'.'.$ext;
            move_uploaded_file($_FILES['galeria']['tmp_name'][$k], __DIR__.'/../uploads/'.$fn);
            $pdo->prepare("INSERT INTO imagens_produtos (produto_id,caminho) VALUES (?,?)")->execute([$id,$fn]);
        }
    }
    $msg='Produto salvo!';
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt=$pdo->prepare("SELECT * FROM produtos WHERE id=?"); $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}

$cats = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
$produtos = $pdo->query("SELECT p.*, c.nome cat FROM produtos p LEFT JOIN categorias c ON c.id=p.categoria_id ORDER BY p.criado_em DESC")->fetchAll();
?>
<h1>Produtos</h1>
<?php if($msg):?><div class="alert alert-success"><?=e($msg)?></div><?php endif;?>

<div class="card" style="margin-bottom:20px">
  <h3><?= $edit?'Editar':'Novo' ?> produto</h3>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?=$edit['id']??''?>">
    <input type="hidden" name="imagem_existente" value="<?=$edit['imagem_principal']??''?>">
    <div class="form-row"><label>Nome</label><input name="nome" required value="<?=e($edit['nome']??'')?>"></div>
    <div class="form-row"><label>Descrição</label><textarea name="descricao"><?=e($edit['descricao']??'')?></textarea></div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
      <div class="form-row"><label>Preço</label><input type="number" step="0.01" name="preco" required value="<?=e($edit['preco']??'')?>"></div>
      <div class="form-row"><label>Tamanho</label><input name="tamanho" value="<?=e($edit['tamanho']??'')?>"></div>
      <div class="form-row"><label>Estado</label><input name="estado" value="<?=e($edit['estado']??'Ótima')?>"></div>
      <div class="form-row"><label>Estoque</label><input type="number" name="estoque" value="<?=e($edit['estoque']??1)?>"></div>
      <div class="form-row"><label>Status</label>
        <select name="status">
          <?php foreach(['disponivel','vendido','indisponivel'] as $s):?>
          <option value="<?=$s?>" <?=($edit['status']??'')===$s?'selected':''?>><?=$s?></option>
          <?php endforeach;?>
        </select>
      </div>
      <div class="form-row"><label>Categoria</label>
        <select name="categoria_id">
          <option value="">--</option>
          <?php foreach($cats as $c):?>
          <option value="<?=$c['id']?>" <?=($edit['categoria_id']??'')==$c['id']?'selected':''?>><?=e($c['nome'])?></option>
          <?php endforeach;?>
        </select>
      </div>
    </div>
    <div class="form-row"><label>Imagem principal</label><input type="file" name="imagem" accept="image/*"></div>
    <div class="form-row"><label>Galeria (várias)</label><input type="file" name="galeria[]" accept="image/*" multiple></div>
    <button class="btn">Salvar</button>
    <?php if($edit):?><a href="/admin/produtos.php" class="btn btn-outline">Cancelar</a><?php endif;?>
  </form>
</div>

<table>
  <tr><th>Imagem</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Status</th><th>Ações</th></tr>
  <?php foreach($produtos as $p):?>
    <tr>
      <td><?php if($p['imagem_principal']):?><img src="/uploads/<?=e($p['imagem_principal'])?>" style="width:50px;height:50px;object-fit:cover;border-radius:6px"><?php endif;?></td>
      <td><?=e($p['nome'])?></td>
      <td><?=e($p['cat']??'-')?></td>
      <td>R$ <?=number_format($p['preco'],2,',','.')?></td>
      <td><?=e($p['status'])?></td>
      <td>
        <a href="?edit=<?=$p['id']?>">✏️</a>
        <a href="?del=<?=$p['id']?>" data-confirm="Excluir produto?">🗑️</a>
      </td>
    </tr>
  <?php endforeach;?>
</table>
<?php require __DIR__.'/_footer.php'; ?>
