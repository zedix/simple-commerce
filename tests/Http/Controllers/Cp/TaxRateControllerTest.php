<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Http\Controllers\Cp;

use DoubleThreeDigital\SimpleCommerce\Models\Country;
use DoubleThreeDigital\SimpleCommerce\Models\State;
use DoubleThreeDigital\SimpleCommerce\Models\TaxRate;
use DoubleThreeDigital\SimpleCommerce\Tests\TestCase;

class TaxRateControllerTest extends TestCase
{
    /** @test */
    public function can_index_tax_rates()
    {
        $rates = factory(TaxRate::class, 5)->create();

        $this
            ->actAsSuper()
            ->get(cp_route('tax-rates.index'))
            ->assertOk();
    }

    /** @test */
    public function can_store_tax_rate()
    {
        $this
            ->actAsSuper()
            ->post(cp_route('tax-rates.store'), [
                'name' => $this->faker->word,
                'country' => [factory(Country::class)->create()->id],
                'state' => [factory(State::class)->create()->id],
                'start_of_zip_code' => 'G72 A12',
                'rate' => '20',
            ])
            ->assertCreated();
    }

    /** @test */
    public function can_update_tax_rate()
    {
        $rate = factory(TaxRate::class)->create();

        $this
            ->actAsSuper()
            ->post(cp_route('tax-rates.update', ['rate' => $rate->uuid]), [
                'name' => $this->faker->word,
                'country' => [$rate->country_id],
                'state' => [$rate->state_id],
                'start_of_zip_code' => $rate->start_of_zip_code,
                'rate' => '20',
            ])
            ->assertOk();
    }

    /** @test */
    public function can_destroy_tax_rate()
    {
        $rate = factory(TaxRate::class)->create();

        $this
            ->actAsSuper()
            ->delete(cp_route('tax-rates.destroy', ['rate' => $rate->uuid]))
            ->assertRedirect();
    }
}
