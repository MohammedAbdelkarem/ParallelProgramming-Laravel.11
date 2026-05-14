<?php 

use App\Services\System\SystemSettingService;

if (!function_exists('SP_TO_USD')) {
    function SP_TO_USD()
    {
        return (new SystemSettingService)->index()[0]["value"];
    }
}

if (!function_exists('banner_live_time')) {
    function banner_live_time()
    {
        return (new SystemSettingService)->index()[1]["value"];
    }
}

if (!function_exists('reel_live_time')) {
    function reel_live_time()
    {
        return (new SystemSettingService)->index()[2]["value"];
    }
}

if (!function_exists('step_reward_value')) {
    function step_reward_value()
    {
        return (new SystemSettingService)->index()[3]["value"];
    }
}

if (!function_exists('steps_daily_goal')) {
    function steps_daily_goal()
    {
        return (new SystemSettingService)->index()[4]["value"];
    }
}

if (!function_exists('stpes_minimum_balance_to_get')) {
    function stpes_minimum_balance_to_get()
    {
        return (new SystemSettingService)->index()[5]["value"];
    }
}