<?php

namespace App\Http\Controllers;

use App\Constants\ApiMessages;
use App\Http\Requests\Order\AddToCartRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    public function get(Request $request)
    {
        return Success(
            $this->orderService->get($request->all()),
            ApiMessages::MSG_SUCCESS,
            null,
            $request->has('per_page')
        );
    }

    public function generateReport(Request $request)
    {
        return Success(
            $this->orderService->generate(),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function addToCart(AddToCartRequest $request)
    {
        return createdSuccess(
            $this->orderService->addToCart($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function removeFromCart($orderItemId)
    {
        $this->orderService->removeFromCart($orderItemId);
        return Success(
            null,
            ApiMessages::MSG_SUCCESS,
        );
    }


    public function changeOrderStatus(Request $request , $orderId)
    {
        return Success(
            $this->orderService->changeOrderStatus($orderId , $request->status),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function getReports(Request $request)
    {
        return Success(
            $this->orderService->getReports(),
            ApiMessages::MSG_SUCCESS,
            null,
            $request->has('per_page')
        );
    }
}
