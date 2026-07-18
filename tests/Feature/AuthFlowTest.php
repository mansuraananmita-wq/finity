<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Notification::fake();
        config(['mail.default' => 'log']);
    }

    public function test_user_can_register_then_logout_then_login(): void
    {
        $email = 'newbuyer@example.com';
        $password = 'Password1!';

        $this->post('/register', [
            'name' => 'New Buyer',
            'email' => $email,
            'phone' => '01712345678',
            'password' => $password,
            'password_confirmation' => $password,
            'preferred_language' => 'en',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => $email, 'role' => 'customer']);
        $this->assertNotNull(User::where('email', $email)->first()->email_verified_at);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();

        $this->post('/login', [
            'email' => $email,
            'password' => $password,
        ])->assertRedirect('/');

        $this->assertAuthenticated();
    }

    public function test_login_page_loads_and_rejects_bad_password(): void
    {
        $user = User::factory()->create([
            'password' => 'Password1!',
            'email_verified_at' => now(),
        ]);

        $this->get('/login')->assertOk();

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_authenticated_user_visiting_login_goes_home_not_missing_route(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect('/');
    }

    public function test_guest_cart_survives_into_checkout_after_login(): void
    {
        $product = Product::first();

        $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertOk();

        $user = User::factory()->create([
            'password' => 'Password1!',
            'email_verified_at' => now(),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password1!',
        ])->assertRedirect('/');

        $this->get('/checkout')->assertOk();
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }
}
