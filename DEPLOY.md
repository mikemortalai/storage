# Deploy StorageSoftAI to https://isoverse.ai/storagesoftai

Host path on this account: `~/isoverse.ai/storagesoftai`  
(Not `public_html`.)

## One-time file layout

Your Cyberduck upload of the full Laravel project into `isoverse.ai/storagesoftai` is correct.

Then add/replace these **two** files in that same folder (next to `app/`, `artisan`, `public/`):

1. **`.htaccess`** ← copy from `deploy/public_html-storagesoftai.htaccess`
2. **`index.php`** ← copy from `deploy/subdir-index.php`

Do **not** visit `/storagesoftai/public` in the browser. Use only:
https://isoverse.ai/storagesoftai

## `.env` (same folder)

```env
APP_NAME=StorageSoftAI
APP_ENV=production
APP_KEY=   # filled by php artisan key:generate
APP_DEBUG=false
APP_URL=https://isoverse.ai/storagesoftai

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mhrhsomq_storagesoftai
DB_USERNAME=YOUR_DB_USER
DB_PASSWORD=YOUR_DB_PASSWORD

SESSION_DRIVER=database
SESSION_PATH=/storagesoftai
CACHE_STORE=database
QUEUE_CONNECTION=database

DEFAULT_FACILITY_SLUG=282-storage
```

## Terminal setup

```bash
cd ~/isoverse.ai/storagesoftai

# Use PHP 8.3+ / 8.4 if `php -v` is still 8.2:
# /opt/cpanel/ea-php84/root/usr/bin/php artisan ...

php artisan key:generate
php artisan migrate --seed --force
php artisan config:clear
php artisan config:cache
```

If `composer` is not on the server, upload a local `vendor/` folder built with PHP 8.3+.

## Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

## Demo logins

| Role | Email | Password |
|---|---|---|
| Owner | `owner@282storage.com` | `password` |
| Tenant | `tenant@282storage.com` | `password` |

## If you still get 500

```bash
cat ~/isoverse.ai/storagesoftai/.htaccess
cat ~/isoverse.ai/storagesoftai/index.php
tail -50 ~/isoverse.ai/storagesoftai/error_log
tail -50 ~/isoverse.ai/storagesoftai/public/error_log
php -v
php artisan about
```
