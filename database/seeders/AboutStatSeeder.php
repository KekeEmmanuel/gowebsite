<?php

namespace Database\Seeders;

use App\Models\AboutStat;
use Illuminate\Database\Seeder;

class AboutStatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stats = [
            [
                'value' => '18+',
                'label' => 'Years curating luxury safaris',
                'display_order' => 0,
                'is_active' => true,
            ],
            [
                'value' => '32',
                'label' => 'Handpicked camps & lodges',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'value' => '96%',
                'label' => 'Guest satisfaction rating',
                'display_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($stats as $stat) {
            AboutStat::updateOrCreate(
                [
                    'value' => $stat['value'],
                    'label' => $stat['label'],
                ],
                $stat
            );
        }
    }
}
