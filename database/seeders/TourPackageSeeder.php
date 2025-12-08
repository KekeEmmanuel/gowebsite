<?php

namespace Database\Seeders;

use App\Models\TourPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TourPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imagePath = public_path('images/safari');
        $safariPhotosPath = $imagePath . '/Safaris_Photo';

        $packages = [
            [
                'title' => 'Ultimate Serengeti Adventure',
                'slug' => 'ultimate-serengeti-adventure',
                'short_description' => 'Experience the Great Migration and witness the Big Five in their natural habitat.',
                'description' => '<p>Embark on an unforgettable journey through the Serengeti National Park, one of Africa\'s most iconic wildlife destinations. This comprehensive package takes you deep into the heart of the Serengeti ecosystem, where you\'ll witness the spectacular Great Migration, encounter the Big Five, and experience the raw beauty of the African savannah.</p>
                
                <p>Your adventure includes multiple game drives at different times of day, allowing you to see wildlife in various lighting conditions. From dawn to dusk, you\'ll track lions, elephants, leopards, rhinos, and buffalo, while also spotting cheetahs, hyenas, giraffes, zebras, and countless bird species.</p>
                
                <p>Accommodation is in carefully selected lodges and tented camps that offer comfort while maintaining an authentic safari experience. Expert guides with years of experience will share their knowledge of the ecosystem, animal behavior, and conservation efforts.</p>',
                'price_from' => 3200.00,
                'duration_days' => 7,
                'max_participants' => 12,
                'is_featured' => true,
                'display_order' => 0,
                'published_at' => now(),
                'hero_image' => $imagePath . '/wildlife-savannah.jpg',
                'gallery_images' => [
                    $safariPhotosPath . '/Classic Serengeti/bibhash-polygon-cafe-banerjee-2n3tcXLZjxg-unsplash.jpg',
                    $safariPhotosPath . '/Classic Serengeti/hu-chen-0LwfbRtQ-ac-unsplash.jpg',
                    $safariPhotosPath . '/Classic Serengeti/hu-chen-3yd8oXGoLqM-unsplash.jpg',
                    $safariPhotosPath . '/Classic Serengeti/magdalena-kula-manchee-gYURRvBM5JQ-unsplash.jpg',
                    $safariPhotosPath . '/Classic Serengeti/magdalena-kula-manchee-qGoGz1ui56Y-unsplash.jpg',
                ],
            ],
            [
                'title' => 'Kilimanjaro Summit Expedition',
                'slug' => 'kilimanjaro-summit-expedition',
                'short_description' => 'Conquer Africa\'s highest peak with expert guides and support team.',
                'description' => '<p>Challenge yourself to summit Mount Kilimanjaro, Africa\'s highest peak at 5,895 meters. This carefully designed expedition takes you through diverse ecosystems, from lush rainforests to alpine deserts, culminating in a breathtaking sunrise at Uhuru Peak.</p>
                
                <p>The Machame Route, known as the "Whiskey Route," offers stunning scenery and excellent acclimatization opportunities. You\'ll be supported by experienced guides, porters, and a comprehensive support team ensuring your safety and success.</p>
                
                <p>Each day brings new challenges and rewards as you ascend through different climate zones. The final summit push begins at midnight, allowing you to reach the peak at sunrise for spectacular views across Tanzania and Kenya.</p>',
                'price_from' => 2800.00,
                'duration_days' => 8,
                'max_participants' => 10,
                'is_featured' => true,
                'display_order' => 1,
                'published_at' => now(),
                'hero_image' => $imagePath . '/kilimanjaro.jpg',
                'gallery_images' => [
                    $safariPhotosPath . '/Mount Kilimanjaro/alessia-paggi-lijSWspkTXY-unsplash.jpg',
                    $safariPhotosPath . '/Mount Kilimanjaro/crispin-jones-ZulYpcsh2-w-unsplash.jpg',
                    $safariPhotosPath . '/Mount Kilimanjaro/harshil-gudka--2qN2QQwT8s-unsplash.jpg',
                    $safariPhotosPath . '/Mount Kilimanjaro/luis-mende-PD2Mp6ESqmM-unsplash.jpg',
                    $safariPhotosPath . '/Mount Kilimanjaro/stephan-bechert-1ZfMAnL4ubE-unsplash.jpg',
                ],
            ],
            [
                'title' => 'Ngorongoro Crater Discovery',
                'slug' => 'ngorongoro-crater-discovery',
                'short_description' => 'Explore the world\'s largest intact caldera and witness incredible wildlife density.',
                'description' => '<p>Descend into the Ngorongoro Crater, a UNESCO World Heritage Site and one of Africa\'s most spectacular natural wonders. This collapsed volcano creates a unique ecosystem where wildlife thrives in incredible density.</p>
                
                <p>The crater floor is home to over 25,000 large animals, including the Big Five. The high walls of the crater create a natural enclosure, making wildlife viewing exceptional. You\'ll have opportunities to see rare black rhinos, large prides of lions, massive elephant herds, and countless other species.</p>
                
                <p>In addition to wildlife, the crater offers stunning landscapes, from the soda lake to the Lerai Forest. Cultural interactions with the Maasai people add depth to your experience, as you learn about their traditional way of life.</p>',
                'price_from' => 1800.00,
                'duration_days' => 4,
                'max_participants' => 8,
                'is_featured' => true,
                'display_order' => 2,
                'published_at' => now(),
                'hero_image' => $imagePath . '/wildlife-herd.jpg',
                'gallery_images' => [
                    $safariPhotosPath . '/Ngorongoro/mariola-grobelska-eN4aDjsGcyI-unsplash.jpg',
                    $safariPhotosPath . '/Ngorongoro/mariola-grobelska-sLA88T3PyOU-unsplash.jpg',
                    $safariPhotosPath . '/Ngorongoro/mtsjrdl-xtQADtXZuXw-unsplash.jpg',
                    $safariPhotosPath . '/Ngorongoro/nichika-sakurai-dWkpjQ7drlg-unsplash.jpg',
                ],
            ],
            [
                'title' => 'Zanzibar Paradise Escape',
                'slug' => 'zanzibar-paradise-escape',
                'short_description' => 'Relax on pristine beaches, explore spice farms, and immerse in rich cultural heritage.',
                'description' => '<p>Escape to the tropical paradise of Zanzibar, where turquoise waters, white sand beaches, and rich cultural history await. This package combines relaxation with exploration, offering the perfect balance of beach time and cultural immersion.</p>
                
                <p>Spend your days lounging on pristine beaches, snorkeling in crystal-clear waters, and exploring the historic Stone Town, a UNESCO World Heritage Site. Visit spice farms to learn about Zanzibar\'s famous spice trade and sample fresh tropical fruits.</p>
                
                <p>Accommodation is in beachfront resorts offering luxury amenities while maintaining the island\'s authentic charm. Evening sunsets over the Indian Ocean provide the perfect backdrop for romantic dinners and relaxation.</p>',
                'price_from' => 1500.00,
                'duration_days' => 5,
                'max_participants' => 15,
                'is_featured' => true,
                'display_order' => 3,
                'published_at' => now(),
                'hero_image' => $imagePath . '/beach-1.jpg',
                'gallery_images' => [
                    $safariPhotosPath . '/Zanzibar/alexander-osipenko-VqxfYDgg8RQ-unsplash.jpg',
                    $safariPhotosPath . '/Zanzibar/humphrey-m-e6dRLBx6Kg8-unsplash.jpg',
                    $safariPhotosPath . '/Zanzibar/med-j-ePeTsqcyUiI-unsplash.jpg',
                    $safariPhotosPath . '/Zanzibar/nichika-sakurai-dWkpjQ7drlg-unsplash.jpg',
                    $safariPhotosPath . '/Zanzibar/patrick-mueller-J1aXF4Fe4j0-unsplash.jpg',
                ],
            ],
            [
                'title' => 'Northern Circuit Safari Combo',
                'slug' => 'northern-circuit-safari-combo',
                'short_description' => 'Combine Serengeti, Ngorongoro, and Tarangire for the ultimate wildlife experience.',
                'description' => '<p>Experience the best of Tanzania\'s Northern Circuit in one comprehensive package. This multi-park safari takes you through Serengeti, Ngorongoro Crater, and Tarangire National Park, each offering unique wildlife viewing opportunities.</p>
                
                <p>Start in Tarangire, famous for its large elephant herds and ancient baobab trees. Then journey to the Ngorongoro Crater for incredible Big Five sightings. Finally, explore the vast Serengeti plains, home to the Great Migration and abundant predators.</p>
                
                <p>This package is perfect for first-time visitors to Tanzania who want to see the diversity of the Northern Circuit. Expert guides will help you understand the differences between each ecosystem and maximize your wildlife viewing opportunities.</p>',
                'price_from' => 4200.00,
                'duration_days' => 10,
                'max_participants' => 12,
                'is_featured' => false,
                'display_order' => 4,
                'published_at' => now(),
                'hero_image' => $imagePath . '/wildlife-savannah.jpg',
                'gallery_images' => [
                    $imagePath . '/wildlife-herd.jpg',
                    $imagePath . '/wildlife-giraffe.jpg',
                    $imagePath . '/wildlife-zebra.jpg',
                    $imagePath . '/lions.jpg',
                ],
            ],
        ];

        foreach ($packages as $packageData) {
            // Extract gallery images before creating the package
            $galleryImages = $packageData['gallery_images'] ?? [];
            $heroImage = $packageData['hero_image'] ?? null;
            
            // Remove images from package data
            unset($packageData['gallery_images'], $packageData['hero_image']);

            // Create or update the package
            $package = TourPackage::updateOrCreate(
                ['slug' => $packageData['slug']],
                $packageData
            );

            // Add hero image
            if ($heroImage && file_exists($heroImage)) {
                // Clear existing hero images
                $package->clearMediaCollection('hero');
                $package->addMedia($heroImage)
                    ->preservingOriginal()
                    ->withCustomProperties(['alt' => $package->title])
                    ->toMediaCollection('hero');
            }

            // Add gallery images
            if (!empty($galleryImages)) {
                // Clear existing gallery
                $package->clearMediaCollection('gallery');
                
                foreach ($galleryImages as $index => $imagePath) {
                    if (file_exists($imagePath)) {
                        $package->addMedia($imagePath)
                            ->preservingOriginal()
                            ->withCustomProperties([
                                'alt' => $package->title . ' - Image ' . ($index + 1),
                            ])
                            ->toMediaCollection('gallery');
                    }
                }
            }
        }
    }
}
