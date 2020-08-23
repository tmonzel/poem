<?php

namespace Modules\Order;

use Poem\Model\Document;

class Module extends \Poem\Module 
{
    /**
     * Uses orders model
     * 
     * @static
     * @var string
     */
    static $type = 'orders';

    /**
     * Prepares the orders model
     * 
     * @static
     * @param Model $orders
     */
    static function prepareModel(Model $orders)
    {
        $orders->addEventListener('document.updated', function(Document $order) {
            $originalState = $order->wasOriginally('state');

            if($originalState === 'cart' && $order->state === 'ordered') {
                // React on state changes to ordered.. 
                // Send email to owner
                
            }
        });
    }
}
