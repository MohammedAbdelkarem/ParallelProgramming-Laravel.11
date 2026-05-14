<?php

use App\Models\Driver;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('channel_for_everyone', function ($user) {
    return true;
});


// Broadcast::channel('driver.{driverId}.live', function ($user, $driverId) {
//     // $driver = Driver::where('id', $driverId)->where('user_id', $user->id)->first();

//     // return (bool) $driver;

//     return true;
// });

// routes/channels.php
Broadcast::channel('driver.{driverId}.live', function ($user, $driverId) {
    Log::info('Auth attempt for driver ID: ' . $driverId);
    Log::info('Current User: ' . json_encode($user));

    try {
        return true;
    } catch (\Exception $e) {
        Log::error('Broadcasting Auth Failed: ' . $e->getMessage());
        return false;
    }
});

Broadcast::channel('driver-company.{driverCompanyId}.live', function ($user, $driverCompanyId) {
    Log::info('Auth attempt for driver company ID: ' . $driverCompanyId);
    Log::info('Current User: ' . json_encode($user));

    try {
        return true;
    } catch (\Exception $e) {
        Log::error('Broadcasting Auth Failed: ' . $e->getMessage());
        return false;
    }
});

Broadcast::channel('custom-clearence-company.{customClearenceCompanyId}.live', function ($user, $customClearenceCompanyId) {
    Log::info('Auth attempt for driver company ID: ' . $customClearenceCompanyId);
    Log::info('Current User: ' . json_encode($user));

    try {
        return true;
    } catch (\Exception $e) {
        Log::error('Broadcasting Auth Failed: ' . $e->getMessage());
        return false;
    }
});

Broadcast::channel('customer.{customerId}.live', function ($user, $customerId) {
    Log::info('Auth attempt for customer ID: ' . $customerId);
    Log::info('Current User: ' . json_encode($user));

    try {
        return true;
    } catch (\Exception $e) {
        Log::error('Broadcasting Auth Failed: ' . $e->getMessage());
        return false;
    }
});

Broadcast::channel('trip.{trip_id}.live', function ($user, $trip_id) {
    // $driver = Driver::where('id', $driverId)->where('user_id', $user->id)->first();

    // return (bool) $driver;

    return true;
});
