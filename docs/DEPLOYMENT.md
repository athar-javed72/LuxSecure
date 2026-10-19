# Production deployment runbook (Ubuntu)

Fresh Ubuntu 22.04/24.04 server → running Maskanoor Group (Laravel + Nginx + MySQL + Supervisor + cron).

## 1. System packages (PHP 8.4+)

```bash
sudo apt update
sudo apt install -y software-properties-common curl git unzip nginx mysql-server supervisor redis-server
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-xml php8.4-mbstring \
  php8.4-curl php8.4-zip php8.4-gd php8.4-intl php8.4-bcmath php8.4-redis
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
# Node 22 LTS (for Vite build on server, or build assets in CI and rsync)
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

## 2. Application user and code

```bash
sudo adduser --disabled-password --gecos "" deploy
sudo mkdir -p /var/www/maskanoor
sudo chown deploy:deploy /var/www/maskanoor
sudo -u deploy git clone <YOUR_REPO_URL> /var/www/maskanoor
cd /var/www/maskanoor
```

## 3. MySQL — limited app user

```bash
sudo mysql -e "CREATE DATABASE maskanoor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'maskanoor'@'127.0.0.1' IDENTIFIED BY 'STRONG_PASSWORD';"
sudo mysql -e "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, REFERENCES, CREATE TEMPORARY TABLES ON maskanoor.* TO 'maskanoor'@'127.0.0.1';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

## 4. `.env` production checklist

Copy `.env.example` → `.env` and set at least:

| Key | Expected |
|-----|----------|
| `APP_NAME` | Brand display name (also edit Admin → Settings) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://your-domain.tld` |
| `APP_KEY` | `php artisan key:generate` |
| `DB_*` | limited MySQL user above |
| `CACHE_DRIVER` / `QUEUE_CONNECTION` / `SESSION_DRIVER` | `redis` recommended |
| `MAIL_*` | real SMTP |
| `TRUSTED_PROXIES` | Nginx / LB IPs or CIDRs |
| `SESSION_SECURE_COOKIE` | `true` |
| Uploads / private disks | `UPLOADS_DISK`, `PRIVATE_DISK` |

Never commit `.env`.

```bash
cp .env.example .env
php artisan key:generate
```

## 5. Composer, migrate, seed reference data

```bash
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan db:seed --class=ReferenceDataSeeder --force
php artisan db:seed --class=RolesAndPermissionsSeeder --force
# Optional geo (place JSON under storage/app/geo — see docs/GEO_DATA.md):
# php artisan maskanoor:geo:import --country=PK,AE
php artisan storage:link
php artisan maskanoor:create-super-admin
```

Do **not** run demo seeders in production.

## 6. Frontend build

```bash
npm ci
npm run build
```

## 7. Nginx + Let's Encrypt

Example site (`/etc/nginx/sites-available/maskanoor`):

```nginx
server {
    listen 80;
    server_name your-domain.tld;
    root /var/www/maskanoor/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/maskanoor /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.tld
```

## 8. File permissions

```bash
sudo chown -R deploy:www-data /var/www/maskanoor
sudo find /var/www/maskanoor -type d -exec chmod 755 {} \;
sudo find /var/www/maskanoor -type f -exec chmod 644 {} \;
sudo chmod -R ug+rwx storage bootstrap/cache
```

## 9. Supervisor — queue worker

`/etc/supervisor/conf.d/maskanoor-worker.conf`:

```ini
[program:maskanoor-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/maskanoor/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=deploy
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/maskanoor/storage/logs/worker.log
```

```bash
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start maskanoor-worker:*
```

## 10. Cron — scheduler

```bash
sudo crontab -u deploy -e
```

Add:

```
* * * * * cd /var/www/maskanoor && php artisan schedule:run >> /dev/null 2>&1
```

Schedules (see `bootstrap/app.php`): `maskanoor:backup`, `maskanoor:notify-rent-due`, `maskanoor:expire-booking-holds`. Deprecated `luxsecure:*` aliases still work.

## 11. Backup and restore test

```bash
php artisan maskanoor:backup
# Confirm archive under storage/app/backups/
# Restore drill: copy dump to a scratch DB and verify row counts; restore a storage zip to a temp dir.
```

See also `docs/BACKUP.md`.

## 12. Deploy / rollback

```bash
chmod +x scripts/deploy.sh
./scripts/deploy.sh           # pull, migrate, rebuild, restart workers
./scripts/deploy.sh rollback <git-ref>
```

## 13. Post-deploy smoke checklist

- [ ] `GET /up` → JSON `status: ok` (database, cache, queue)
- [ ] Homepage / properties / contact render; CSP has no `unsafe-eval`
- [ ] Login as super admin; Admin → Settings shows brand + division names
- [ ] Country visibility readiness shows privacy/terms source (country or global)
- [ ] Queue: create a contact inquiry (mail/job) and confirm worker processes it
- [ ] `php artisan maskanoor:backup --storage` succeeds
- [ ] HTTPS certificate auto-renew (`sudo certbot renew --dry-run`)

## CI

GitHub Actions (`.github/workflows/ci.yml`): `pint --test`, `php artisan test`, `npm run build`.
