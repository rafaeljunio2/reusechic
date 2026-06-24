<?php
require_once __DIR__.'/../php/config/init.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNÓSTICO DE UPLOAD (Cloudinary) ===\n\n";

// 1. Variável de ambiente
echo "1. CLOUDINARY_URL\n";
$url = getenv('CLOUDINARY_URL') ?: ($_ENV['CLOUDINARY_URL'] ?? null);
echo "   Definida: " . ($url ? 'SIM ✓' : 'NÃO ✗ — configure a env var CLOUDINARY_URL no Dokploy') . "\n\n";

// 2. Banco de dados
echo "2. BANCO DE DADOS\n";
try {
    $count = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
    echo "   Conexão OK — " . $count . " produto(s)\n";
    $logoRow = $pdo->query("SELECT valor FROM configuracoes WHERE chave='logo'")->fetch();
    echo "   Logo no banco: " . ($logoRow['valor'] ?? '(não definido)') . "\n";
} catch (Exception $e) {
    echo "   ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Produtos no banco
echo "3. IMAGENS DOS PRODUTOS\n";
$rows = $pdo->query("SELECT id, nome, imagem_principal FROM produtos")->fetchAll();
foreach ($rows as $r) {
    $fn = $r['imagem_principal'];
    $isCloudinary = $fn && (str_starts_with($fn, 'http://') || str_starts_with($fn, 'https://'));
    echo "   #" . $r['id'] . " " . $r['nome'] . "\n";
    echo "      imagem_principal: " . ($fn ?? 'NULL') . "\n";
    echo "      tipo: " . ($isCloudinary ? 'Cloudinary ✓' : ($fn ? 'legado (local)' : 'sem imagem')) . "\n";
    echo "      URL: " . (uploadUrl($fn) ?? '(null)') . "\n";
}
echo "\n";

// 4. Teste de upload para o Cloudinary
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['test_file']['name'])) {
    echo "4. RESULTADO DO UPLOAD\n";
    echo "   Arquivo recebido : " . $_FILES['test_file']['name'] . "\n";
    echo "   Erro \$_FILES     : " . $_FILES['test_file']['error'] . "\n";
    $saved = saveUpload($_FILES['test_file'], 'teste_');
    echo "   saveUpload()     : " . var_export($saved, true) . "\n";
    if ($saved) {
        echo "   URL Cloudinary   : " . $saved . " ✓\n";
    } else {
        echo "   FALHOU — verifique se CLOUDINARY_URL está definida corretamente\n";
    }
}
?>

<hr>
<form method="post" enctype="multipart/form-data">
  <p>Teste de upload para o Cloudinary: <input type="file" name="test_file" accept="image/*"> <button>Enviar</button></p>
</form>
