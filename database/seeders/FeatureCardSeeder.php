<?php

namespace Database\Seeders;

use App\Models\FeatureCard;
use Illuminate\Database\Seeder;

class FeatureCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $featureCards = [
            [
                'icon' => 'travellers',
                'title' => 'Happy Travellers Yearly',
                'headline' => '500+ Happy Travellers Yearly',
                'copy' => 'Expert travel designers crafting hand-picked itineraries and seamless logistics for every guest.',
                'count_value' => 500,
                'display_order' => 0,
                'is_active' => true,
            ],
            [
                'icon' => 'pricing',
                'title' => 'Best Price Guarantee',
                'headline' => null,
                'copy' => 'Preferred partner rates across lodges, charter flights, and experiences tailored to your budget.',
                'count_value' => null,
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'icon' => 'support',
                'title' => '24/7 Top-Notch Support',
                'headline' => null,
                'copy' => 'Dedicated concierge before, during, and after your safari—always a WhatsApp or call away.',
                'count_value' => null,
                'display_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($featureCards as $card) {
            FeatureCard::updateOrCreate(
                [
                    'icon' => $card['icon'],
                    'title' => $card['title'],
                ],
                $card
            );
        }
    }
}
