<?php

namespace App\Services;

use App\Models\Product;

/**
 * Class ProductService.
 */
class ProductService
{
    public function index($data)
    {
        return getOrPaginate(
            Product::query()->orderByDesc("created_at"),
            $data
        );
    }

    public function store($data)
    {
        return Product::create($data);
    }

    public function update($id , $data)
    {
        $product = Product::findByIdOrFail($id);

        $product->update($data);

        return $product;
    }
}
