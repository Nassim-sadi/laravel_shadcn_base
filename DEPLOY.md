# Deploy Flow

## Prerequisites

- PHP 8.3+
- Composer
- Node.js 20+
- MySQL/MariaDB database
- A web server (Apache/Nginx) pointed to `public/`
- Redis or database queue driver configured

## Environment Setup

```bash
cp .env.example .env
# Edit .env with your database credentials, app URL, mail settings
```

Required `.env` values:

```
APP_URL=https://example.com
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nsbase
DB_USERNAME=root
DB_PASSWORD=
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## One-Command Deploy

```bash
# 1. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 2. Install and build frontend
npm ci && npm run build

# 3. Set permissions (shared hosting)
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/build

# 4. Run migrations and seeders
php artisan migrate --force
php artisan db:seed --force

# 5. Create storage symlink
php artisan storage:link

# 6. Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Queue worker (if using queue driver)
# php artisan queue:restart
# php artisan queue:work --daemon &
```

## Zero-Downtime Deploy Script

Save as `deploy.sh` on your server:

```bash
#!/bin/bash
set -e

echo "Deploying NsBase..."

cd /var/www/nsbase

# Maintenance mode
php artisan down --retry=60

# Pull latest (if using git)
# git pull origin main

# Dependencies
composer install --no-dev --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build

# Database
php artisan migrate --force
php artisan db:seed --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage
php artisan storage:link

# Queue
php artisan queue:restart

# Exit maintenance mode
php artisan up

echo "Deploy complete!"
```

## Shared Hosting Notes

- **PHP version**: Ensure the server runs PHP 8.3+
- **Storage permissions**: `storage/` and `bootstrap/cache/` must be writable by the web server user
- **public/ symlink**: The `public/storage` symlink must point to `storage/app/public`
- **Cron**: Add Laravel scheduler to crontab:
  ```
  * * * * * cd /path/to/nsbase && php artisan schedule:run >> /dev/null 2>&1
  ```
- **Queue**: For shared hosting without queue workers, set `QUEUE_CONNECTION=sync` in `.env`
- **Sitemap**: The sitemap is generated dynamically at `/sitemap.xml` — no build step needed

## First-Time Setup Checklist

- [ ] Database created and credentials in `.env`
- [ ] `APP_KEY` generated (`php artisan key:generate`)
- [ ] Storage writable
- [ ] Public storage symlink created
- [ ] `.env` has production values (debug=false, https URL)
- [ ] Vite manifest cached in `public/build/`
- [ ] Admin user created (`php artisan db:seed --force` creates admin@test.com / password)

## Troubleshooting

| Problem | Likely Cause | Fix |
|---------|-------------|-----|
| Blank page | Storage permissions | `chmod -R 775 storage` |
| 404 on all routes | .htaccess missing or route cache stale | `php artisan route:clear` |
| Storage links broken | Symlink missing | `php artisan storage:link` |
| Vite assets not loading | Build missing | `npm run build` |
| Email not sending | SMTP config wrong | Check `.env` mail values |
