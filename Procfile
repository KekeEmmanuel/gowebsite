web: bash -lc 'set -euo pipefail; \
  composer install --no-dev --prefer-dist --no-progress --no-interaction && \
  npm ci --no-audit --no-fund && \
  npm run build && \
  php artisan config:cache && \
  php artisan route:cache && \
  php artisan view:cache && \
  php artisan serve --host=0.0.0.0 --port=${PORT:-8080}'


