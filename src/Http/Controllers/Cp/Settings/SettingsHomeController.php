<?php

namespace DoubleThreeDigital\SimpleCommerce\Http\Controllers\Cp\Settings;

use DoubleThreeDigital\SimpleCommerce\Http\Requests\SettingsUpdateRequest;
use Statamic\CP\Breadcrumbs;
use Statamic\Http\Controllers\CP\CpController;

class SettingsHomeController extends CpController
{
    public function index()
    {
        return view('simple-commerce::cp.settings.index', [
            'settings' => [
                [
                    'title' => 'Order Statuses',
                    'description' => 'Order statuses help you to organise the status of orders in your store.',
                    'url' => cp_route('settings.order-statuses.index'),
                    'icon' => 'select',
                ],
                [
                    'title' => 'Shipping Zones',
                    'description' => 'Shipping Zones gives you the ability to charge customers a fixed price for shipping to them.',
                    'url' => cp_route('settings.shipping-zones.index'),
                    'icon' => 'pin',
                ],
                [
                    'title' => 'Tax Rates',
                    'description' => 'Manage the tax rates that are added to customer\'s orders when checking out.',
                    'url' => cp_route('settings.tax-rates.index'),
                    'icon' => 'earth',
                ],
                [
                    'title' => 'More Settings',
                    'description' => 'Simple Commerce lets you configure more settings in the config/simple-commerce.php file.',
                    'url' => '#',
                    'icon' => 'settings-horizontal',
                ],
            ],
        ]);
    }
}
