<?php
require_once __DIR__.'/../php/config/init.php';
requireAdmin();

$msg = '';

// Excluir produto
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM produtos WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: ' . url('/admin/produtos.php') . '?msg=removido');
    exit;
}

// Excluir imagem da galeria
if (isset($_GET['del_img'])) {
    $imgId = (int)$_GET['del_img'];
    $row = $pdo->prepare("SELECT produto_id FROM imagens_produtos WHERE id=?");
    $row->execute([$imgId]);
    $prodId = (int)$row->fetchColumn();
    $pdo->prepare("DELETE FROM imagens_produtos WHERE id=?")->execute([$imgId]);
    header('Location: ' . url('/admin/produtos.php') . '?edit=' . $prodId);
    exit;
}

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'] === 'removido' ? 'Produto removido.' : '';
}

// Salvar (criar/editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $imgPrincipal = $_POST['imagem_existente'] ?? null;

    if (!empty($_FILES['imagem']['name'])) {
        $saved = saveUpload($_FILES['imagem'], 'p_');
        if ($saved) {
            $imgPrincipal = $saved;
        } else {
            $msg = 'Erro ao fazer upload da imagem principal. Verifique as configurações do Cloudinary.';
        }
    }

    if ($id) {
        $pdo->prepare("UPDATE produtos SET nome=?,descricao=?,preco=?,tamanho=?,estado=?,estoque=?,status=?,categoria_id=?,imagem_principal=? WHERE id=?")
            ->execute([$_POST['nome'],$_POST['descricao'],$_POST['preco'],$_POST['tamanho'],$_POST['estado'],$_POST['estoque'],$_POST['status'],$_POST['categoria_id']?:null,$imgPrincipal,$id]);
    } else {
        $pdo->prepare("INSERT INTO produtos (nome,descricao,preco,tamanho,estado,estoque,status,categoria_id,imagem_principal) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$_POST['nome'],$_POST['descricao'],$_POST['preco'],$_POST['tamanho'],$_POST['estado'],$_POST['estoque'],$_POST['status'],$_POST['categoria_id']?:null,$imgPrincipal]);
        $id = (int)$pdo->lastInsertId();
    }

    // Galeria múltipla
    if (!empty($_FILES['galeria']['name'][0])) {
        foreach ($_FILES['galeria']['name'] as $k => $nm) {
            if (!$nm) continue;
            $fn = saveUpload([
                'name'     => $nm,
                'tmp_name' => $_FILES['galeria']['tmp_name'][$k],
                'error'    => $_FILES['galeria']['error'][$k],
            ], 'g_');
            if ($fn) {
                $pdo->prepare("INSERT INTO imagens_produtos (produto_id,caminho) VALUES (?,?)")->execute([$id, $fn]);
            }
        }
    }

    if (!$msg) $msg = 'Produto salvo!';
}

$edit    = null;
$galeria = [];
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
    if ($edit) {
        $stmt = $pdo->prepare("SELECT * FROM imagens_produtos WHERE produto_id=? ORDER BY id");
        $stmt->execute([$edit['id']]);
        $galeria = $stmt->fetchAll();
    }
}

$cats     = $pdo->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
$produtos = $pdo->query("SELECT p.*, c.nome cat FROM produtos p LEFT JOIN categorias c ON c.id=p.categoria_id ORDER BY p.criado_em DESC")->fetchAll();

require __DIR__.'/_layout.php';
?>
<h1>Produtos</h1>
<?php if ($msg): ?>
  <div class="alert <?= str_contains($msg, 'Erro') ? 'alert-error' : 'alert-success' ?>"><?= e($msg) ?></div>
<?php endif; ?>

<div class="card" style="margin-bottom:20px">
  <h3><?= $edit ? 'Editar' : 'Novo' ?> produto</h3>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
    <input type="hidden" name="imagem_existente" value="<?= e($edit['imagem_principal'] ?? '') ?>">

    <div class="form-row"><label>Nome</label><input name="nome" required value="<?= e($edit['nome'] ?? '') ?>"></div>
    <div class="form-row"><label>Descrição</label><textarea name="descricao"><?= e($edit['descricao'] ?? '') ?></textarea></div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
      <div class="form-row"><label>Preço</label><input type="number" step="0.01" name="preco" required value="<?= e($edit['preco'] ?? '') ?>"></div>
      <div class="form-row"><label>Tamanho</label><input name="tamanho" value="<?= e($edit['tamanho'] ?? '') ?>"></div>
      <div class="form-row"><label>Estado</label><input name="estado" value="<?= e($edit['estado'] ?? 'Ótima') ?>"></div>
      <div class="form-row"><label>Estoque</label><input type="number" name="estoque" value="<?= e($edit['estoque'] ?? 1) ?>"></div>
      <div class="form-row"><label>Status</label>
        <select name="status">
          <?php foreach (['disponivel', 'vendido', 'indisponivel'] as $s): ?>
            <option value="<?= $s ?>" <?= ($edit['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row"><label>Categoria</label>
        <select name="categoria_id">
          <option value="">--</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($edit['categoria_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Imagem principal -->
    <div class="form-row">
      <label>Imagem principal</label>
      <?php if (!empty($edit['imagem_principal']) && $imgUrl = uploadUrl($edit['imagem_principal'])): ?>
        <div style="margin-bottom:8px">
          <img src="<?= e($imgUrl) ?>" style="height:80px;border-radius:6px;object-fit:cover;display:block;margin-bottom:4px">
          <small style="color:#888">Imagem atual — envie um novo arquivo para substituir</small>
        </div>
      <?php endif; ?>
      <input type="file" name="imagem" accept="image/*">
    </div>

    <!-- Galeria -->
    <div class="form-row">
      <label>Galeria (várias)</label>
      <?php if ($galeria): ?>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:8px">
          <?php foreach ($galeria as $g): ?>
            <div style="position:relative;display:inline-block">
              <img src="<?= e(uploadUrl($g['caminho'])) ?>" style="width:70px;height:70px;object-fit:cover;border-radius:6px">
              <a href="<?= url('/admin/produtos.php') ?>?del_img=<?= $g['id'] ?>&edit=<?= $edit['id'] ?>"
                 data-confirm="Remover esta imagem da galeria?"
                 style="position:absolute;top:-6px;right:-6px;background:#e53e3e;color:#fff;border-radius:50%;width:18px;height:18px;font-size:11px;display:flex;align-items:center;justify-content:center;text-decoration:none;line-height:1">✕</a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <input type="file" name="galeria[]" accept="image/*" multiple>
    </div>

    <button class="btn">Salvar</button>
    <?php if ($edit): ?>
      <a href="<?= url('/admin/produtos.php') ?>" class="btn btn-outline">Cancelar</a>
    <?php endif; ?>
  </form>
</div>

<table>
  <tr><th>Imagem</th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Status</th><th>Ações</th></tr>
  <?php foreach ($produtos as $p): ?>
    <tr>
      <td>
        <?php if (!empty($p['imagem_principal'])): ?>
          <img src="<?= e(uploadUrl($p['imagem_principal'])) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:6px">
        <?php endif; ?>
      </td>
      <td><?= e($p['nome']) ?></td>
      <td><?= e($p['cat'] ?? '-') ?></td>
      <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
      <td><?= e($p['status']) ?></td>
      <td>
        <a href="?edit=<?= $p['id'] ?>">✏️</a>
        <a href="<?= url('/admin/produtos.php') ?>?del=<?= $p['id'] ?>" data-confirm="Excluir produto?">🗑️</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<?php require __DIR__.'/_footer.php'; ?>
