<?php

namespace DoubleThreeDigital\SimpleCommerce\Models;

use DoubleThreeDigital\SimpleCommerce\Events\ProductCategoryUpdated;
use DoubleThreeDigital\SimpleCommerce\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Statamic\Facades\Blueprint;

class ProductCategory extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'title', 'slug',
    ];

    protected $dispatchesEvents = [
        'updated' => ProductCategoryUpdated::class,
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function createUrl()
    {
        return cp_route('product-categories.create');
    }

    public function showUrl()
    {
        return cp_route('product-categories.show', ['category' => $this->uuid]);
    }

    public function editUrl()
    {
        return cp_route('product-categories.edit', ['category' => $this->uuid]);
    }

    public function updateUrl()
    {
        return cp_route('product-categories.update', ['category' => $this->uuid]);
    }

    public function deleteUrl()
    {
        return cp_route('product-categories.destroy', ['category' => $this->uuid]);
    }

    public function blueprint()
    {
        return Blueprint::setDirectory(__DIR__.'/../../resources/blueprints')
            ->find('product_category');
    }
}
