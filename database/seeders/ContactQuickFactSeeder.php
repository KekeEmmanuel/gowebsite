<?php

namespace Database\Seeders;

use App\Models\ContactQuickFact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactQuickFactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facts = [
            [
                'fact' => 'Dedicated concierge from pre-trip briefing to touchdown back home.',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'fact' => 'Access to a private guest portal with live itinerary updates.',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'fact' => 'Emergency response network spanning Tanzania and Zanzibar.',
                'display_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($facts as $fact) {
            ContactQuickFact::updateOrCreate(
                ['fact' => $fact['fact']],
                $fact
            );
        }
    }
}
