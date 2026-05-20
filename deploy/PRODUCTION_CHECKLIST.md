# Production Deployment Checklist

## Pre-deployment

```bash
# 1. Set environment variables
cp .env.example .env
# Edit .env with production values:
# - APP_ENV=production
# - APP_DEBUG=false
# - APP_URL=https://yourdomain.com
# - APP_KEY= (generate with php artisan key:generate)
# - DB_CONNECTION=mysql (or pgsql)
# - DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
# - SESSION_SECURE_COOKIE=true
# - SESSION_DOMAIN=.yourdomain.com
# - SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
# - TRUSTED_PROXIES=* (if behind Cloudflare) or specific IPs
# - MAIL_* settings
# - API_RATE_LIMIT=60

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# 3. Generate app key
php artisan key:generate

# 4. Run migrations
php artisan migrate --force

# 5. Storage symlink
php artisan storage:link

# 6. Seed essential data (roles, permissions, settings)
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SettingSeeder

# 7. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Server Configuration

### Apache (.htaccess is already configured)
- Security headers (X-Frame-Options, CSP, etc.)
- HSTS (enable if using HTTPS)
- Compression and caching headers
- File protection

### Nginx (alternative)
```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self';" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known) {
        deny all;
    }
}
```

### Queue Worker (Supervisor)

```bash
# Install supervisor
apt install supervisor -y

# Copy config
cp deploy/supervisor/laravel-worker.conf /etc/supervisor/conf.d/

# Update path in the config file to match your deployment path
# Then:
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### Cron (for scheduled tasks)

```bash
# Add to crontab (crontab -e)
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

## Post-deployment

```bash
# Verify
php artisan optimize:clear
php artisan route:list
php artisan config:show

# Health check
curl -f https://yourdomain.com/api/up || echo "Health check failed"

# Verify queue worker
sudo supervisorctl status laravel-worker:*

# Verify storage permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Monitoring

- Set up log rotation for `storage/logs/`
- Monitor queue worker status
- Set up uptime monitoring for `/up` endpoint
- Consider error tracking (Sentry, Bugsnag)
- Set up database backups

## Rollback Plan

```bash
# If something goes wrong:
php artisan down
# Fix the issue
php artisan up

# Or rollback code:
git checkout <previous-commit>
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize
```
