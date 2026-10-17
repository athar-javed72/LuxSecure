# LuxSecure backup plan

## What to back up

1. **Database** — listings, users, RBAC, bookings, CMS, settings, activity log  
2. **`storage/app/public`** — listing/agent uploads  
3. **`.env`** — secrets (store separately, encrypted; never in the same public archive)

## Automated command

```bash
php artisan luxsecure:backup
# or selectively:
php artisan luxsecure:backup --db
php artisan luxsecure:backup --storage
```

Artifacts land in `storage/app/backups/{timestamp}/`. Old runs are pruned per `BACKUP_KEEP` (default 7).

Schedule nightly (server cron):

```cron
0 2 * * * cd /path/to/LuxSecure && php artisan luxsecure:backup >> storage/logs/backup.log 2>&1
```

Or in `app/Console/Kernel.php`:

```php
$schedule->command('luxsecure:backup')->dailyAt('02:00');
```

## Production checklist

| Item | Recommendation |
|------|----------------|
| Cache | `CACHE_DRIVER=redis` (fallback: `database`) |
| Queue | `QUEUE_CONNECTION=database` or `redis` + `php artisan queue:work` |
| DB dumps | Prefer managed snapshots (RDS / hosting panel) in addition to `mysqldump` |
| Offsite | Sync `storage/app/backups` to S3 / Backblaze / another region |
| Restore drill | Quarterly: restore dump + storage zip into a staging env |

## Restore (MySQL)

```bash
mysql -u USER -p DATABASE < storage/app/backups/TIMESTAMP/database.sql
unzip storage/app/backups/TIMESTAMP/storage-public.zip -d storage/app/public
php artisan storage:link
php artisan migrate --force
php artisan config:cache
```

## Queue worker (queued mail)

```bash
php artisan queue:table   # once, if using database driver
php artisan migrate
php artisan queue:work --tries=3 --timeout=90
```

Use Supervisor / systemd in production so the worker restarts on failure.
