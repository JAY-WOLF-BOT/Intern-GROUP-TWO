<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Listing;
use App\Models\Photo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with sample data.
     */
    public function run(): void
    {
        // Create demo landlord
        $landlord = User::firstOrCreate(
            ['email' => 'landlord@demo.com'],
            [
                'name' => 'Kofi Mensah Properties',
                'email' => 'landlord@demo.com',
                'phone_number' => '+233501234567',
                'password' => Hash::make('password123'),
                'role' => 'landlord',
            ]
        );

        // Create demo tenant
        $tenant = User::firstOrCreate(
            ['email' => 'tenant@demo.com'],
            [
                'name' => 'Ama Boateng',
                'email' => 'tenant@demo.com',
                'phone_number' => '+233551234567',
                'password' => Hash::make('password123'),
                'role' => 'tenant',
            ]
        );

        // Sample listings data
        $listings = [
            [
                'title' => 'Luxury 2-Bedroom Apartment in Osu',
                'description' => 'Beautiful and spacious 2-bedroom apartment in the heart of Osu. Modern kitchen, furnished, free WiFi, 24-hour security, and parking included. Perfect for young professionals. Close to restaurants and shopping.',
                'price' => 1500,
                'deposit' => 3000,
                'bedrooms' => 2,
                'bathrooms' => 1,
                'area_sqft' => 1200,
                'property_type' => 'apartment',
                'neighborhood' => 'Osu',
                'furnished' => true,
                'wifi' => true,
                'parking' => true,
                'security' => true,
                'pool' => false,
                'gym' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1494145904049-0dca7b3c3e4d?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800&h=600&fit=crop',
                ],
            ],
            [
                'title' => 'Cozy Studio Apartment in Accra Mall Area',
                'description' => 'Small but comfortable studio apartment close to Accra Mall. Fully furnished with AC, cable TV, water tank, and hot shower. Great location for individuals or couples. Walking distance to mall and transportation.',
                'price' => 800,
                'deposit' => 1600,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'area_sqft' => 600,
                'property_type' => 'studio',
                'neighborhood' => 'Accra Mall',
                'furnished' => true,
                'wifi' => false,
                'parking' => true,
                'security' => true,
                'pool' => false,
                'gym' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1493246507139-91e8fad9978e?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop',
                ],
            ],
            [
                'title' => 'Spacious 3-Bedroom House in Tema',
                'description' => 'Newly renovated 3-bedroom house with modern amenities. Large garden, excellent natural lighting, tiled floors, and very close to schools and market. Perfect for families. Built with quality materials.',
                'price' => 2500,
                'deposit' => 5000,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area_sqft' => 1800,
                'property_type' => 'house',
                'neighborhood' => 'Tema',
                'furnished' => true,
                'wifi' => true,
                'parking' => true,
                'security' => true,
                'pool' => false,
                'gym' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800&h=600&fit=crop',
                ],
            ],
            [
                'title' => 'Shared Room in Legon - Student Friendly',
                'description' => 'Affordable shared room suitable for university students. Clean, secure environment with friendly housemates. Water and electricity included in rent. Common area for studying and relaxation. Close to KNUST campus.',
                'price' => 350,
                'deposit' => 700,
                'bedrooms' => 0,
                'bathrooms' => 1,
                'area_sqft' => 300,
                'property_type' => 'shared_room',
                'neighborhood' => 'Legon',
                'furnished' => true,
                'wifi' => true,
                'parking' => false,
                'security' => true,
                'pool' => false,
                'gym' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=600&fit=crop',
                ],
            ],
            [
                'title' => 'Elegant 2-Bedroom in Cantonments',
                'description' => 'Premium 2-bedroom apartment in upscale Cantonments. Italian kitchen, marble floors, gym access, rooftop view, and swimming pool. Ideal for executives and business professionals. Secure gated community.',
                'price' => 2200,
                'deposit' => 4400,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'area_sqft' => 1400,
                'property_type' => 'apartment',
                'neighborhood' => 'Cantonments',
                'furnished' => true,
                'wifi' => true,
                'parking' => true,
                'security' => true,
                'pool' => true,
                'gym' => true,
                'images' => [
                    'https://images.unsplash.com/photo-1501672260648-cf0ee12f1ef3?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop',
                ],
            ],
            [
                'title' => 'Beautiful Bungalow with Garden in East Legon',
                'description' => 'Charming bungalow with spacious garden and outdoor patio. 3 bedrooms, 2 bathrooms, modern design with separate dining area. Close to schools, shopping centers, and major highways. Family-friendly neighborhood.',
                'price' => 3000,
                'deposit' => 6000,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'area_sqft' => 2000,
                'property_type' => 'bungalow',
                'neighborhood' => 'East Legon',
                'furnished' => true,
                'wifi' => true,
                'parking' => true,
                'security' => true,
                'pool' => false,
                'gym' => false,
                'images' => [
                    'https://images.unsplash.com/photo-1570129477492-45201b8d7e75?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&h=600&fit=crop',
                ],
            ],
        ];

        // Create listings with photos
        foreach ($listings as $listingData) {
            $images = $listingData['images'];
            unset($listingData['images']);

            $listing = Listing::firstOrCreate(
                ['title' => $listingData['title']],
                [
                    ...$listingData,
                    'landlord_id' => $landlord->id,
                    'verification_status' => 'approved',
                    'location_address' => $listingData['neighborhood'] . ', Accra',
                    'location_lat' => 5.6037 + (rand(-100, 100) / 10000),
                    'location_long' => -0.1870 + (rand(-100, 100) / 10000),
                    'view_count' => rand(10, 100),
                ]
            );

            // Add photos if listing is new
            if ($listing->photos()->count() == 0) {
                foreach ($images as $index => $image_url) {
                    Photo::create([
                        'listing_id' => $listing->id,
                        'photo_path' => 'listings/' . $listing->id . '/photo_' . ($index + 1) . '.jpg',
                        'photo_url' => $image_url,
                        'is_primary' => $index === 0,
                        'order' => $index + 1,
                    ]);
                }
            }
        }
    }
}
