<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LodgeResource extends JsonResource
{
    /**
     * Get hero image with fallback
     */
    private function getHeroImage(): array
    {
        $fallback = '/images/safari/beach-1.jpg';
        
        if ($this->relationLoaded('media') || $this->hasMedia('hero')) {
            $media = $this->getFirstMedia('hero');
            if ($media) {
                $heroUrl = $media->getUrl();
                
                // Check if conversions exist, otherwise use original
                $thumbUrl = $media->hasGeneratedConversion('thumb') 
                    ? $media->getUrl('thumb') 
                    : $heroUrl;
                $coverUrl = $media->hasGeneratedConversion('cover') 
                    ? $media->getUrl('cover') 
                    : $heroUrl;
                
                // Convert absolute URLs to relative if they contain localhost
                $makeRelative = function ($url) use ($fallback) {
                    if (empty($url)) return $fallback;
                    // If URL contains localhost or is absolute, extract the path
                    if (strpos($url, 'localhost') !== false || strpos($url, 'http') === 0) {
                        $parsed = parse_url($url);
                        $path = $parsed['path'] ?? $fallback;
                        // Ensure path starts with /
                        return (strpos($path, '/') === 0) ? $path : '/' . $path;
                    }
                    // Ensure relative URLs start with /
                    if (strpos($url, '/') !== 0) {
                        return '/' . $url;
                    }
                    return $url ?: $fallback;
                };

                // Check if the file actually exists in storage before returning the URL
                $checkAndReturnUrl = function ($url, $originalUrl) use ($makeRelative, $fallback, $media) {
                    $relativePath = str_replace(env('APP_URL'), '', $url);
                    if (Storage::disk($media->disk)->exists(ltrim($relativePath, '/storage/'))) {
                        return $makeRelative($url);
                    }
                    // If conversion doesn't exist or file is missing, try original
                    $originalRelativePath = str_replace(env('APP_URL'), '', $originalUrl);
                    if (Storage::disk($media->disk)->exists(ltrim($originalRelativePath, '/storage/'))) {
                        return $makeRelative($originalUrl);
                    }
                    return $fallback;
                };
                
                return [
                    'url' => $checkAndReturnUrl($heroUrl, $heroUrl),
                    'thumb' => $checkAndReturnUrl($thumbUrl, $heroUrl),
                    'cover' => $checkAndReturnUrl($coverUrl, $heroUrl),
                ];
            }
        }
        
        return [
            'url' => $fallback,
            'thumb' => $fallback,
            'cover' => $fallback,
        ];
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fallbackImage = '/images/safari/beach-1.jpg';

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'location' => $this->location,
            'type' => $this->type,
            'mood' => $this->mood,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'amenities' => $this->amenities ?? [],
            'price_from' => $this->price_from,
            'capacity' => $this->capacity,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'published_at' => optional($this->published_at)->toISOString(),
            'hero_image' => $this->getHeroImage(),
            'gallery' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('gallery'),
                function () use ($fallbackImage) {
                    $makeRelative = function ($url) use ($fallbackImage) {
                        if (empty($url)) return $fallbackImage;
                        if (strpos($url, 'localhost') !== false || strpos($url, 'http') === 0) {
                            $parsed = parse_url($url);
                            $path = $parsed['path'] ?? $fallbackImage;
                            return (strpos($path, '/') === 0) ? $path : '/' . $path;
                        }
                        if (strpos($url, '/') !== 0) {
                            return '/' . $url;
                        }
                        return $url ?: $fallbackImage;
                    };
                    
                    return $this->getMedia('gallery')->map(function ($media) use ($makeRelative, $fallbackImage) {
                        $url = $media->getUrl();
                        // Check if conversions exist, otherwise use original
                        $thumbUrl = $media->hasGeneratedConversion('thumb') 
                            ? $media->getUrl('thumb') 
                            : $url;
                        $coverUrl = $media->hasGeneratedConversion('cover') 
                            ? $media->getUrl('cover') 
                            : $url;

                        $checkAndReturnUrl = function ($urlToCheck, $originalUrl) use ($makeRelative, $fallbackImage, $media) {
                            $relativePath = str_replace(env('APP_URL'), '', $urlToCheck);
                            if (Storage::disk($media->disk)->exists(ltrim($relativePath, '/storage/'))) {
                                return $makeRelative($urlToCheck);
                            }
                            $originalRelativePath = str_replace(env('APP_URL'), '', $originalUrl);
                            if (Storage::disk($media->disk)->exists(ltrim($originalRelativePath, '/storage/'))) {
                                return $makeRelative($originalUrl);
                            }
                            return $fallbackImage;
                        };
                        
                        return [
                            'id' => $media->id,
                            'url' => $checkAndReturnUrl($url, $url),
                            'thumb' => $checkAndReturnUrl($thumbUrl, $url),
                            'cover' => $checkAndReturnUrl($coverUrl, $url),
                            'name' => $media->name,
                            'alt' => $media->getCustomProperty('alt'),
                        ];
                    });
                }
            ),
            'image' => $this->getHeroImage()['url'], // For backward compatibility
            'links' => [
                'self' => $this->slug ? route('api.lodges.show', $this->slug) : null,
            ],
        ];
    }
}
