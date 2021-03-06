<?php

namespace DoubleThreeDigital\SimpleCommerce\Models;

use DoubleThreeDigital\SimpleCommerce\Data\VariantData;
use DoubleThreeDigital\SimpleCommerce\Events\VariantUpdated;
use DoubleThreeDigital\SimpleCommerce\Models\Traits\HasAttributes;
use DoubleThreeDigital\SimpleCommerce\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasAttributes, HasUuid;

    protected $fillable = [
        'uuid', 'name', 'sku', 'description', 'images', 'weight', 'price', 'stock', 'unlimited_stock', 'max_quantity', 'product_id',
    ];

    protected $casts = [
        'images' => 'json',
    ];

    protected $appends = [
        'outOfStock',
    ];

    protected $dispatchesEvents = [
        'updated' => VariantUpdated::class,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function lineItems()
    {
        return $this->hasMany(LineItem::class);
    }

    public function getOutOfStockAttribute()
    {
        if ($this->unlimited_stock) {
            return false;
        }

        if ($this->stock <= 0) {
            return true;
        }

        return false;
    }

    public function delete()
    {
        parent::delete();

        if ($this->attributes()->count() > 0) {
            $this->attributes()->delete();
        }
    }

    public function templatePrep()
    {
        return (new VariantData)->data($this->toArray(), $this);
    }
}
