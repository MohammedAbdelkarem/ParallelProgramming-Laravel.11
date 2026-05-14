<?php

namespace App\Http\Controllers\Administration;

use App\Constants\ApiMessages;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Services\Administration\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    )
    {
    }

    public function get(Request $request)
    {
        return success(
            $this->orderService->get($request->all()),
            ApiMessages::MSG_SUCCESS,
            OrderResource::class,
            $request->has('per_page')
        );
    }

    public function show($id)
    {
        return success(
            $this->orderService->show($id),
            ApiMessages::MSG_SUCCESS,
            OrderResource::class
        );
     }
}
