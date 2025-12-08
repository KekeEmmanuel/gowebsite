# Railway Performance Optimization Guide

## Changes Made

### 1. Removed Database Seeding on Every Start
- **Before**: `php artisan db:seed --force` ran on every container restart
- **After**: Removed from start command - only run migrations
- **Impact**: Faster startup times (seeding should only happen once during initial setup)

### 2. Added Multiple Workers
- **Before**: Single worker process
- **After**: `--workers=4` flag added to `php artisan serve`
- **Impact**: Can handle 4 concurrent requests instead of 1

### 3. Added Event Caching
- Added `php artisan event:cache` to build phase
- **Impact**: Faster event resolution

### 4. Optimized Vite Build
- Added minification with Terser
- Added code splitting for vendor chunks
- Removed console.log statements in production
- **Impact**: Smaller bundle sizes, faster asset loading

### 5. Enabled Response Caching
- Added ResponseCache middleware for production
- **Impact**: Cached responses for GET requests (7 days default)

## Railway Environment Variables to Set

Add these in your Railway dashboard under Variables:

```
APP_ENV=production
APP_DEBUG=false
CACHE_STORE=file
RESPONSE_CACHE_ENABLED=true
RESPONSE_CACHE_LIFETIME=604800
```

## Additional Recommendations

### 1. Use Redis for Caching (Optional but Recommended)
If you have Redis available on Railway:
```
CACHE_STORE=redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379
```

### 2. Database Connection Pooling
Consider using connection pooling if your database supports it.

### 3. CDN for Static Assets
Consider using a CDN (like Cloudflare) for serving static assets from `/public` directory.

### 4. Monitor Performance
- Check Railway logs for slow queries
- Use Laravel Telescope (if installed) to monitor queries
- Monitor response times in Railway dashboard

## Important Notes

- **Database Seeding**: If you need to seed data, do it manually via Railway CLI or create a separate deployment command
- **Workers**: The `--workers=4` flag may need adjustment based on your Railway plan's memory limits
- **OPcache**: PHP OPcache should be enabled by default on Railway, but verify in your PHP configuration

