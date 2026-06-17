</main>
<footer class="site-footer">
  <div>
    <h4>Endereço</h4>
    <p><?= e($config['endereco'] ?? '') ?></p>
  </div>
  <div>
    <h4>Pagamento</h4>
    <p>Visa · Mastercard · Elo · Pix · PicPay · Mercado Pago</p>
  </div>
  <div>
    <h4>Contatos</h4>
    <p><?= e($config['email_contato'] ?? '') ?><br><?= e($config['whatsapp'] ?? '') ?></p>
  </div>
  <div>
    <h4>Redes Sociais</h4>
    <p>WhatsApp · Instagram · Facebook</p>
  </div>
</footer>
<script src="/js/app.js"></script>
</body>
</html>
