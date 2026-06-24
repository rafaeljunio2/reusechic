<?php
/**
 * ============================================
 * CARD DE PRODUTO (COMPONENTE REUTILIZÁVEL)
 * ============================================
 * Usado em:
 *   - index.php      -> produtos em destaque
 *   - produto.php    -> produtos relacionados
 *
 * Antes de cada include deste arquivo, a página precisa
 * definir:
 *   $produto -> array associativo com, no mínimo:
 *               'id', 'nome', 'preco', 'tamanho'
 *               (opcional: 'estado', 'imagem_principal')
 */

$id = (int) ($produto['id'] ?? $id ?? 0);
$estado = $produto['estado'] ?? 'Ótima';
$img = uploadUrl($produto['imagem_principal'] ?? null);
?>
<article class="card-produto">

    <a href="<?= url('/produto.php') ?>?id=<?= $id ?>" class="card-produto__link">

        <?php if ($img): ?>
            <img class="card-produto__imagem" src="<?= e($img) ?>" alt="<?= e($produto['nome']) ?>">
        <?php else: ?>
            <!-- Placeholder quando não há foto cadastrada -->
            <div class="card-produto__imagem card-produto__imagem--placeholder" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 4h6l1 3h3a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h3l1-3Z"></path>
                    <circle cx="12" cy="13" r="3"></circle>
                </svg>
            </div>
        <?php endif; ?>

        <p class="card-produto__meta">
            Estado: <?= e($estado) ?> · Tamanho: <?= e($produto['tamanho']) ?>
        </p>

        <h3 class="card-produto__nome"><?= e($produto['nome']) ?></h3>

    </a>

    <form method="post" action="<?= url('/carrinho.php') ?>" class="card-produto__rodape">
        <span class="card-produto__preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
        <input type="hidden" name="acao" value="add">
        <input type="hidden" name="produto_id" value="<?= $id ?>">
        <button
            type="submit"
            class="card-produto__add"
            aria-label="Adicionar <?= e($produto['nome']) ?> ao carrinho"
        >🛒</button>
    </form>

</article>
