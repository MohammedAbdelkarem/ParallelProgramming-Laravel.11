<?php

namespace App\Http\Controllers;

use App\Constants\ApiMessages;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request)
    {
        return success(
            $this->productService->index($request->all()),
            ApiMessages::MSG_SUCCESS,
            null,
            $request->has('per_page')
        );
    }

    public function store(CreateProductRequest $request)
    {
        return createdSuccess(
            $this->productService->store($request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }

    public function update($id, UpdateProductRequest $request)
    {
        return success(
            $this->productService->update($id, $request->validated()),
            ApiMessages::MSG_SUCCESS,
        );
    }
}
