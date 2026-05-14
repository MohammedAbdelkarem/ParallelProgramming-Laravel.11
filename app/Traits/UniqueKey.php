<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UniqueKey
{
    protected function generateUniqeStringKey($model, string $columnName, ?string $pre = null, int $length = 32): string
    {
        do {
            $randKey = Str::random($length);
            $uniqueKey = $pre ? "$pre$randKey" : $randKey;
        } while ($model::where($columnName, $uniqueKey)->first());
        return $uniqueKey;
    }

    protected function generateUniqeNumericKey($model, string $columnName, ?string $pre = null, int $min = 110000, int $max = 990000): string
    {
        do {
            $randKey = random_int($min, $max);
            $uniqueKey = $pre ? "$pre$randKey" : $randKey;
        } while ($model::where($columnName, $uniqueKey)->first());
        return $uniqueKey;
    }
}
