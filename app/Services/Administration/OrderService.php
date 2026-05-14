<?php

namespace App\Services\Administration;

use App\Models\Order;

/**
 * Class OrderService.
 */
class OrderService
{
    public function get($data)
    {
        return getOrPaginate(
            Order::with([
                'customer' , 'offers'
            ])->filter($data)
            ,$data
        );
    }

    public function show($id)
    {
        return Order::with([
            'customer' , 'offers', 'trip', 'clearenceTransaction.additionalDocs', 'clearenceOrderAdditionalDocs' , 
                'deliveringWindows','settlement'
        ])->findOrFail($id);
    }
}
