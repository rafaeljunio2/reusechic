# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

PHP 8+ (no framework), MySQL, vanilla HTML/CSS/JS. Runs under XAMPP/WAMP/Laragon via Apache at `http://localhost/reusechic/`.

## Running locally

1. Place this folder in `htdocs/` (XAMPP) or `www/` (WAMP).
2. Import `sql/reusechic.sql` via phpMyAdmin or `mysql -u root -p < sql/reusechic.sql`.
3. Start Apache + MySQL from the XAMPP control panel.
4. Visit `http://localhost/reusechic/`.
5. Default admin: `admin@reusechic.com` / `admin123`. If the hash doesn't work, regenerate it:
   ```
   php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
   ```
   then update the `senha` column in `administradores`, or create a new admin via `/admin/cadastro.php`.

Database credentials are in [php/config/db.php](php/config/db.php) (default: root / no password / database `reusechic`).

## Architecture

Every page bootstraps by including `php/config/init.php`, which:
- Starts the session
- Connects to MySQL via PDO (`$pdo`)
- Loads all rows from `configuracoes` into `$config` (site name, colors, logo, WhatsApp number, etc.)
- Defines global helpers: `e()` (htmlspecialchars), `isLoggedCliente()`, `isLoggedAdmin()`, `requireAdmin()`, `requireCliente()`

Dynamic theming works via CSS variables set inline in the `<head>` using `$config['cor_primaria']` and `$config['cor_secundaria']`. All site-wide settings (name, colors, logo, WhatsApp) are stored in the `configuracoes` table and managed via Admin → Personalizar.

### Front-end pages (`/`)

Each page is a self-contained PHP file that includes `php/includes/header.php` and `php/includes/footer.php`. No templating engine.

| Page | Purpose |
|------|---------|
| `index.php` | Public homepage (banners carousel, featured products) |
| `catalogo.php` | Product listing with filters (category, size, price) and search |
| `produto.php` | Product detail + WhatsApp checkout link |
| `carrinho.php` | Session-based cart + WhatsApp message generation |
| `perfil.php` | Customer profile (requires login) |
| `login.php` / `cadastro.php` / `recuperar.php` | Customer auth |

### Admin panel (`/admin/`)

All admin pages include `admin/_layout.php` (sidebar + auth guard via `requireAdmin()`), then close with `admin/_footer.php`. Each page is self-contained CRUD.

### Database schema

Key tables: `usuarios`, `administradores`, `categorias`, `produtos`, `imagens_produtos` (gallery), `carrinho`, `pedidos`, `configuracoes`, `banners`. See [sql/reusechic.sql](sql/reusechic.sql) for full schema.

Product images are stored in `uploads/` (must be `chmod 775`), saved with `uniqid()`-based filenames.

### Cart flow

Cart items live in `$_SESSION['carrinho']` as an array of `produto_id => quantidade`. Checkout generates a WhatsApp URL (`wa.me/{number}`) with a pre-filled message listing all items and total.

## Security conventions

- All DB queries use PDO prepared statements — never interpolate user input directly.
- All output goes through `e()` (`htmlspecialchars`).
- Passwords use `password_hash()` / `password_verify()` (BCRYPT).
- `.htaccess` blocks direct access to `.sql` and `.md` files.

## Front-end conventions

- [css/style.css](css/style.css): single stylesheet, uses CSS custom properties `--primary-color` and `--secondary-color` (set per-request from DB).
- [js/app.js](js/app.js): minimal vanilla JS — form validation (forms with `data-validate` attribute) and delete confirmations (`data-confirm` attribute).
