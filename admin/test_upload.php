<?php
require_once __DIR__.'/../php/config/init.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== DIAGNÓSTICO DE UPLOAD ===\n\n";

// 1. Paths
echo "1. CAMINHOS\n";
echo "   __DIR__       : " . __DIR__ . "\n";
echo "   uploadsPath() : " . uploadsPath() . "\n";
echo "   url('/uploads/'): " . url('/uploads/') . "\n";
echo "   BASE          : " . BASE . "\n\n";

// 2. Pasta uploads
$dir = uploadsPath();
echo "2. PASTA UPLOADS\n";
echo "   Existe       : " . (is_dir($dir) ? 'SIM' : 'NÃO') . "\n";
echo "   Gravável     : " . (is_writable($dir) ? 'SIM' : 'NÃO') . "\n";
echo "   Permissões   : " . decoct(fileperms($dir) & 0777) . "\n";
echo "   Dono         : " . posix_getpwuid(fileowner($dir))['name'] . "\n";
echo "   Usuário PHP  : " . get_current_user() . " / " . posix_getpwuid(posix_getuid())['name'] . "\n\n";

// 3. PHP upload config
echo "3. CONFIG PHP\n";
echo "   file_uploads        : " . ini_get('file_uploads') . "\n";
echo "   upload_max_filesize : " . ini_get('upload_max_filesize') . "\n";
echo "   post_max_size       : " . ini_get('post_max_size') . "\n";
echo "   upload_tmp_dir      : " . ini_get('upload_tmp_dir') . "\n\n";

// 4. Teste de escrita
echo "4. TESTE DE ESCRITA\n";
$testFile = $dir . 'test_write_' . time() . '.txt';
$ok = file_put_contents($testFile, 'ok');
if ($ok !== false) {
    echo "   Arquivo de teste criado: " . basename($testFile) . " ✓\n";
    unlink($testFile);
    echo "   Arquivo de teste removido ✓\n";
} else {
    echo "   FALHOU ao criar arquivo de teste ✗\n";
}
echo "\n";

// 5. Banco de dados
echo "5. BANCO DE DADOS\n";
try {
    $count = $pdo->query("SELECT COUNT(*) FROM produtos")->fetchColumn();
    echo "   Conexão OK - " . $count . " produto(s)\n";
    $logoRow = $pdo->query("SELECT valor FROM configuracoes WHERE chave='logo'")->fetch();
    echo "   Logo no banco: " . ($logoRow['valor'] ?? '(não definido)') . "\n";
} catch (Exception $e) {
    echo "   ERRO: " . $e->getMessage() . "\n";
}
echo "\n";

// 6. Produtos no banco
echo "6. PRODUTOS NO BANCO\n";
$rows = $pdo->query("SELECT id, nome, imagem_principal FROM produtos")->fetchAll();
foreach ($rows as $r) {
    $fn = $r['imagem_principal'];
    $exists = $fn ? file_exists(uploadsPath() . basename($fn)) : false;
    echo "   #" . $r['id'] . " " . $r['nome'] . "\n";
    echo "      imagem_principal: " . ($fn ?? 'NULL') . "\n";
    echo "      arquivo em disco: " . ($exists ? 'SIM ✓' : 'NÃO ✗') . "\n";
    echo "      URL gerada: " . (uploadUrl($fn) ?? '(null)') . "\n";
}
echo "\n";

// 7. Arquivos na pasta uploads
echo "7. ARQUIVOS EM UPLOADS\n";
$files = array_diff(scandir($dir), ['.', '..', '.gitkeep']);
if ($files) {
    foreach ($files as $f) {
        $path = $dir . $f;
        echo "   " . $f . " (" . filesize($path) . " bytes)\n";
    }
} else {
    echo "   (pasta vazia)\n";
}

// 8. Se houver POST de teste
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['test_file']['name'])) {
    echo "\n8. RESULTADO DO UPLOAD\n";
    echo "   Arquivo recebido : " . $_FILES['test_file']['name'] . "\n";
    echo "   Erro \$_FILES     : " . $_FILES['test_file']['error'] . "\n";
    $saved = saveUpload($_FILES['test_file'], 'teste_');
    echo "   saveUpload()     : " . var_export($saved, true) . "\n";
    echo "   Tipo do retorno  : " . gettype($saved) . "\n";
    if ($saved) {
        echo "   URL da imagem    : " . uploadUrl($saved) . "\n";
        echo "   Arquivo em disco : " . (file_exists(uploadsPath() . $saved) ? 'SIM ✓' : 'NÃO ✗') . "\n";
    }
}
?>

<hr>
<form method="post" enctype="multipart/form-data">
  <p>Teste de upload: <input type="file" name="test_file"> <button>Enviar</button></p>
</form>
