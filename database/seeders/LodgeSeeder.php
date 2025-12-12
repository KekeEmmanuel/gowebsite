<?php

namespace Database\Seeders;

use App\Models\Lodge;
use Illuminate\Database\Seeder;

class LodgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imagePath = public_path('images/safari');
        
        $lodges = [
            [
                'name' => 'Serengeti Luxury Camp',
                'location' => 'Serengeti National Park',
                'mood' => 'Luxury',
                'image_base64' => $this->getImageBase64($imagePath . '/lodge-1.jpg'),
                'display_order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Ngorongoro Safari Lodge',
                'location' => 'Ngorongoro Conservation Area',
                'mood' => 'Comfortable',
                'image_base64' => $this->getImageBase64($imagePath . '/lodge-2.jpg'),
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Zanzibar Beach Resort',
                'location' => 'Zanzibar',
                'mood' => 'Relaxing',
                'image_base64' => $this->getImageBase64($imagePath . '/beach-2.jpg'),
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Tarangire Tented Camp',
                'location' => 'Tarangire National Park',
                'mood' => 'Authentic',
                'image_base64' => $this->getImageBase64($imagePath . '/beach-2.jpg'),
                'display_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($lodges as $lodge) {
            Lodge::updateOrCreate(
                [
                    'name' => $lodge['name'],
                    'location' => $lodge['location'],
                ],
                $lodge
            );
        }
    }

    /**
     * Get base64 encoded image from file path.
     */
    private function getImageBase64(string $filePath): ?string
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $imageData = file_get_contents($filePath);
        
        // Try to detect MIME type - use finfo if available, otherwise use file extension
        $mimeType = 'image/jpeg'; // default
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($filePath);
        } else {
            // Fallback to extension-based detection
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'image/jpeg';
        }
        
        return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
    }
}
