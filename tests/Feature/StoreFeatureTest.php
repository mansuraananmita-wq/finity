<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_product_listing_page_loads(): void
    {
        $product = Product::first();
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee($product->name);
    }

    public function test_home_page_loads_in_english(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_guest_can_add_to_cart(): void
    {
        $product = Product::first();
        $response = $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_shipping_cost_calculation(): void
    {
        $product = Product::first();
        $cartService = app(CartService::class);
        $shippingService = app(ShippingService::class);

        $cartService->addItem($product->id, 2);
        $cart = $cartService->getCartWithItems();
        $zone = $shippingService->findZoneByDistrict('Dhaka');
        $weight = $cartService->calculateTotalWeightGrams($cart);
        $cost = $shippingService->calculateCost($zone, $weight);

        $expectedKg = (int) ceil($weight / 1000);
        $expected = round((float) $zone->base_cost + ($expectedKg * (float) $zone->per_kg_cost), 2);

        $this->assertEquals($expected, $cost);
    }

    public function test_coupon_apply_and_remove(): void
    {
        $product = Product::where('price', '>=', 100)->first();
        app(CartService::class)->addItem($product->id, 10);
        $subtotal = app(CartService::class)->calculateSubtotal(app(CartService::class)->getCartWithItems());

        $couponService = app(CouponService::class);
        $result = $couponService->apply('WELCOME10', $subtotal);

        $this->assertEquals('WELCOME10', $result['code']);
        $this->assertGreaterThan(0, $result['discount_amount']);

        $couponService->remove();
        $this->assertNull($couponService->getAppliedCoupon());
    }

    public function test_review_requires_purchase(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::first();

        $this->actingAs($user)
            ->postJson(route('reviews.store', $product), [
                'rating' => 5,
                'comment' => 'Great fish',
            ])
            ->assertForbidden();
    }

    public function test_cod_order_creation_decrements_stock(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::first();
        $initialStock = $product->stock_qty;

        $this->actingAs($user);
        app(CartService::class)->addItem($product->id, 1);

        $response = $this->post(route('checkout.store'), [
            'shipping_address' => 'House 1, Road 2',
            'shipping_district' => 'Dhaka',
            'phone' => '01712345678',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();
        $product->refresh();
        $this->assertEquals($initialStock - 1, $product->stock_qty);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'payment_method' => 'cod']);
    }

    public function test_out_of_stock_rejected_at_checkout(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $product = Product::first();
        $product->update(['stock_qty' => 2]);

        $this->actingAs($user);
        app(CartService::class)->addItem($product->id, 1);

        $product->update(['stock_qty' => 0]);

        $response = $this->from(route('checkout.index'))->post(route('checkout.store'), [
            'shipping_address' => 'House 1',
            'shipping_district' => 'Dhaka',
            'phone' => '01712345678',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHasErrors('checkout');
    }
}
