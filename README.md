# StorageSoftAI

Multi-tenant **Storage Management Software** (PHP + MySQL / Laravel) with:

- Facility public website (rent online, waitlist, CMS, blog)
- Admin ops (units, customers, payments, delinquency, reports, CMS)
- Tenant payment portal
- AI copilot (briefing, Q&A, pricing assist — confirm-before-write)
- SaaS packaging (plans, templates, trial signup)

**282 Storage** (`282storage.com`) is the reference production tenant / dogfood site.

## Stack

- PHP 8.2+
- MySQL 8 / MariaDB 10.3+
- Laravel 13
- Blade + Vite + Tailwind

Chosen for **shared hosting** compatibility (cPanel / Apache / PHP-FPM). Node is only needed to build front-end assets once.

## Local setup

```bash
cp .env.example .env
# set DB_* to your MySQL credentials
composer install
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

### Demo logins (after seed)

| Role | Email | Password |
|---|---|---|
| Owner (282 Storage) | `owner@282storage.com` | `password` |
| Tenant portal | `tenant@282storage.com` | `password` |
| Platform admin | `admin@storagesoftai.com` | `password` |

### Key URLs

- Facility site: `/`
- Platform marketing: `/app`
- Admin: `/admin`
- Tenant portal: `/portal`
- Login: `/login`

## Shared server deploy (cPanel / PHP + MySQL)

1. Create a MySQL database + user in cPanel.
2. Upload the project **above** the web root when possible, and point the domain document root to `/public`.
   - If you cannot change the docroot: upload contents of `public/` into `public_html/`, and place the rest of the Laravel app one level above (or adjust paths).
3. Set `public/.htaccess` (already included) and ensure `mod_rewrite` is on.
4. Copy `.env.example` → `.env` on the server and set:

```env
APP_NAME=StorageSoftAI
APP_ENV=production
APP_DEBUG=false
APP_URL=https://282storage.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

5. On SSH (or locally then upload `vendor/` + `public/build/`):

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --seed
npm ci && npm run build   # or upload prebuilt public/build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

6. Point **282storage.com** (and www) at this app. Facility resolution matches `facilities.custom_domain`.
7. Make writable: `storage/` and `bootstrap/cache/`.

### Apache note

Document root **must** be `public/` so application code is not web-accessible.

## Domain / multi-tenant notes

- Each facility can have `custom_domain` and/or `subdomain`.
- Local/dev without a matching host falls back to the `282-storage` facility.
- New subscribers sign up at `/app/signup` (14-day trial).

## Payments

Current payments use a **demo processor** (ledger updates only). Swap in a hosted-fields PCI processor (Stripe/Authorize.net/etc.) before production card capture.

## Requirements source

See `282storage-storable-easy-requirements.md` for discovery notes from Storable Easy / 282 Storage.
