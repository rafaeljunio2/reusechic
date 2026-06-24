<?php
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Cloudinary\Cloudinary;

function cloudinaryInstance(): Cloudinary {
    static $instance;
    if (!$instance) {
        // Lê automaticamente a env var CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
        $instance = new Cloudinary();
    }
    return $instance;
}

// Faz upload de um arquivo para o Cloudinary e retorna a URL segura (https://).
// Retorna null se o arquivo for inválido ou o upload falhar.
function cloudinaryUpload(array $file, string $folder = 'reusechic'): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
        return null;
    }
    try {
        $result = cloudinaryInstance()->uploadApi()->upload($file['tmp_name'], [
            'folder'        => $folder,
            'resource_type' => 'image',
        ]);
        return $result['secure_url'] ?? null;
    } catch (\Exception $e) {
        error_log('Cloudinary upload error: ' . $e->getMessage());
        return null;
    }
}
