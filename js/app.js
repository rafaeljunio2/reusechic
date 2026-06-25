// ReuseChic - interações de front-end
document.addEventListener('DOMContentLoaded', () => {
  // Validação simples de formulários
  document.querySelectorAll('form[data-validate]').forEach(f => {
    f.addEventListener('submit', e => {
      const required = f.querySelectorAll('[required]');
      for (const el of required) {
        if (!el.value.trim()) {
          e.preventDefault();
          alert('Preencha todos os campos obrigatórios.');
          el.focus();
          return;
        }
      }
    });
  });

  // Confirmação de exclusão
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm || 'Tem certeza?')) e.preventDefault();
    });
  });

  // Máscara de telefone — (XX) XXXX-XXXX ou (XX) XXXXX-XXXX
  document.querySelectorAll('[data-mask="phone"]').forEach(input => {
    input.addEventListener('input', function () {
      let v = this.value.replace(/\D/g, '').slice(0, 11);
      if (v.length > 10) {
        v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
      } else if (v.length > 6) {
        v = v.replace(/^(\d{2})(\d{4,5})(\d{1,4})$/, '($1) $2-$3');
      } else if (v.length > 2) {
        v = v.replace(/^(\d{2})(\d+)$/, '($1) $2');
      } else if (v.length > 0) {
        v = '(' + v;
      }
      this.value = v;
    });
  });

  if (typeof iniciarCarrosselBanners === 'function') iniciarCarrosselBanners();
});
