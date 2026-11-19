<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run()
    {
        // Let's get a client to be the buyer of the reserved/sold houses
        $client = User::where('role', 'client')->first();
        
        // If no client exists (Pair 1 hasn't seeded yet), create a fake one
        if (!$client) {
            $client = User::create([
                'name' => 'Demo Client',
                'email' => 'demo@client.com',
                'password' => bcrypt('password'),
                'role' => 'client',
                'phone' => '0700000000'
            ]);
        }

        // Create 8 Available houses
        Property::create(['title' => 'Modern 3 Bedroom', 'location' => 'Kilimani', 'bedrooms' => 3, 'size' => '1500 sqft', 'price' => 15000000, 'deposit_amount' => 1500000, 'status' => Property::AVAILABLE]);
        Property::create(['title' => 'Luxury Villa', 'location' => 'Karen', 'bedrooms' => 5, 'size' => '4000 sqft', 'price' => 45000000, 'deposit_amount' => 4500000, 'status' => Property::AVAILABLE]);
        Property::create(['title' => 'Cozy Apartment', 'location' => 'Westlands', 'bedrooms' => 2, 'size' => '800 sqft', 'price' => 8000000, 'deposit_amount' => 800000, 'status' => Property::AVAILABLE]);
        Property::create(['title' => 'Family Home', 'location' => 'Lavington', 'bedrooms' => 4, 'size' => '2500 sqft', 'price' => 25000000, 'deposit_amount' => 2500000, 'status' => Property::AVAILABLE]);
        Property::create(['title' => 'Bungalow', 'location' => 'Runda', 'bedrooms' => 3, 'size' => '2000 sqft', 'price' => 18000000, 'deposit_amount' => 1800000, 'status' => Property::AVAILABLE]);
        Property::create(['title' => 'Penthouse', 'location' => 'Kileleshwa', 'bedrooms' => 4, 'size' => '3000 sqft', 'price' => 35000000, 'deposit_amount' => 3500000, 'status' => Property::AVAILABLE]);
        Property::create(['title' => 'Studio Apartment', 'location' => 'Kileleshwa', 'bedrooms' => 1, 'size' => '500 sqft', 'price' => 4000000, 'deposit_amount' => 400000, 'status' => Property::AVAILABLE]);
        Property::create(['title' => 'Townhouse', 'location' => 'Donholm', 'bedrooms' => 3, 'size' => '1800 sqft', 'price' => 12000000, 'deposit_amount' => 1200000, 'status' => Property::AVAILABLE]);

        // Create 1 Reserved house (Using your helper!)
        $reservedHouse = Property::create(['title' => 'Reserved Villa', 'location' => 'Muthaiga', 'bedrooms' => 4, 'size' => '3500 sqft', 'price' => 40000000, 'deposit_amount' => 4000000]);
        $reservedHouse->markReserved($client);

        // Create 1 Sold house (Using your helper!)
        $soldHouse = Property::create(['title' => 'Sold Family Home', 'location' => 'Runda', 'bedrooms' => 5, 'size' => '5000 sqft', 'price' => 60000000, 'deposit_amount' => 6000000]);
        $soldHouse->markReserved($client); // Must reserve before selling
        $soldHouse->markSold();
    }
}