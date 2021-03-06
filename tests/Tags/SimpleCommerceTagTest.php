<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Tags;

use App\User;
use DoubleThreeDigital\SimpleCommerce\Gateways\DummyGateway;
use DoubleThreeDigital\SimpleCommerce\Models\Country;
use DoubleThreeDigital\SimpleCommerce\Models\Currency;
use DoubleThreeDigital\SimpleCommerce\Models\Order;
use DoubleThreeDigital\SimpleCommerce\Models\Product;
use DoubleThreeDigital\SimpleCommerce\Models\ProductCategory;
use DoubleThreeDigital\SimpleCommerce\Models\State;
use DoubleThreeDigital\SimpleCommerce\Models\Variant;
use DoubleThreeDigital\SimpleCommerce\Tags\SimpleCommerceTag;
use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Statamic\Facades\Antlers;

class SimpleCommerceTagTest extends TestCase
{
    public $tag;

    public function setUp() : void
    {
        parent::setUp();

        $this->currency = factory(Currency::class)->create();
        Config::set('simple-commerce.currency.iso', $this->currency->iso);

        $this->tag = (new SimpleCommerceTag())
            ->setParser(Antlers::parser())
            ->setContext([]);
    }

    /** @test */
    public function simple_commerce_tag_is_registered()
    {
        $this->assertTrue(isset(app()['statamic.tags']['simple-commerce']));
    }

    /** @test */
    public function simple_commerce_currency_code_tag()
    {
        $run = $this->tag->currencyCode();

        $this->assertSame($run, $this->currency->iso);
    }

    /** @test */
    public function simple_commerce_currency_symbol_tag()
    {
        $run = $this->tag->currencySymbol();

        $this->assertSame($run, $this->currency->symbol);
    }

    /** @test */
    public function simple_commerce_categories_tag()
    {
        $categories = factory(ProductCategory::class, 5)->create();

        $this->tag->setParameters([]);

        $run = $this->tag->categories();

        $this->assertIsArray($run);
    }

    /** @test */
    public function simple_commerce_categories_tag_count()
    {
        $categories = factory(ProductCategory::class, 5)->create();

        $this->tag->setParameters(['count' => true]);

        $count = $this->tag->categories();

        $this->assertIsNumeric($count);
    }

    /** @test */
    public function simple_commerce_products_tag()
    {
        $products = factory(Product::class, 2)->create();
        factory(Variant::class)->create(['product_id' => $products[0]['id']]);
        factory(Variant::class)->create(['product_id' => $products[1]['id']]);

        $this->tag->setParameters([]);

        $run = $this->tag->products();

        $this->assertIsArray($run);
    }

    /** @test */
    public function simple_commerce_products_tag_in_category()
    {
        $category = factory(ProductCategory::class)->create();
        $products = factory(Product::class, 2)->create([
            'product_category_id' => $category->id,
        ]);
        factory(Variant::class)->create(['product_id' => $products[0]['id']]);
        factory(Variant::class)->create(['product_id' => $products[1]['id']]);

        $this->tag->setParameters([
            'category' => $category->slug,
        ]);

        $run = $this->tag->products();

        $this->assertIsArray($run);
        $this->assertStringContainsString($products[1]['title'], json_encode($run));
    }

    /** @test */
    public function simple_commerce_products_tag_where()
    {
        $this->markTestIncomplete();

        $products = factory(Product::class, 2)->create();
        factory(Variant::class)->create(['product_id' => $products[0]['id']]);
        factory(Variant::class)->create(['product_id' => $products[1]['id']]);

        $this->tag->setParameters([
            'where' => 'slug:'.$products[0]['slug'],
        ]);

        $run = $this->tag->products();

        $this->assertIsArray($run);
        $this->assertStringContainsString($products[0]['title'], json_encode($run));
        $this->assertStringNotContainsString($products[1]['title'], json_encode($run));
    }

    /** @test */
    public function simple_commerce_products_tag_and_include_disabled()
    {
        $enabledProduct = factory(Product::class)->create([
            'is_enabled'    => true,
        ]);
        factory(Variant::class)->create(['product_id' => $enabledProduct->id]);

        $disabledProduct = factory(Product::class)->create([
            'is_enabled'    => false,
        ]);
        factory(Variant::class)->create(['product_id' => $disabledProduct->id]);

        $this->tag->setParameters([
            'include_disabled' => true,
        ]);

        $run = $this->tag->products();

        $this->assertIsArray($run);
        $this->assertStringContainsString($enabledProduct->title, json_encode($run));
        $this->assertStringContainsString($disabledProduct->title, json_encode($run));
    }

    /** @test */
    public function simple_commerce_products_tag_limit()
    {
        $this->markTestIncomplete();

        $products = factory(Product::class, 3)->create();
        factory(Variant::class)->create(['product_id' => $products[0]['id']]);
        factory(Variant::class)->create(['product_id' => $products[1]['id']]);
        factory(Variant::class)->create(['product_id' => $products[2]['id']]);

        $this->tag->setParameters([
            'limit' => '2',
        ]);

        $run = $this->tag->products();

        $this->assertIsArray($run);
        $this->assertStringContainsString($products[0]['title'], json_encode($run));
        $this->assertStringNotContainsString($products[2]['title'], json_encode($run));
    }

    /** @test */
    public function simple_commerce_products_tag_count()
    {
        $products = factory(Product::class, 2)->create();
        factory(Variant::class)->create(['product_id' => $products[0]['id']]);
        factory(Variant::class)->create(['product_id' => $products[1]['id']]);

        $this->tag->setParameters([
            'count' => true,
        ]);

        $run = $this->tag->products();

        $this->assertIsInt($run);
        $this->assertSame($run, 2);
    }

    /** @test */
    public function simple_commerce_products_tag_first()
    {
        $this->markTestIncomplete();

        $products = factory(Product::class, 2)->create();
        factory(Variant::class)->create(['product_id' => $products[0]['id']]);
        factory(Variant::class)->create(['product_id' => $products[1]['id']]);

        $this->tag->setParameters([
            'first' => 'true',
        ]);

        $run = $this->tag->products();

        $this->assertIsArray($run);
        $this->assertStringContainsString($products[0]['title'], json_encode($run));
        $this->assertStringNotContainsString($products[1]['title'], json_encode($run));
    }

    /** @test */
    public function simple_commerce_product_tag()
    {
        $product = factory(Product::class)->create();
        $variants = factory(Variant::class, 2)->create([
            'product_id' => $product->id,
        ]);

        $this->tag->setParameters([
            'slug' => $product->slug,
        ]);

        $run = $this->tag->product();

        $this->assertIsArray($run);
        $this->assertStringContainsString($product->title, json_encode($run));
        $this->assertStringContainsString($variants[0]['name'], json_encode($run));
        $this->assertStringContainsString($variants[1]['name'], json_encode($run));
    }

    /** @test */
    public function simple_commerce_countries_tag()
    {
        $countries = factory(Country::class, 15)->create();

        $this->tag->setParameters([]);

        $run = $this->tag->countries();

        $this->assertIsArray($run);
        $this->assertStringContainsString($countries[2]['name'], json_encode($run));
        $this->assertStringContainsString($countries[5]['name'], json_encode($run));
        $this->assertStringContainsString($countries[10]['name'], json_encode($run));
        $this->assertStringContainsString($countries[13]['name'], json_encode($run));
    }

    /** @test */
    public function simple_commerce_states_tag()
    {
        $states = factory(State::class, 5)->create();

        $this->tag->setParameters([]);

        $run = $this->tag->states();

        $this->assertIsArray($run);
        $this->assertStringContainsString($states[0]['name'], json_encode($run));
        $this->assertStringContainsString($states[1]['name'], json_encode($run));
        $this->assertStringContainsString($states[2]['name'], json_encode($run));
        $this->assertStringContainsString($states[3]['name'], json_encode($run));
        $this->assertStringContainsString($states[4]['name'], json_encode($run));
    }

    /** @test */
    public function simple_commerce_currencies_tag()
    {
        $currencies = factory(Currency::class, 5)->create();

        $run = $this->tag->currencies();

        $this->assertIsArray($run);
        $this->assertStringContainsString($currencies[0]['name'], json_encode($run));
        $this->assertStringContainsString($currencies[1]['name'], json_encode($run));
        $this->assertStringContainsString($currencies[2]['name'], json_encode($run));
        $this->assertStringContainsString($currencies[3]['name'], json_encode($run));
        $this->assertStringContainsString($currencies[4]['name'], json_encode($run));
    }

    /** @test */
    public function simple_commerce_gateways_tag()
    {
        Config::set('simple-commerce.gateways', [
            DummyGateway::class,
        ]);

        $this->tag->setParameters([]);

        $run = $this->tag->gateways();

        $this->assertIsArray($run);
        $this->assertStringContainsString('Dummy', json_encode($run));
    }

    /** @test */
    public function simple_commerce_orders_tag()
    {
        $user = factory(User::class)->create();
        $orders = factory(Order::class, 2)->create([
            'customer_id' => $user->id,
        ]);

        Auth::loginUsingId($user->id);

        $this->tag->setParameters([]);

        $run = $this->tag->orders();

        $this->assertIsArray($run);
        $this->assertStringContainsString($orders[0]['id'], json_encode($run));
        $this->assertStringContainsString($orders[1]['id'], json_encode($run));
    }

    /** @test */
    public function simple_commerce_orders_tag_can_get_single_order()
    {
        $user = factory(User::class)->create();
        $order = factory(Order::class)->create([
            'customer_id' => $user->id,
        ]);

        Auth::loginUsingId($user->id);

        $this->tag->setParameters([
            'get' => $order->uuid,
        ]);

        $run = $this->tag->orders();

        $this->assertIsArray($run);
        $this->assertStringContainsString($order->id, json_encode($run));
    }

    /** @test */
    public function simple_commerce_orders_tag_returns_null_if_logged_out()
    {
        $this->tag->setParameters([]);

        $run = $this->tag->orders();

        $this->assertNull($run);
    }

    /** @test */
    public function simple_commerce_form_tag_with_for_param()
    {
        $this->tag->setParameters([
            'for' => 'checkout',
        ]);

        $this->tag->setContent('
            <input type="text" name="name" value="Duncan McClean">
            <input type="email" name="email" value="duncan@example.com">
            
            <button type="submit">Submit</button>
        ');

        $run = $this->tag->form();

        $this->assertIsString($run);
        $this->assertStringContainsString('checkout" ', $run);
        $this->assertStringContainsString('<input type="text" name="name" value="Duncan McClean">', $run);
        $this->assertStringContainsString('<input type="email" name="email" value="duncan@example.com">', $run);
        $this->assertStringContainsString('name="_token"', $run);
    }

    /** @test */
    public function simple_commerce_form_tag_with_in_param()
    {
        $this->tag->setParameters([
            'in' => 'checkout',
        ]);

        $this->tag->setContent('
            <input type="text" name="name" value="Duncan McClean">
            <input type="email" name="email" value="duncan@example.com">
            
            <button type="submit">Submit</button>
        ');

        $run = $this->tag->form();

        $this->assertIsString($run);
        $this->assertStringContainsString('checkout" ', $run);
        $this->assertStringContainsString('<input type="text" name="name" value="Duncan McClean">', $run);
        $this->assertStringContainsString('<input type="email" name="email" value="duncan@example.com">', $run);
        $this->assertStringContainsString('name="_token"', $run);
    }

    /** @test */
    public function simple_commerce_errors_tag()
    {
        //
    }

    /** @test */
    public function simple_commerce_success_tag()
    {
        $this->session([
            'form.checkout.success' => 'Your payment is being processed.',
        ]);

        $this->tag->setParameters([
            'for' => 'checkout',
        ]);

        $run = $this->tag->success();

        $this->assertTrue($run);
    }
}
