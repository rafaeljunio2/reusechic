<?php require __DIR__.'/_layout.php';
$msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    foreach ($_POST['cfg'] as $chave=>$valor) {
        $pdo->prepare("INSERT INTO configuracoes (chave,valor) VALUES (?,?) ON DUPLICATE KEY UPDATE valor=VALUES(valor)")
            ->execute([$chave,$valor]);
    }
    if (!empty($_FILES['logo']['name'])) {
        $fn = saveUpload($_FILES['logo'], 'logo_');
        if ($fn) {
            $pdo->prepare("INSERT INTO configuracoes (chave,valor) VALUES ('logo',?) ON DUPLICATE KEY UPDATE valor=VALUES(valor)")
                ->execute([$fn]);
        } else {
            $msg = 'Erro ao fazer upload da logo. Verifique as configurações do Cloudinary.';
        }
    }
    if (!$msg) $msg = 'Configurações atualizadas! Recarregue a página.';
    // recarrega
    $config=[];
    foreach ($pdo->query("SELECT chave, valor FROM configuracoes") as $row) $config[$row['chave']]=$row['valor'];
}
?>
<h1>Personalização do Site</h1>
<?php if($msg):?><div class="alert <?= str_contains($msg, 'Erro') ? 'alert-error' : 'alert-success' ?>"><?=e($msg)?></div><?php endif;?>
<div class="card">
  <form method="post" enctype="multipart/form-data">
    <div class="form-row"><label>Nome do brechó</label><input name="cfg[nome_site]" value="<?=e($config['nome_site']??'')?>"></div>
    <div class="form-row"><label>Subtítulo</label><input name="cfg[subtitulo]" value="<?=e($config['subtitulo']??'')?>"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="form-row"><label>Cor primária</label><input type="color" name="cfg[cor_primaria]" value="<?=e($config['cor_primaria']??'#c98b7a')?>"></div>
      <div class="form-row"><label>Cor secundária</label><input type="color" name="cfg[cor_secundaria]" value="<?=e($config['cor_secundaria']??'#f6dcd5')?>"></div>
    </div>
    <div class="form-row"><label>WhatsApp (com DDI, ex: 5561999999999)</label><input name="cfg[whatsapp]" value="<?=e($config['whatsapp']??'')?>"></div>
    <div class="form-row"><label>E-mail de contato</label><input name="cfg[email_contato]" value="<?=e($config['email_contato']??'')?>"></div>
    <div class="form-row"><label>Endereço</label><input name="cfg[endereco]" value="<?=e($config['endereco']??'')?>"></div>
    <div class="form-row"><label>Título da Home</label><input name="cfg[titulo_home]" value="<?=e($config['titulo_home']??'')?>"></div>
    <div class="form-row"><label>Subtítulo da Home</label><input name="cfg[subtitulo_home]" value="<?=e($config['subtitulo_home']??'')?>"></div>
    <div class="form-row">
      <label>Logo</label>
      <?php if (!empty($config['logo']) && ($logoUrl = uploadUrl($config['logo']))): ?>
        <img src="<?= e($logoUrl) ?>" alt="Logo atual" style="display:block;max-height:60px;margin-bottom:8px">
      <?php endif; ?>
      <input type="file" name="logo" accept="image/*">
    </div>
    <button class="btn">Salvar</button>
  </form>
</div>
<?php require __DIR__.'/_footer.php'; ?>
