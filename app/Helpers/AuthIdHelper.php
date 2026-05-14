<?php

use App\Models\CustomClearenceCompany;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\DriverCompany;

if (!function_exists('auth_customer_id')) {
    function auth_customer_id()
    {
        $user_id = auth()->id();

        return Customer::where('user_id', $user_id)->first()->id;
    }
}

if (!function_exists('auth_driver_company_id')) {
    function auth_driver_company_id()
    {
        $user_id = auth()->id();

        return DriverCompany::where('user_id', $user_id)->first()->id;
    }
}

if (!function_exists('auth_custom_clearence_company_id')) {
    function auth_custom_clearence_company_id()
    {
        $user_id = auth()->id();

        return CustomClearenceCompany::where('user_id', $user_id)->first()->id;
    }
}

if (!function_exists('auth_driver_id')) {
    function auth_driver_id()
    {
        $user_id = auth()->id();

        return Driver::where('user_id', $user_id)->first()->id;
    }
}