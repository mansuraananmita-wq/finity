<?php

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAndReviewDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_approved_review_appears_on_product_page(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::first();

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Visible after approve',
            'is_approved' => false,
        ]);

        $this->actingAs(User::where('role', 'admin')->first())
            ->post(route('admin.reviews.approve', $review))
            ->assertRedirect();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Visible after approve')
            ->assertSee($user->name)
            ->assertDontSee('No reviews yet.');
    }

    public function test_user_can_view_and_update_profile(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'phone' => '01700000000',
            'address' => 'Old address',
        ]);

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('01700000000')
            ->assertSee('Recent orders')
            ->assertSee('Saved fish');

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Updated Name',
                'email' => $user->email,
                'phone' => '01811111111',
                'address' => 'Dhaka, Bangladesh',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'phone' => '01811111111',
            'address' => 'Dhaka, Bangladesh',
        ]);
    }
}
