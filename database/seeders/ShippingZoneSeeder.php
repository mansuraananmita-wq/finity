<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'zone_name' => 'Dhaka Metro',
                'districts' => ['Dhaka', 'Gazipur', 'Narayanganj', 'Narsingdi', 'Manikganj', 'Tangail', 'Munshiganj'],
                'base_cost' => 80,
                'per_kg_cost' => 25,
                'estimated_days' => '1-2 days',
            ],
            [
                'zone_name' => 'Chattogram',
                'districts' => ['Chattogram', 'Cox\'s Bazar', 'Cumilla', 'Feni', 'Noakhali', 'Chandpur', 'Lakshmipur'],
                'base_cost' => 120,
                'per_kg_cost' => 35,
                'estimated_days' => '2-3 days',
            ],
            [
                'zone_name' => 'Sylhet',
                'districts' => ['Sylhet', 'Moulvibazar', 'Habiganj', 'Sunamganj'],
                'base_cost' => 130,
                'per_kg_cost' => 40,
                'estimated_days' => '2-4 days',
            ],
            [
                'zone_name' => 'Rajshahi',
                'districts' => ['Rajshahi', 'Bogura', 'Pabna', 'Sirajganj', 'Naogaon', 'Natore', 'Joypurhat', 'Chapainawabganj'],
                'base_cost' => 140,
                'per_kg_cost' => 40,
                'estimated_days' => '3-4 days',
            ],
            [
                'zone_name' => 'Khulna',
                'districts' => ['Khulna', 'Jessore', 'Satkhira', 'Bagerhat', 'Magura', 'Jhenaidah', 'Narail', 'Kushtia', 'Chuadanga', 'Meherpur'],
                'base_cost' => 150,
                'per_kg_cost' => 45,
                'estimated_days' => '3-5 days',
            ],
            [
                'zone_name' => 'Rest of Bangladesh',
                'districts' => ['Barishal', 'Patuakhali', 'Bhola', 'Pirojpur', 'Barguna', 'Jhalokati', 'Rangpur', 'Dinajpur', 'Kurigram', 'Lalmonirhat', 'Nilphamari', 'Gaibandha', 'Thakurgaon', 'Panchagarh', 'Mymensingh', 'Jamalpur', 'Sherpur', 'Netrokona'],
                'base_cost' => 180,
                'per_kg_cost' => 50,
                'estimated_days' => '4-6 days',
            ],
        ];

        foreach ($zones as $zone) {
            ShippingZone::updateOrCreate(
                ['zone_name' => $zone['zone_name']],
                array_merge($zone, ['is_active' => true])
            );
        }
    }
}
