# Deploy StorageSoftAI to https://isoverse.ai/storagesoftai

Use the **server MySQL** you already created. No local MySQL needed.

## 1. Prepare files on your computer

From this project (after `git pull`):

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

If you don’t have Composer/Node on your PC, use a machine that does, or ask hosting support / use cPanel Terminal if available.

## 2. Create `.env` on the server (not in git)

In File Manager, inside `public_html/storagesoftai/`, create `.env` with:

```env
APP_NAME=StorageSoftAI
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://isoverse.ai/storagesoftai

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mhrhsomq_storagesoftai
DB_USERNAME=YOUR_DB_USER
DB_PASSWORD=YOUR_DB_PASSWORD

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_PATH=/storagesoftai

CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@isoverse.ai"
MAIL_FROM_NAME="${APP_NAME}"

DEFAULT_FACILITY_SLUG=282-storage
```

Because the app runs **on the same server** as MySQL, `DB_HOST=localhost` is correct here.

## 3. Upload with cPanel File Manager or FTP

1. In cPanel → **File Manager** → go to `public_html`
2. Create folder: `storagesoftai`
3. Upload the **entire Laravel project** into `public_html/storagesoftai`  
   (folders like `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `vendor`, plus `artisan`, `composer.json`, etc.)
4. Copy `deploy/public_html-storagesoftai.htaccess` to  
   `~/isoverse.ai/storagesoftai/.htaccess`  
   (this sends traffic into Laravel’s `public/` folder; must skip paths already under `public/`)
5. Make writable (permissions **755** or **775**):
   - `storage`
   - `storage/*` subfolders
   - `bootstrap/cache`

Skip uploading: `.env` from your laptop if it has secrets mixed up — create the server `.env` as in step 2.  
Do **not** upload `node_modules`.

## 4. Finish setup in cPanel Terminal (or SSH)

```bash
cd ~/public_html/storagesoftai
php artisan key:generate
php artisan migrate --seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5. Open the site

- App: https://isoverse.ai/storagesoftai  
- Admin: https://isoverse.ai/storagesoftai/admin  
- Login: https://isoverse.ai/storagesoftai/login  

| Role | Email | Password |
|---|---|---|
| Owner | `owner@282storage.com` | `password` |
| Tenant | `tenant@282storage.com` | `password` |

**Change those passwords after first login.**

## Troubleshooting

| Problem | Fix |
|---|---|
| 500 error | Check `storage/logs/laravel.log`; confirm `storage` + `bootstrap/cache` writable |
| CSS missing | Confirm `public/build` was uploaded; `APP_URL` exact match |
| DB error | `DB_HOST=localhost`, correct db/user/pass from cPanel |
| Wrong links | `APP_URL=https://isoverse.ai/storagesoftai` (no trailing slash) |
