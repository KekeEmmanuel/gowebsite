<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\TourPackage;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = TourPackage::all();

        if ($packages->isEmpty()) {
            $this->command->warn('No tour packages found. Please run TourPackageSeeder first.');
            return;
        }

        $bookings = [
            [
                'tour_package_slug' => 'ultimate-serengeti-adventure',
                'full_name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+1 (555) 123-4567',
                'whatsapp' => '+1 (555) 123-4567',
                'travel_date' => now()->addMonths(2)->format('Y-m-d'),
                'number_of_travelers' => 4,
                'customization_data' => [
                    'locations' => [
                        ['location' => 'Serengeti National Park', 'days' => 4],
                        ['location' => 'Ngorongoro Crater', 'days' => 2],
                        ['location' => 'Tarangire National Park', 'days' => 1],
                    ],
                    'total_days' => 7,
                    'special_preferences' => 'We prefer luxury tented camps with en-suite bathrooms. Interested in hot air balloon safari.',
                ],
                'special_requests' => 'We are celebrating our 20th wedding anniversary. Would love a special dinner arrangement if possible.',
                'status' => 'pending',
            ],
            [
                'tour_package_slug' => 'kilimanjaro-summit-expedition',
                'full_name' => 'Sarah Johnson',
                'email' => 'sarah.j@example.com',
                'phone' => '+44 20 7946 0958',
                'whatsapp' => '+44 20 7946 0958',
                'travel_date' => now()->addMonths(3)->format('Y-m-d'),
                'number_of_travelers' => 2,
                'customization_data' => [
                    'locations' => [
                        ['location' => 'Mount Kilimanjaro - Machame Route', 'days' => 7],
                        ['location' => 'Arusha', 'days' => 1],
                    ],
                    'total_days' => 8,
                    'special_preferences' => 'We are experienced hikers. Prefer faster pace if possible.',
                ],
                'special_requests' => 'Need vegetarian meal options throughout the trip.',
                'status' => 'pending',
            ],
            [
                'tour_package_slug' => 'zanzibar-paradise-escape',
                'full_name' => 'Michael Chen',
                'email' => 'michael.chen@example.com',
                'phone' => '+86 138 0013 8000',
                'whatsapp' => '+86 138 0013 8000',
                'travel_date' => now()->addMonths(1)->format('Y-m-d'),
                'number_of_travelers' => 6,
                'customization_data' => [
                    'locations' => [
                        ['location' => 'Stone Town', 'days' => 2],
                        ['location' => 'Nungwi Beach', 'days' => 3],
                    ],
                    'total_days' => 5,
                    'special_preferences' => 'Beachfront accommodation with private pool preferred.',
                ],
                'special_requests' => 'Family trip with 2 children (ages 8 and 12). Need family-friendly activities.',
                'status' => 'completed',
                'admin_notes' => 'Booking confirmed. All arrangements made. Client very satisfied.',
            ],
            [
                'tour_package_slug' => 'ngorongoro-crater-discovery',
                'full_name' => 'Emma Williams',
                'email' => 'emma.williams@example.com',
                'phone' => '+61 2 9374 4000',
                'whatsapp' => null,
                'travel_date' => now()->addMonths(4)->format('Y-m-d'),
                'number_of_travelers' => 2,
                'customization_data' => [
                    'locations' => [
                        ['location' => 'Ngorongoro Crater', 'days' => 3],
                        ['location' => 'Lake Manyara', 'days' => 1],
                    ],
                    'total_days' => 4,
                    'special_preferences' => 'Photography-focused safari. Prefer early morning and late afternoon game drives.',
                ],
                'special_requests' => 'We are professional wildlife photographers. Need vehicle with roof hatch for photography.',
                'status' => 'pending',
            ],
            [
                'tour_package_slug' => 'northern-circuit-safari-combo',
                'full_name' => 'David Brown',
                'email' => 'david.brown@example.com',
                'phone' => '+1 (555) 987-6543',
                'whatsapp' => '+1 (555) 987-6543',
                'travel_date' => now()->addMonths(5)->format('Y-m-d'),
                'number_of_travelers' => 8,
                'customization_data' => [
                    'locations' => [
                        ['location' => 'Tarangire National Park', 'days' => 2],
                        ['location' => 'Ngorongoro Crater', 'days' => 2],
                        ['location' => 'Serengeti National Park', 'days' => 5],
                        ['location' => 'Lake Manyara', 'days' => 1],
                    ],
                    'total_days' => 10,
                    'special_preferences' => 'Group of friends. Prefer mid-range accommodation with good food.',
                ],
                'special_requests' => 'Large group booking. Need 2 vehicles. Some members have dietary restrictions (gluten-free, vegetarian).',
                'status' => 'pending',
            ],
        ];

        foreach ($bookings as $bookingData) {
            $package = $packages->firstWhere('slug', $bookingData['tour_package_slug']);
            
            if (!$package) {
                continue;
            }

            $completedAt = null;
            if ($bookingData['status'] === 'completed') {
                $completedAt = now()->subDays(rand(1, 30));
            }

            Booking::create([
                'tour_package_id' => $package->id,
                'full_name' => $bookingData['full_name'],
                'email' => $bookingData['email'],
                'phone' => $bookingData['phone'],
                'whatsapp' => $bookingData['whatsapp'] ?? null,
                'travel_date' => $bookingData['travel_date'],
                'number_of_travelers' => $bookingData['number_of_travelers'],
                'customization_data' => $bookingData['customization_data'],
                'special_requests' => $bookingData['special_requests'],
                'status' => $bookingData['status'],
                'admin_notes' => $bookingData['admin_notes'] ?? null,
                'completed_at' => $completedAt,
                'created_at' => now()->subDays(rand(1, 60)),
            ]);
        }
    }
}
