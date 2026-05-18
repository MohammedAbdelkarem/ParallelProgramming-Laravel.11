<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

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

    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {

            // Lock the row for update
            $product = Product::where('id', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $product->update($data);

            return $product;
        });
    }

}
