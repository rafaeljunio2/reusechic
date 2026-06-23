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

  iniciarCarrosselBanners();
});
