<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingZone;
use App\Models\User;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class Part3FeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Notification::fake();
    }

    public function test_guest_cart_persists_across_requests(): void
    {
        $product = Product::first();

        $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->get(route('cart.view'))->assertOk();

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_guest_cart_merges_on_login(): void
    {
        $product = Product::first();
        $user = User::factory()->create();

        $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ])->assertOk();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $userCart = $user->carts()->first();
        $this->assertNotNull($userCart);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userCart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_checkout_creates_order_with_correct_totals(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::first();
        $product->update(['price' => 500, 'weight_grams' => 500]);

        $this->actingAs($user);
        app(CartService::class)->addItem($product->id, 2);

        $cart = app(CartService::class)->getCartWithItems();
        $subtotal = app(CartService::class)->calculateSubtotal($cart);
        $zone = app(ShippingService::class)->findZoneByDistrict('Dhaka');
        $weight = app(CartService::class)->calculateTotalWeightGrams($cart);
        $shipping = app(ShippingService::class)->calculateCost($zone, $weight);

        app(CouponService::class)->apply('WELCOME10', $subtotal);
        $discount = app(CouponService::class)->getDiscountAmount($subtotal);
        $expectedTotal = round($subtotal + $shipping - $discount, 2);

        $this->post(route('checkout.store'), [
            'shipping_address' => 'Test address',
            'shipping_district' => 'Dhaka',
            'phone' => '01712345678',
            'payment_method' => 'cod',
        ])->assertRedirect();

        $order = Order::where('user_id', $user->id)->latest()->first();

        $this->assertEquals($subtotal, (float) $order->subtotal);
        $this->assertEquals($shipping, (float) $order->shipping_cost);
        $this->assertEquals($discount, (float) $order->discount_amount);
        $this->assertEquals($expectedTotal, (float) $order->total_amount);
    }

    public function test_coupon_rejects_expired_code(): void
    {
        Coupon::create([
            'code' => 'EXPIRED',
            'type' => 'fixed',
            'value' => 50,
            'expires_at' => now()->subDay(),
            'is_active' => true,
        ]);

        $this->expectException(\RuntimeException::class);
        app(CouponService::class)->apply('EXPIRED', 1000);
    }

    public function test_coupon_rejects_over_limit_code(): void
    {
        Coupon::create([
            'code' => 'MAXED',
            'type' => 'fixed',
            'value' => 50,
            'max_uses' => 5,
            'used_count' => 5,
            'is_active' => true,
        ]);

        $this->expectException(\RuntimeException::class);
        app(CouponService::class)->apply('MAXED', 1000);
    }

    public function test_coupon_rejects_under_minimum_subtotal(): void
    {
        Coupon::create([
            'code' => 'MIN500',
            'type' => 'fixed',
            'value' => 50,
            'min_order_amount' => 500,
            'is_active' => true,
        ]);

        $this->expectException(\RuntimeException::class);
        app(CouponService::class)->apply('MIN500', 100);
    }

    public function test_user_with_delivered_order_can_submit_review(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::first();
        $zone = ShippingZone::first();

        $order = Order::create([
            'user_id' => $user->id,
            'shipping_zone_id' => $zone->id,
            'subtotal' => 500,
            'shipping_cost' => 80,
            'discount_amount' => 0,
            'total_amount' => 580,
            'status' => 'delivered',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'shipping_address' => 'Test',
            'shipping_district' => 'Dhaka',
            'phone' => '01712345678',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price_at_purchase' => $product->price,
        ]);

        $this->actingAs($user)
            ->postJson(route('reviews.store', $product), [
                'rating' => 5,
                'comment' => 'Excellent fish!',
            ])
            ->assertOk();

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'is_approved' => false,
        ]);
    }

    public function test_admin_routes_reject_non_admin_users(): void
    {
        $customer = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('home'));

        $this->actingAs($customer)
            ->get(route('admin.products.index'))
            ->assertRedirect(route('home'));
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::where('email', 'admin@finity.test')->first();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_can_create_product_with_image_upload(): void
    {
        $admin = User::where('email', 'admin@finity.test')->first();
        $category = \App\Models\Category::first();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name_en' => 'Test Guppy',
            'name_bn' => 'টেস্ট গাপ্পি',
            'scientific_name' => 'Poecilia reticulata',
            'slug' => 'test-guppy',
            'price' => 150,
            'stock_qty' => 20,
            'description_en' => 'A test fish.',
            'description_bn' => 'একটি টেস্ট মাছ।',
            'care_level' => 'Easy',
            'tank_size_liters' => 40,
            'weight_grams' => 200,
            'image' => \Illuminate\Http\UploadedFile::fake()->image('guppy.jpg', 2000, 1500),
            'is_featured' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('slug', 'test-guppy')->first();
        $this->assertNotNull($product);
        $this->assertStringStartsWith('/storage/products/', $product->image_path);

        // Uploaded image must be resized down to max 1200px wide
        $saved = storage_path('app/public/'.substr($product->image_path, strlen('/storage/')));
        $this->assertFileExists($saved);
        [$width] = getimagesize($saved);
        $this->assertLessThanOrEqual(1200, $width);

        @unlink($saved);
    }
}
