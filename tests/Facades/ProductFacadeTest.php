<?php

namespace Damcclean\Commerce\Tests\Facades;

use Damcclean\Commerce\Facades\Product;
use Damcclean\Commerce\Tests\TestCase;

class ProductFacadeTest extends TestCase
{
    /**
     * @test
     * We just want to make sure that we get through
     * to the FileCouponRepository.
     */
    public function facade_can_get_create_rules()
    {
        $rules = Product::createRules('coupons');

        $this->assertIsArray($rules);
    }
}