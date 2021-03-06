<?php

namespace DoubleThreeDigital\SimpleCommerce\Models;

use DoubleThreeDigital\SimpleCommerce\Data\LineItemData;
use DoubleThreeDigital\SimpleCommerce\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class LineItem extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'sku', 'description', 'note', 'price', 'total', 'weight', 'quantity', 'order_id', 'variant_id', 'tax_rate_id', 'shipping_rate_id', 'coupon_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function shippingRate()
    {
        return $this->belongsTo(ShippingRate::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function recalculate()
    {
        return $this->order->recalculate();
    }

    public function templatePrep()
    {
        return (new LineItemData)->data($this->toArray(), $this);
    }
}
