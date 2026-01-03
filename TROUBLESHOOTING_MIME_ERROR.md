# Troubleshooting mime_content_type() Error

## The Problem

When uploading images, you get:
```
Call to undefined function Spatie\ImageOptimizer\mime_content_type()
```

## Why This Happens

1. **Image Optimizer Package** calls `mime_content_type()` from within the `Spatie\ImageOptimizer` namespace
2. When code in a namespace calls a function without `\`, PHP looks in that namespace first
3. The `fileinfo` extension is not enabled on the server
4. Even with an empty optimizer array, the Conversion class still creates an OptimizerChain, which may try to check image types

## The Solution (Already Implemented)

We've implemented multiple layers of protection:

### 1. Polyfill in `bootstrap/mime-polyfill.php`
- Defines `mime_content_type()` in global namespace
- Defines `mime_content_type()` in `Spatie\ImageOptimizer` namespace using `eval()`
- Loaded early in `public/index.php` BEFORE Laravel bootstraps

### 2. Service Provider (`ImageOptimizerServiceProvider`)
- Ensures namespace function exists before any Image class is instantiated
- Registered in `bootstrap/providers.php`

### 3. Config Override (`AppServiceProvider::register()`)
- Sets `media-library.image_optimizers` to empty array when `fileinfo` is missing
- Runs early in the application lifecycle

### 4. Model-Level Protection
- `TourPackage` and `SafariPackage` skip conversions entirely when `fileinfo` is missing
- Prevents Conversion class from being instantiated

## Testing

1. **Upload `test-mime-polyfill.php` to `public_html`**
2. **Access it via browser**: `https://www.gotzsafari.com/test-mime-polyfill.php`
3. **Check each step** to see where it fails

## If It Still Fails

### Check 1: Is the polyfill being loaded?
- Check `public/index.php` - should have `require __DIR__.'/../bootstrap/mime-polyfill.php';`
- Check `bootstrap/mime-polyfill.php` exists

### Check 2: Is the service provider registered?
- Check `bootstrap/providers.php` - should include `App\Providers\ImageOptimizerServiceProvider::class`
- Check Laravel logs for provider errors

### Check 3: Are conversions being skipped?
- Check `app/Models/TourPackage.php` - should return early if `!extension_loaded('fileinfo')`
- Check if old conversions are cached

### Check 4: Is config cache cleared?
- Run: `php artisan config:clear`
- Delete `bootstrap/cache/config.php` manually if it exists

### Check 5: Are old media conversions cached?
- Clear media library cache if it exists
- Check if conversions were registered before the fix

## Alternative: Disable Conversions Completely

If the polyfill still doesn't work, you can disable conversions entirely:

```php
// In TourPackage.php and SafariPackage.php
public function registerMediaConversions(?Media $media = null): void
{
    // Always skip conversions if fileinfo is not available
    if (!extension_loaded('fileinfo')) {
        return;
    }
    
    // ... rest of code
}
```

This is already implemented, but make sure it's actually running on the server.

## Root Cause

The issue is that **even with an empty optimizer array**, the `Conversion` class constructor still calls:
```php
$this->manipulations->optimize($optimizerChain)->format('jpg');
```

And the `OptimizerChain` might still try to check if optimizers can handle the image, which creates an `Image` object, which calls `mime()` which calls `mime_content_type()` in the namespace.

The polyfill should handle this, but if it's not being loaded early enough or the namespace function isn't being found, the error persists.

## Final Solution

If all else fails, contact your hosting provider to enable the `fileinfo` PHP extension. This is the proper solution for production.

