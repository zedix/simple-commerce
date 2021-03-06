<?php

namespace DoubleThreeDigital\SimpleCommerce\Http\Controllers\Cp;

use DoubleThreeDigital\SimpleCommerce\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Statamic\Contracts\Auth\User as UserContract;
use Statamic\Http\Controllers\CP\CpController;

class CustomerOrderController extends CpController
{
    public function __invoke(Request $request): Collection
    {
        $this->authorize('index', UserContract::class);

        $customerModel = config('simple-commerce.customers.model');
        $customerModel = new $customerModel();

        return Order::with('orderStatus')
            ->where('customer_id', $customerModel::where('email', $request->email)->first()->id)
            ->orderByDesc('created_at')
            ->get();
    }
}
