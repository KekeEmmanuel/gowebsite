<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imagePath = public_path('images/safari');
        
        $destinations = [
            [
                'name' => 'Serengeti National Park',
                'slug' => 'serengeti-national-park',
                'region' => 'Northern Circuit',
                'teaser' => 'Great Migration',
                'description' => 'River-crossing drama, predator action, endless horizons under technicolour sunsets.',
                'image_base64' => $this->getImageBase64($imagePath . '/wildlife-savannah.jpg'),
                'is_featured' => true,
                'published_at' => now(),
            ],
            [
                'name' => 'Ngorongoro Crater',
                'slug' => 'ngorongoro-crater',
                'region' => 'Northern Circuit',
                'teaser' => 'World Heritage',
                'description' => 'A collapsed caldera teeming with BIG5 sightings, Maasai culture, and mist-draped mornings.',
                'image_base64' => $this->getImageBase64($imagePath . '/wildlife-herd.jpg'),
                'is_featured' => true,
                'published_at' => now(),
            ],
            [
                'name' => 'Mount Kilimanjaro',
                'slug' => 'mount-kilimanjaro',
                'region' => 'Northern Circuit',
                'teaser' => 'Summit Trek',
                'description' => 'Africa\'s rooftop crowned by glaciers, alpine desert moonscapes, and iconic Uhuru sunrise.',
                'image_base64' => $this->getImageBase64($imagePath . '/wildlife-zebra.jpg'),
                'is_featured' => true,
                'published_at' => now(),
            ],
            [
                'name' => 'Ruaha & Selous Reserves',
                'slug' => 'ruaha-selous-reserves',
                'region' => 'Southern Circuit',
                'teaser' => 'Southern Circuit',
                'description' => 'Remote fly-in safaris with boating on the Rufiji, walking trails, and off-grid luxury camps.',
                'image_base64' => $this->getImageBase64($imagePath . '/wildlife-giraffe.jpg'),
                'is_featured' => false,
                'published_at' => now(),
            ],
            [
                'name' => 'Zanzibar Archipelago',
                'slug' => 'zanzibar-archipelago',
                'region' => 'Coastal',
                'teaser' => 'Coastal Haven',
                'description' => 'From Matemwe reefs to Mnemba atoll, drift between spice farms and barefoot luxury hideaways.',
                'image_base64' => $this->getImageBase64($imagePath . '/beach-1.jpg'),
                'is_featured' => true,
                'published_at' => now(),
            ],
            [
                'name' => 'Tarangire National Park',
                'slug' => 'tarangire-national-park',
                'region' => 'Northern Circuit',
                'teaser' => 'Elephant Paradise',
                'description' => 'Home to large elephant herds and baobab trees dotting the savannah landscape.',
                'image_base64' => $this->getImageBase64($imagePath . '/wildlife-savannah.jpg'),
                'is_featured' => false,
                'published_at' => now(),
            ],
            [
                'name' => 'Lake Manyara National Park',
                'slug' => 'lake-manyara-national-park',
                'region' => 'Northern Circuit',
                'teaser' => 'Tree-Climbing Lions',
                'description' => 'Famous for tree-climbing lions, diverse birdlife, and the alkaline lake.',
                'image_base64' => $this->getImageBase64($imagePath . '/lions.jpg'),
                'is_featured' => false,
                'published_at' => now(),
            ],
        ];

        foreach ($destinations as $destination) {
            Destination::updateOrCreate(
                [
                    'slug' => $destination['slug'],
                ],
                $destination
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
