#!/usr/bin/env bash
# Production deploy helper — run from the app root on the server.
set -euo pipefail

APP_DIR="${APP_DIR:-$(pwd)}"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
RELEASE="$(date +%Y%m%d%H%M%S)"

cd "$APP_DIR"

echo "==> Deploy $RELEASE"
git fetch --all --tags
PREVIOUS="$(git rev-parse HEAD)"

if [[ "${1:-}" == "rollback" ]]; then
  TARGET="${2:-}"
  if [[ -z "$TARGET" ]]; then
    echo "Usage: $0 rollback <git-ref>"
    exit 1
  fi
  git checkout "$TARGET"
  $COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction
  $PHP_BIN artisan migrate --force
  $PHP_BIN artisan optimize:clear
  $PHP_BIN artisan config:cache
  $PHP_BIN artisan route:cache
  $PHP_BIN artisan view:cache
  npm ci --omit=dev
  npm run build
  sudo supervisorctl restart all || true
  echo "Rolled back to $TARGET (was $PREVIOUS)"
  exit 0
fi

git pull --ff-only
$COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction
$PHP_BIN artisan down --retry=60 || true
$PHP_BIN artisan migrate --force
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan storage:link || true
npm ci --omit=dev
npm run build
$PHP_BIN artisan up
sudo supervisorctl restart all || true

echo "==> Smoke"
curl -fsS "http://127.0.0.1/up" >/dev/null || curl -fsS "${APP_URL:-http://127.0.0.1}/up" || true
echo "Deployed $PREVIOUS -> $(git rev-parse HEAD)"
