<?php

namespace App\Http\Resources\Wallet;

use App\Constants\RouteNames;
use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends BaseJsonResource
{
    protected function resourceArray($request)
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'balance' => $this->balance,
        ];

        return $data;
    }

    protected function extendForHttp(array $data, $request)
    {
        $routeName = $request->route()->getName();

        switch ($routeName) 
        {
            //case RouteNames::EXAMPLE:
            //    $data['foo'] = 'bar';
            //    break;
        }

        return $data;
    }
}
