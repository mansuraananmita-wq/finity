<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            ShippingZoneSeeder::class,
        ]);

        Coupon::updateOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percentage',
                'value' => 10,
                'min_order_amount' => 500,
                'max_uses' => 100,
                'used_count' => 0,
                'expires_at' => now()->addYear(),
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'FLAT100'],
            [
                'type' => 'fixed',
                'value' => 100,
                'min_order_amount' => 1000,
                'max_uses' => null,
                'used_count' => 0,
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@finity.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '01700000000',
                'preferred_language' => 'bn',
                'email_verified_at' => now(),
            ]
        );
    }
}
