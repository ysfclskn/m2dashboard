# Dash4Game — Shared Linux Hosting Deployment Guide

## Prerequisites

- PHP 8.2+ with extensions: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath
- MySQL / MariaDB 5.7+
- Apache or LiteSpeed with mod_rewrite / `.htaccess` support

---

## Option A — SSH + Composer available

```bash
# 1. Upload project files to server (outside public_html)
#    e.g. /home/username/dash4game-app/

# 2. Create MySQL database from cPanel → MySQL Databases

# 3. Configure .env
cp .env.example .env
nano .env
# Set: APP_URL, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Install dependencies
composer install --no-dev --optimize-autoloader

# 5. Generate app key
php artisan key:generate

# 6. Run migrations and seed demo data
php artisan migrate --seed
# Or without demo data:
php artisan migrate

# 7. Cache config/routes/views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 9. Create storage symlink (for file uploads)
php artisan storage:link

# 10. Point document root to /public
#     In cPanel: Domains → document root → /home/user/dash4game-app/public
```

---

## Option B — FTP only, no SSH/Composer

Build everything locally first:

```bash
# On your local machine:
composer install --no-dev --optimize-autoloader
npm run build
php artisan key:generate
```

Then copy `.env.example` → `.env` and fill in your server's DB credentials.

**Upload structure:**

```
/home/username/
  dash4game-app/          ← upload everything EXCEPT public/ here
    app/
    bootstrap/
    config/
    database/
    resources/
    routes/
    storage/
    vendor/               ← upload the locally-built vendor folder
    .env                  ← upload with your server credentials filled in
    artisan
    composer.json
    ...

public_html/              ← upload contents of local public/ here
  index.php               ← adjust paths (see below)
  build/                  ← compiled Vite assets
  .htaccess
```

**Adjust `public_html/index.php`** — change two lines:

```php
// Find these two lines and update paths:
require __DIR__.'/../dash4game-app/vendor/autoload.php';
$app = require_once __DIR__.'/../dash4game-app/bootstrap/app.php';
```

**Import database manually via phpMyAdmin:**

```bash
# On local machine, export the schema:
php artisan migrate --pretend > schema.sql
# Or use mysqldump after running migrations locally:
mysqldump -u root dash4game_local > dash4game_export.sql
```

Import the SQL file through phpMyAdmin → Import tab.

Then insert the demo user manually if needed:

```sql
INSERT INTO users (name, email, password, created_at, updated_at)
VALUES ('Guild Master', 'demo@example.com',
        '$2y$12$...bcrypt_hash_of_password...', NOW(), NOW());
```

Or run seeders if artisan is available: `php artisan db:seed`

---

## Fallback: Hosting forces all files under public_html

If your host **cannot** change the document root, add this `.htaccess` to `public_html/`:

```apache
# Block sensitive Laravel directories
RedirectMatch 403 ^/(app|bootstrap|config|database|routes|storage|vendor|\.env)(/|$)

# Route everything through index.php
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

Place all Laravel files under `public_html/laravel/` and adjust `index.php`:

```php
require __DIR__.'/laravel/vendor/autoload.php';
$app = require_once __DIR__.'/laravel/bootstrap/app.php';
```

Add a `public_html/laravel/.htaccess` to deny all web access:

```apache
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Deny from all
</IfModule>
```

---

## File Permissions

```
chmod 644 .env
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Paths that must NEVER be web-accessible

- `.env`
- `vendor/`
- `storage/` (except `storage/app/public` via symlink)
- `bootstrap/`
- `app/`
- `database/`
- `routes/`
- `config/`

## Common Issues

| Issue | Fix |
|---|---|
| 500 error after upload | Check `storage/` and `bootstrap/cache/` are writable |
| "No application encryption key" | Run `php artisan key:generate` or set APP_KEY in `.env` manually |
| CSS/JS not loading | Ensure `public/build/` was uploaded; check `APP_URL` in `.env` |
| Database error | Verify DB credentials in `.env`; ensure MySQL user has privileges |
| Session not working | Set `SESSION_DRIVER=file` in `.env` |
| Cache issues | Set `CACHE_STORE=file` in `.env` |
| Migrations fail | Run `php artisan migrate:status` to check which ran |
| White page, no error | Set `APP_DEBUG=true` temporarily to see errors |

## Clearing Caches After Updates

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## Demo Login

- URL: `https://yourdomain.com`
- Email: `demo@example.com`
- Password: `password`
