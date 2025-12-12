<?php

namespace Database\Seeders;

use App\Models\ContactChannel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = [
            [
                'label' => 'Call',
                'value' => '+255 (0) 742 123 456',
                'detail' => 'Daily 08:00 – 20:00 East Africa Time',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'label' => 'Email',
                'value' => 'bookings@gotanzaniasafari.com',
                'detail' => 'Expect a crafted itinerary within 24 hours',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'label' => 'WhatsApp',
                'value' => '+255 (0) 742 123 456',
                'detail' => 'Instant updates & on-trip assistance',
                'display_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($channels as $channel) {
            ContactChannel::updateOrCreate(
                ['label' => $channel['label']],
                $channel
            );
        }
    }
}
