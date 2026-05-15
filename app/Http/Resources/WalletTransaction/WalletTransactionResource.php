<?php

namespace App\Http\Resources\WalletTransaction;

use App\Constants\ModelPaths;
use App\Constants\RouteNames;
use App\Http\Resources\BaseJsonResource;
use App\Http\Resources\CustomClearenceCompany\CustomClearenceCompanyResource;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Resources\Driver\DriverResource;
use App\Http\Resources\DriverCompany\DriverCompanyResource;
use App\Models\CustomClearenceCompany;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\DriverCompany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletTransactionResource extends BaseJsonResource
{
    protected function resourceArray($request)
    {
        $data = [
            'id' => $this->id,
            // 'wallet_id' => $this->wallet_id,
            'type' => $this->type,
            'amount' => $this->amount,

            // Polymorphic reference (order, settlement, refund, dispute)
            'reference' => [
                'id' => $this->reference_id,
                // 'type' => $this->reference_type,
                'type_name' => $this->reference_type ? class_basename($this->reference_type) : null,
            ],


            // From (driver, company, clearance, customer)
            'from' => [
                'id' => $this->from_id,
                // 'type' => $this->from_type,
                'type_name' => ($this->from_id == 3 
                ? 'User' : 'Admin'),
                // 'name' => $from_name,
            ],

            // To (driver, company, clearance, customer)
            'to' => [
                'id' => $this->to_id,
                // 'type' => $this->to_type,
                'type_name' => ($this->to_id == 3 
                ? 'User' : 'Admin'),
                // 'name' => $to_name,
            ],

            // Descriptions
            'description' => $this->description,
            'notes' => $this->notes,

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
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
