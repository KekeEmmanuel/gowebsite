<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItinerarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imagePath = public_path('images/safari');
        
        // Get service types and destinations
        $safariTours = ServiceType::where('slug', 'safari-tours')->first();
        $kilimanjaro = ServiceType::where('slug', 'kilimanjaro-climbing')->first();
        $beachTours = ServiceType::where('slug', 'beach-island-tours')->first();
        
        $serengeti = Destination::where('slug', 'serengeti-national-park')->first();
        $ngorongoro = Destination::where('slug', 'ngorongoro-crater')->first();
        $kilimanjaroDest = Destination::where('slug', 'mount-kilimanjaro')->first();
        $zanzibar = Destination::where('slug', 'zanzibar-archipelago')->first();

        $itineraries = [
            [
                'title' => 'Classic Serengeti & Ngorongoro Safari',
                'slug' => 'classic-serengeti-ngorongoro-safari',
                'summary' => 'Experience the best of Tanzania\'s Northern Circuit with game drives in Serengeti and Ngorongoro Crater.',
                'badge' => 'Best Seller',
                'image_base64' => $this->getImageBase64($imagePath . '/wildlife-savannah.jpg'),
                'service_type_id' => $safariTours?->id,
                'destination_id' => $serengeti?->id,
                'duration_days' => 5,
                'price_from' => 2500.00,
                'difficulty' => 'Moderate',
                'highlights' => [
                    'Game drives in Serengeti National Park',
                    'Ngorongoro Crater exploration',
                    'Big Five wildlife viewing',
                    'Professional guide',
                ],
                'inclusions' => [
                    'Accommodation',
                    'All meals',
                    'Game drives',
                    'Park fees',
                    'Transportation',
                ],
                'exclusions' => [
                    'International flights',
                    'Travel insurance',
                    'Tips and gratuities',
                ],
                'tags' => ['safari', 'wildlife', 'big-five', 'serengeti'],
                'is_featured' => true,
                'display_order' => 0,
                'published_at' => now(),
            ],
            [
                'title' => 'Mount Kilimanjaro 7-Day Machame Route',
                'slug' => 'kilimanjaro-7-day-machame-route',
                'summary' => 'Summit Africa\'s highest peak via the scenic Machame Route with expert guides.',
                'badge' => 'Adventure',
                'image_base64' => $this->getImageBase64($imagePath . '/wildlife-zebra.jpg'),
                'service_type_id' => $kilimanjaro?->id,
                'destination_id' => $kilimanjaroDest?->id,
                'duration_days' => 7,
                'price_from' => 1800.00,
                'difficulty' => 'Challenging',
                'highlights' => [
                    'Summit Mount Kilimanjaro',
                    'Machame Route ascent',
                    'Expert mountain guides',
                    'Porters and support team',
                ],
                'inclusions' => [
                    'Mountain guides',
                    'Porters',
                    'Camping equipment',
                    'All meals on mountain',
                    'Park fees',
                ],
                'exclusions' => [
                    'International flights',
                    'Travel insurance',
                    'Personal gear',
                    'Tips',
                ],
                'tags' => ['kilimanjaro', 'climbing', 'adventure', 'trekking'],
                'is_featured' => true,
                'display_order' => 1,
                'published_at' => now(),
            ],
            [
                'title' => 'Zanzibar Beach & Spice Island Escape',
                'slug' => 'zanzibar-beach-spice-island-escape',
                'summary' => 'Relax on pristine beaches and explore the cultural heritage of Zanzibar.',
                'badge' => 'Relaxation',
                'image_base64' => $this->getImageBase64($imagePath . '/beach-1.jpg'),
                'service_type_id' => $beachTours?->id,
                'destination_id' => $zanzibar?->id,
                'duration_days' => 4,
                'price_from' => 1200.00,
                'difficulty' => 'Easy',
                'highlights' => [
                    'Beach relaxation',
                    'Spice farm tour',
                    'Stone Town exploration',
                    'Snorkeling',
                ],
                'inclusions' => [
                    'Beachfront accommodation',
                    'Breakfast',
                    'Spice tour',
                    'Stone Town tour',
                ],
                'exclusions' => [
                    'International flights',
                    'Lunch and dinner',
                    'Optional activities',
                ],
                'tags' => ['beach', 'zanzibar', 'relaxation', 'culture'],
                'is_featured' => true,
                'display_order' => 2,
                'published_at' => now(),
            ],
            [
                'title' => 'Ngorongoro Crater Day Trip',
                'slug' => 'ngorongoro-crater-day-trip',
                'summary' => 'A full-day safari in the Ngorongoro Crater, home to diverse wildlife.',
                'badge' => 'Day Trip',
                'image_base64' => $this->getImageBase64($imagePath . '/wildlife-herd.jpg'),
                'service_type_id' => $safariTours?->id,
                'destination_id' => $ngorongoro?->id,
                'duration_days' => 1,
                'price_from' => 350.00,
                'difficulty' => 'Easy',
                'highlights' => [
                    'Full day in Ngorongoro Crater',
                    'Big Five viewing',
                    'Picnic lunch',
                    'Professional guide',
                ],
                'inclusions' => [
                    'Park fees',
                    'Game drive',
                    'Lunch',
                    'Transportation',
                ],
                'exclusions' => [
                    'Accommodation',
                    'Tips',
                ],
                'tags' => ['safari', 'ngorongoro', 'day-trip', 'wildlife'],
                'is_featured' => false,
                'display_order' => 3,
                'published_at' => now(),
            ],
        ];

        foreach ($itineraries as $itinerary) {
            Itinerary::updateOrCreate(
                [
                    'slug' => $itinerary['slug'],
                ],
                $itinerary
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
        $mimeType = mime_content_type($filePath);
        
        return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
    }
}
