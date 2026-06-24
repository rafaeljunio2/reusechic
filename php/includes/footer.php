</main>

<footer class="rodape-loja">
    <div class="rodape-loja__coluna">
        <h4>Endereço</h4>
        <p>
            Área Especial para Indústria<br>
            Lote nº 02, Setor Leste, Gama,<br>
            Brasília, DF - CEP 72.445-020
        </p>
    </div>
    <div class="rodape-loja__ilustracao" aria-hidden="true">
        <img src="./img/footer-bolsa.png" alt="" class="rodape-loja__ilustracao-item rodape-loja__ilustracao-item--bolsa">
        <img src="./img/footer-menina.png" alt="" class="rodape-loja__ilustracao-item rodape-loja__ilustracao-item--menina">
        <img src="./img/footer-sapatos.png" alt="" class="rodape-loja__ilustracao-item rodape-loja__ilustracao-item--sapatos">
    </div>
    <div class="rodape-loja__coluna">
        <h4>Pagamento</h4>
        <div class="rodape-loja__selos">
            <img src="./img/visa.png" alt="Visa">
            <img src="./img/mastercard.png" alt="Mastercard">
            <img src="./img/elo.png" alt="Elo">
            <img src="./img/pix.png" alt="Pix">
            <img src="./img/picpay.png" alt="PicPay">
            <img src="./img/mercadopago.png" alt="Mercado Pago">
        </div>
    </div>
    <div class="rodape-loja__coluna">
        <h4>Contatos</h4>
        <p>
            <?= e($config['email_contato'] ?? '') ?><br>
            <?= e(formatTelefone($config['whatsapp'] ?? '')) ?>
        </p>
    </div>
    <div class="rodape-loja__coluna">
        <h4>Redes Sociais</h4>
        <div class="rodape-loja__selos">
            <a href="https://wa.me/<?= e($telefone_whatsapp_loja) ?>" target="_blank" rel="noopener">
                <img src="./img/whatsapp.png" alt="WhatsApp">
            </a>
            <a href="#" target="_blank" rel="noopener">
                <img src="./img/instagram.png" alt="Instagram">
            </a>
            <a href="#" target="_blank" rel="noopener">
                <img src="./img/facebook.png" alt="Facebook">
            </a>
        </div>
    </div>
</footer>

<script src="<?= url('/js/app.js') ?>"></script>
<script src="<?= url('/js/carrosel.js') ?>"></script>
</body>
</html>
