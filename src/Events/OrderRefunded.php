<?php

namespace DoubleThreeDigital\SimpleCommerce\Events;

use DoubleThreeDigital\SimpleCommerce\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

class OrderRefunded
{
    use Dispatchable, InteractsWithSockets;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
