<?php

namespace Database\Seeders;

use App\Models\AboutHighlight;
use Illuminate\Database\Seeder;

class AboutHighlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $highlights = [
            [
                'title' => 'Dedicated Journey Architect',
                'copy' => 'A single expert consultant curates, books, and monitors every detail from the first call to your return home.',
                'display_order' => 0,
                'is_active' => true,
            ],
            [
                'title' => 'Conservation First',
                'copy' => 'Partnerships with community conservancies, carbon offset initiatives, and low-impact travel practices.',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Seamless Logistics',
                'copy' => 'Private charters, VIP fast-track on arrival, and on-call concierges ensure your itinerary flows effortlessly.',
                'display_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($highlights as $highlight) {
            AboutHighlight::updateOrCreate(
                [
                    'title' => $highlight['title'],
                ],
                $highlight
            );
        }
    }
}
