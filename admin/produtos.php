<?php
require_once __DIR__.'/../php/config/init.php';
requireAdmin();

$msg  = '';
$erros = [];

$TAMANHOS_VALIDOS = ['PP', 'P', 'M', 'G', 'GG', 'XG', 'Único'];
$ESTADOS_VALIDOS  = ['Novo com etiqueta', 'Excelente', 'Ótimo', 'Bom', 'Regular'];

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
    $id          = (int)($_POST['id'] ?? 0);
    $nome        = trim($_POST['nome'] ?? '');
    $descricao   = trim($_POST['descricao'] ?? '');
    $preco       = $_POST['preco'] ?? '';
    $tamanho     = trim($_POST['tamanho'] ?? '');
    $estado      = trim($_POST['estado'] ?? '');
    $estoque     = $_POST['estoque'] ?? '';
    $categoria_id = $_POST['categoria_id'] ?? '';
    $temImagem   = !empty($_FILES['imagem']['name']);
    $temImagemExistente = !empty($_POST['imagem_existente']);

    // Validação do nome
    if ($nome === '') {
        $erros[] = 'O nome do produto é obrigatório'; // MSG-22
    } elseif (mb_strlen($nome) < 5) {
        $erros[] = 'O nome do produto deve ter no mínimo 5 caracteres'; // MSG-23
    }

    // Validação do preço
    $precoFloat = is_numeric($preco) ? (float)$preco : -1;
    if ($precoFloat <= 0) {
        $erros[] = 'O preço deve ser maior que R$ 0,00'; // MSG-24
    } elseif ($precoFloat > 10000) {
        $erros[] = 'O preço não pode ultrapassar R$ 10.000,00'; // MSG-25
    }

    // Validação do estoque
    if ($estoque === '' || !is_numeric($estoque)) {
        $erros[] = 'A quantidade em estoque deve ser entre 0 e 10'; // MSG-26
    } elseif ((int)$estoque < 0 || (int)$estoque > 10) {
        $erros[] = 'A quantidade em estoque deve ser entre 0 e 10'; // MSG-26
    }

    // Validação da descrição
    if (mb_strlen($descricao) > 2000) {
        $erros[] = 'A descrição deve ter no máximo 2000 caracteres'; // MSG-27
    }

    // Validação da imagem (obrigatória apenas no cadastro)
    if (!$id && !$temImagem) {
        $erros[] = 'É necessário enviar pelo menos uma imagem do produto'; // MSG-28
    }

    // Validação da categoria
    if (empty($categoria_id)) {
        $erros[] = 'Selecione uma categoria válida'; // MSG-29
    } else {
        $stmtCat = $pdo->prepare("SELECT id FROM categorias WHERE id=?");
        $stmtCat->execute([(int)$categoria_id]);
        if (!$stmtCat->fetch()) $erros[] = 'Selecione uma categoria válida'; // MSG-29
    }

    // Validação do tamanho
    if (!in_array($tamanho, $TAMANHOS_VALIDOS, true)) {
        $erros[] = 'Selecione um tamanho válido (PP, P, M, G, GG, XG, Único)'; // MSG-30
    }

    // Validação do estado de conservação
    if (!in_array($estado, $ESTADOS_VALIDOS, true)) {
        $erros[] = 'Selecione o estado de conservação'; // MSG-31
    }

    if (empty($erros)) {
        $imgPrincipal = $_POST['imagem_existente'] ?? null;

        if ($temImagem) {
            // Validação do arquivo
            $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $erros[] = 'Formato não suportado. Use JPG ou PNG'; // MSG-32
            } elseif ($_FILES['imagem']['size'] > 2 * 1024 * 1024) {
                $erros[] = 'Imagem excede o limite de 2MB'; // MSG-33
            } else {
                $saved = saveUpload($_FILES['imagem'], 'p_');
                if ($saved) {
                    $imgPrincipal = $saved;
                } else {
                    $erros[] = 'Erro ao enviar imagem. Tente novamente'; // MSG-45
                }
            }
        }

        if (empty($erros)) {
            if ($id) {
                $pdo->prepare("UPDATE produtos SET nome=?,descricao=?,preco=?,tamanho=?,estado=?,estoque=?,status=?,categoria_id=?,imagem_principal=? WHERE id=?")
                    ->execute([$nome,$descricao,$precoFloat,$tamanho,$estado,(int)$estoque,$_POST['status'],(int)$categoria_id,$imgPrincipal,$id]);
            } else {
                $pdo->prepare("INSERT INTO produtos (nome,descricao,preco,tamanho,estado,estoque,status,categoria_id,imagem_principal) VALUES (?,?,?,?,?,?,?,?,?)")
                    ->execute([$nome,$descricao,$precoFloat,$tamanho,$estado,(int)$estoque,$_POST['status'],(int)$categoria_id,$imgPrincipal]);
                $id = (int)$pdo->lastInsertId();
            }

            // Galeria múltipla
            if (!empty($_FILES['galeria']['name'][0])) {
                // Conta imagens já existentes
                $countExist = (int)$pdo->prepare("SELECT COUNT(*) FROM imagens_produtos WHERE produto_id=?")->execute([$id]) ? 0 : 0;
                $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM imagens_produtos WHERE produto_id=?");
                $stmtCount->execute([$id]);
                $countExist = (int)$stmtCount->fetchColumn();

                foreach ($_FILES['galeria']['name'] as $k => $nm) {
                    if (!$nm) continue;
                    if ($countExist >= 5) {
                        $erros[] = 'Máximo de 5 imagens por produto'; // MSG-46
                        break;
                    }
                    $extG = strtolower(pathinfo($nm, PATHINFO_EXTENSION));
                    if (!in_array($extG, ['jpg', 'jpeg', 'png'])) {
                        $erros[] = 'Formato não suportado. Use JPG ou PNG'; // MSG-32
                        continue;
                    }
                    if ($_FILES['galeria']['size'][$k] > 2 * 1024 * 1024) {
                        $erros[] = 'Imagem excede o limite de 2MB'; // MSG-33
                        continue;
                    }
                    $fn = saveUpload([
                        'name'     => $nm,
                        'tmp_name' => $_FILES['galeria']['tmp_name'][$k],
                        'error'    => $_FILES['galeria']['error'][$k],
                    ], 'g_');
                    if ($fn) {
                        $pdo->prepare("INSERT INTO imagens_produtos (produto_id,caminho) VALUES (?,?)")->execute([$id, $fn]);
                        $countExist++;
                    }
                }
            }

            if (empty($erros)) $msg = 'Produto salvo!';
        }
    }
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
<?php if ($erros): ?>
  <div class="alert alert-error"><?= implode('<br>', array_map('e', $erros)) ?></div>
<?php elseif ($msg): ?>
  <div class="alert <?= str_contains($msg, 'Erro') ? 'alert-error' : 'alert-success' ?>"><?= e($msg) ?></div>
<?php endif; ?>

<div class="card" style="margin-bottom:20px">
  <h3><?= $edit ? 'Editar' : 'Novo' ?> produto</h3>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
    <input type="hidden" name="imagem_existente" value="<?= e($edit['imagem_principal'] ?? '') ?>">

    <div class="form-row"><label>Nome <small style="color:#888">(mín. 5 caracteres)</small></label>
      <input name="nome" required value="<?= e($edit['nome'] ?? '') ?>">
    </div>
    <div class="form-row"><label>Descrição <small style="color:#888">(máx. 2000 caracteres)</small></label>
      <textarea name="descricao" maxlength="2000"><?= e($edit['descricao'] ?? '') ?></textarea>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
      <div class="form-row"><label>Preço</label>
        <input type="number" step="0.01" min="0.01" max="10000" name="preco" required value="<?= e($edit['preco'] ?? '') ?>">
      </div>

      <div class="form-row"><label>Tamanho</label>
        <select name="tamanho" required>
          <option value="">-- Selecione --</option>
          <?php foreach (['PP', 'P', 'M', 'G', 'GG', 'XG', 'Único'] as $t): ?>
            <option value="<?= $t ?>" <?= ($edit['tamanho'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-row"><label>Estado de conservação</label>
        <select name="estado" required>
          <option value="">-- Selecione --</option>
          <?php foreach (['Novo com etiqueta', 'Excelente', 'Ótimo', 'Bom', 'Regular'] as $est): ?>
            <option value="<?= $est ?>" <?= ($edit['estado'] ?? '') === $est ? 'selected' : '' ?>><?= $est ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-row"><label>Estoque <small style="color:#888">(0–10)</small></label>
        <input type="number" name="estoque" min="0" max="10" value="<?= e($edit['estoque'] ?? 1) ?>">
      </div>

      <div class="form-row"><label>Status</label>
        <select name="status">
          <?php foreach (['disponivel', 'vendido', 'indisponivel'] as $s): ?>
            <option value="<?= $s ?>" <?= ($edit['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-row"><label>Categoria</label>
        <select name="categoria_id" required>
          <option value="">-- Selecione --</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($edit['categoria_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- Imagem principal -->
    <div class="form-row">
      <label>Imagem principal <?= !$edit ? '<small style="color:#e53e3e">*obrigatória</small>' : '' ?></label>
      <?php if (!empty($edit['imagem_principal']) && $imgUrl = uploadUrl($edit['imagem_principal'])): ?>
        <div style="margin-bottom:8px">
          <img src="<?= e($imgUrl) ?>" style="height:80px;border-radius:6px;object-fit:cover;display:block;margin-bottom:4px">
          <small style="color:#888">Imagem atual — envie um novo arquivo para substituir</small>
        </div>
      <?php endif; ?>
      <input type="file" name="imagem" accept="image/jpeg,image/png" <?= !$edit ? 'required' : '' ?>>
      <small style="color:#888">JPG ou PNG, máx. 2MB</small>
    </div>

    <!-- Galeria -->
    <div class="form-row">
      <label>Galeria (várias, máx. 5) <small style="color:#888">JPG ou PNG, máx. 2MB cada</small></label>
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
      <input type="file" name="galeria[]" accept="image/jpeg,image/png" multiple>
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
