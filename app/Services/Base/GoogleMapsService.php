<?php

namespace App\Services\Base;

use App\Models\CustomClearenceCompany;
use App\Models\Driver;
use App\Models\DriverCompany;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    public function getETA($originLat, $originLng, $destLat, $destLng)
    {
        $key = config('services.google.maps_key');
        
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $key,
            'X-Goog-FieldMask' => 'routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline',
        ])->post('https://routes.googleapis.com/directions/v2:computeRoutes', [
            'origin' => [
                'location' => [
                    'latLng' => [
                        'latitude' => $originLat,
                        'longitude' => $originLng,
                    ],
                ],
            ],
            'destination' => [
                'location' => [
                    'latLng' => [
                        'latitude' => $destLat,
                        'longitude' => $destLng,
                    ],
                ],
            ],
            'travelMode' => 'DRIVE',
            'routingPreference' => 'TRAFFIC_AWARE',
        ]);

        if ($response->failed()) {
            // return null;

            return [
                'distance_meters' => 10000,
                'duration_seconds' => 3600,
                'polyline'         => null,
            ];
        }

        $data = $response->json();
        // dd($data);

        return [
            'distance_meters' => $data['routes'][0]['distanceMeters'] ?? null,
            'duration_seconds' => isset($data['routes'][0]['duration'])
                ? (int) rtrim($data['routes'][0]['duration'], 's')
                : null,
            'polyline' => $data['routes'][0]['polyline']['encodedPolyline'] ?? null,
        ];
    }
    // public function getCompanyDriversInRange($src_lat , $src_lng)
    // {
    //     $radius = order_range(); // km

    //     $drivers = Driver::
    //     orderBy('is_preffered' , 'desc')
    //     ->active()
    //     ->notIndividual()
    //     ->online()
    //     // ->driverModeOn()
    //     ->companyDriversModeOn()
    //     // whereNotNull('current_lat')
    //     // ->whereNotNull('current_lng')
    //     // ->select(columns: 'drivers.*')
    //     //     ->selectRaw("
    //     //         (6371 * acos(
    //     //             cos(radians(?)) *
    //     //             cos(radians(current_lat)) *
    //     //             cos(radians(current_lng) - radians(?)) +
    //     //             sin(radians(?)) *
    //     //             sin(radians(current_lat))
    //     //         )) AS distance
    //     //     ", [$src_lat, $src_lng, $src_lat])
    //     //     ->having('distance', '<=', $radius)
    //     //     ->orderBy('distance')
    //     ->get();

    //     return $drivers;
    // }

}
