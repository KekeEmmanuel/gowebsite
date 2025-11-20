<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            [
                'name' => 'Safari Tours',
                'slug' => 'safari-tours',
                'summary' => 'Wildlife viewing and game drives in Tanzania\'s national parks and reserves.',
                'display_order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Kilimanjaro Climbing',
                'slug' => 'kilimanjaro-climbing',
                'summary' => 'Summit Africa\'s highest peak with expert guides and support teams.',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Beach & Island Tours',
                'slug' => 'beach-island-tours',
                'summary' => 'Relax on Zanzibar\'s pristine beaches and explore the Spice Islands.',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Cultural Tours',
                'slug' => 'cultural-tours',
                'summary' => 'Immerse yourself in Tanzania\'s rich culture and traditions.',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Adventure Tours',
                'slug' => 'adventure-tours',
                'summary' => 'Thrilling adventures including hiking, biking, and water sports.',
                'display_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($serviceTypes as $type) {
            ServiceType::updateOrCreate(
                [
                    'slug' => $type['slug'],
                ],
                $type
            );
        }
    }
}
