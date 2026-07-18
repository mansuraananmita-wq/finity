<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * Production seed: categories + shipping zones + admin only.
     * Products come from the real 40-fish catalog seeder after catalog is finalized.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ShippingZoneSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@finity.test')],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'change-me-on-deploy')),
                'role' => 'admin',
                'phone' => env('ADMIN_PHONE', '01700000000'),
                'preferred_language' => 'bn',
                'email_verified_at' => now(),
            ]
        );
    }
}
