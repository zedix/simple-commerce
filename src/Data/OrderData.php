<?php

namespace DoubleThreeDigital\SimpleCommerce\Data;

use DoubleThreeDigital\SimpleCommerce\Facades\Currency;
use DoubleThreeDigital\SimpleCommerce\Models\LineItem;

class OrderData extends Data
{
    public function data(array $data, $original)
    {
        $data['items_count'] = 0;
        $data['line_items'] = $original->lineItems
            ->map(function (LineItem $lineItem) use (&$data) {
                $data['items_count'] += $lineItem->quantity;

                return $lineItem->templatePrep();
            })->toArray();

        $data['customer'] = $original->customer_id != null ? $original->customer->toArray() : null;
        $data['billing_address'] = $original->billing_address_id != null ? $original->billingAddress->toArray() : null;
        $data['shipping_address'] = $original->shipping_address_id != null ? $original->shippingAddress->toArray() : null;
        $data['order_status'] = $original->orderStatus->toArray();
        $data['item_total'] = Currency::parse($original->item_total);
        $data['shipping_total'] = Currency::parse($original->shipping_total);
        $data['tax_total'] = Currency::parse($original->tax_total);
        $data['coupon_total'] = Currency::parse($original->coupon_total);
        $data['total'] = Currency::parse($original->total);

        return $data;
    }
}
