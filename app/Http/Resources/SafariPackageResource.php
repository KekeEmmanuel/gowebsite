<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SafariPackageResource extends JsonResource
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
                $makeRelative = function ($url) use ($fallback, $media) {
                    if (empty($url)) return $fallback;
                    // If URL contains localhost or is absolute, extract the path
                    if (strpos($url, 'localhost') !== false || strpos($url, 'http') === 0) {
                        $parsed = parse_url($url);
                        $path = $parsed['path'] ?? $fallback;
                        // Ensure path starts with /
                        $path = (strpos($path, '/') === 0) ? $path : '/' . $path;
                        // Verify file exists in public storage
                        if ($media && strpos($path, '/storage/') === 0) {
                            $publicPath = public_path($path);
                            if (!file_exists($publicPath)) {
                                return $fallback;
                            }
                        }
                        return $path;
                    }
                    // Ensure relative URLs start with /
                    if (strpos($url, '/') !== 0) {
                        return '/' . $url;
                    }
                    // Verify file exists for storage paths
                    if ($media && strpos($url, '/storage/') === 0) {
                        $publicPath = public_path($url);
                        if (!file_exists($publicPath)) {
                            return $fallback;
                        }
                    }
                    return $url ?: $fallback;
                };
                
                return [
                    'url' => $makeRelative($heroUrl),
                    'thumb' => $makeRelative($thumbUrl),
                    'cover' => $makeRelative($coverUrl),
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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price_from' => $this->price_from,
            'duration_days' => $this->duration_days,
            'max_participants' => $this->max_participants,
            'is_featured' => $this->is_featured,
            'display_order' => $this->display_order,
            'published_at' => optional($this->published_at)->toISOString(),
            'hero_image' => $this->getHeroImage(),
            'gallery' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('gallery'),
                function () {
                    $fallback = '/images/safari/beach-1.jpg';
                    $makeRelative = function ($url) use ($fallback) {
                        if (empty($url)) return $fallback;
                        if (strpos($url, 'localhost') !== false || strpos($url, 'http') === 0) {
                            $parsed = parse_url($url);
                            $path = $parsed['path'] ?? $fallback;
                            return (strpos($path, '/') === 0) ? $path : '/' . $path;
                        }
                        if (strpos($url, '/') !== 0) {
                            return '/' . $url;
                        }
                        return $url ?: $fallback;
                    };
                    
                    return $this->getMedia('gallery')->map(function ($media) use ($makeRelative, $fallback) {
                        $url = $media->getUrl();
                        // Check if conversions exist, otherwise use original
                        $thumbUrl = $media->hasGeneratedConversion('thumb') 
                            ? $media->getUrl('thumb') 
                            : $url;
                        $coverUrl = $media->hasGeneratedConversion('cover') 
                            ? $media->getUrl('cover') 
                            : $url;
                        
                        // Helper to check file existence
                        $checkAndMakeRelative = function ($url) use ($makeRelative, $fallback, $media) {
                            $relative = $makeRelative($url);
                            // If it's a storage path, verify it exists
                            if (strpos($relative, '/storage/') === 0) {
                                $publicPath = public_path($relative);
                                return file_exists($publicPath) ? $relative : $fallback;
                            }
                            return $relative;
                        };
                        
                        return [
                            'id' => $media->id,
                            'url' => $checkAndMakeRelative($url),
                            'thumb' => $checkAndMakeRelative($thumbUrl),
                            'cover' => $checkAndMakeRelative($coverUrl),
                            'name' => $media->name,
                            'alt' => $media->getCustomProperty('alt'),
                        ];
                    });
                }
            ),
            'images' => $this->when(
                $this->relationLoaded('media') || $this->hasMedia('gallery'),
                function () {
                    $fallback = '/images/safari/beach-1.jpg';
                    $makeRelative = function ($url) use ($fallback) {
                        if (empty($url)) return $fallback;
                        if (strpos($url, 'localhost') !== false || strpos($url, 'http') === 0) {
                            $parsed = parse_url($url);
                            $path = $parsed['path'] ?? $fallback;
                            return (strpos($path, '/') === 0) ? $path : '/' . $path;
                        }
                        if (strpos($url, '/') !== 0) {
                            return '/' . $url;
                        }
                        return $url ?: $fallback;
                    };
                    
                    return $this->getMedia('gallery')->map(fn ($media) => $makeRelative($media->getUrl()));
                }
            ),
            'links' => [
                'self' => route('api.safari-packages.show', $this->slug),
            ],
        ];
    }
}
