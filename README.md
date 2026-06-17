# ReuseChic - Brechó Sustentável

Sistema completo de e-commerce para brechó, em **HTML5, CSS3, JavaScript, PHP e MySQL**.

## 📦 Estrutura

```
reusechic/
├── index.php             Home (vitrine pública)
├── inicial.php           Tela de escolha (Cliente / Admin)
├── login.php             Login do cliente
├── cadastro.php          Cadastro do cliente
├── recuperar.php         Recuperação de senha
├── catalogo.php          Catálogo com filtros e busca
├── produto.php           Detalhe do produto + WhatsApp
├── carrinho.php          Carrinho com sessão PHP
├── perfil.php            Perfil do cliente
├── logout.php
├── admin/                Painel administrativo
│   ├── login.php
│   ├── cadastro.php
│   ├── index.php         Dashboard
│   ├── produtos.php      CRUD de produtos + upload de imagens
│   ├── categorias.php    CRUD de categorias
│   ├── banners.php       CRUD do carrossel
│   ├── personalizar.php  Cores, logo, nome, WhatsApp...
│   ├── perfil.php
│   └── logout.php
├── css/style.css
├── js/app.js
├── php/
│   ├── config/db.php     Conexão PDO
│   ├── config/init.php   Sessão + helpers
│   └── includes/         Header e footer
├── uploads/              Imagens enviadas (chmod 775)
└── sql/reusechic.sql     Banco de dados
```

## 🚀 Como rodar (XAMPP / WAMP / Laragon)

1. Copie a pasta `reusechic/` para `htdocs/` (XAMPP) ou `www/` (WAMP).
2. Abra o **phpMyAdmin** → importe `sql/reusechic.sql`.
3. Edite `php/config/db.php` se seu MySQL tiver senha.
4. Acesse: `http://localhost/reusechic/`
5. Login admin padrão: `admin@reusechic.com` / `admin123`

> **⚠️ Senha do admin**: o hash inserido no SQL é exemplo. Se não funcionar,
> rode no PHP: `echo password_hash('admin123', PASSWORD_DEFAULT);`
> e substitua o valor da coluna `senha` em `administradores`.
> Ou cadastre um novo admin em `/admin/cadastro.php`.

## ✨ Funcionalidades

- ✅ Login/cadastro de clientes e administradores (PHP + sessões)
- ✅ Senhas com `password_hash` / `password_verify`
- ✅ PDO com **prepared statements** (proteção contra SQL Injection)
- ✅ CRUD completo de produtos, categorias e banners
- ✅ Upload de imagem principal + galeria múltipla
- ✅ Carrinho persistente em sessão PHP
- ✅ **Finalização via WhatsApp** com mensagem automática
- ✅ Catálogo com filtros (categoria, tamanho, preço) e ordenação
- ✅ **Personalização dinâmica**: cores, logo, nome, textos, WhatsApp — tudo do banco
- ✅ Variáveis CSS `--primary-color` e `--secondary-color` carregadas do banco
- ✅ Layout responsivo (mobile + desktop), tema rosa elegante
- ✅ Validações front-end (JS) e back-end (PHP)

## 🔐 Segurança

- PDO + prepared statements em todas as queries
- `password_hash` (BCRYPT) em todas as senhas
- `htmlspecialchars` em todas as saídas (função `e()`)
- Sessões PHP com checagem (`requireAdmin`, `requireCliente`)
- Uploads salvos com nomes únicos (`uniqid`)

## 📱 WhatsApp

Configure o número em **Admin → Personalizar → WhatsApp**
(formato internacional, ex: `5561999999999`).

A mensagem do carrinho é gerada automaticamente:
```
Olá, tenho interesse nos seguintes produtos:
- Blusa Floral Rosa (tam P) x1
- Blusa Lírio (tam M) x2
Total: R$ 76,90
```
