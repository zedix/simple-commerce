<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Http\Controllers\Cp\Settings;

use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;

class OrderStatusControllerTest extends TestCase
{
    /** @test */
    public function can_get_index()
    {
        $this
            ->actAsSuper()
            ->get(cp_route('settings.order-statuses.index'))
            ->assertOk()
            ->assertSee('Order Statuses');
    }
}
